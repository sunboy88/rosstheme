<?php

class Magestore_Affiliatepluspayment_Model_Cron
{
    /**
     * recurring payment for affilate, run every day at 3PM
     */
    public function recurringPayment() {
        // get store to pay by recurring Payment
        $stores = array();
        if (Mage::app()->isSingleStoreMode()) {
            $store = Mage::app()->getDefaultStoreView();
            if (Mage::getStoreConfig('affiliateplus_payment/recurring/enable', $store)) {
                $stores[$store->getId()] = $store;
            } else {
                return ;
            }
        } else {
            foreach (Mage::app()->getStores() as $store) {
                if (Mage::getStoreConfig('affiliateplus_payment/recurring/enable', $store)) {
                    $stores[$store->getId()] = $store;
                }
            }
        }
        if (!count($stores)) {
            return ;
        }
        // pay by recurring payment for available store
        $receivedIds = array();
        foreach ($stores as $store) {
            $collection = Mage::getResourceModel('affiliateplus/account_collection');
            
            // check payment method (enable for recurring) and registered account for that method
            $paymentConditions = array();
            if (Mage::getStoreConfig('affiliateplus_payment/paypal/use_for_recurring', $store)) {
                $paymentConditions[] = "(recurring_method = 'paypal' AND paypal_email IS NOT NULL AND paypal_email != '')";
            }
            if (Mage::getStoreConfig('affiliateplus_payment/moneybooker/use_for_recurring', $store)) {
                $paymentConditions[] = "(recurring_method = 'moneybooker' AND moneybooker_email IS NOT NULL AND moneybooker_email != '')";
            }
            if (count($paymentConditions)){
                $collection->getSelect()->where(implode(' OR ', $paymentConditions));
            } else {
                continue;
            }
            
            // filter verified gateway email
            $collection->getSelect()
                ->joinLeft(array('pa' => $collection->getTable('affiliateplus/payment_verify')),
                    "main_table.account_id = pa.account_id AND pa.payment_method = 'paypal' AND " .
                    "main_table.recurring_method = 'paypal' AND paypal_email = pa.field AND " .
                    "pa.verified = '1'",
                    array()
                )->joinLeft(array('mo' => $collection->getTable('affiliateplus/payment_verify')),
                    "main_table.account_id = mo.account_id AND mo.payment_method = 'moneybooker' AND " .
                    "main_table.recurring_method = 'moneybooker' AND moneybooker_email = mo.field AND " .
                    "mo.verified = '1'",
                    array()
                )->where("(pa.verified = '1') OR (mo.verified = '1')");
            
            // filter balance for account
            $balanceType = Mage::getStoreConfig('affiliateplus/account/balance', $store);
            $minBalance = Mage::getStoreConfig('affiliateplus/payment/payment_release', $store);
            
            $amountType = Mage::getStoreConfig('affiliateplus_payment/recurring/amount_type', $store);
            $amountValue = Mage::getStoreConfig('affiliateplus_payment/recurring/amount_value', $store);
            if ($amountValue < 0.0001) {
                continue;
            }
            if ($amountType != 'percentage') {
                $minBalance = ($minBalance > $amountValue) ?  $minBalance : $amountValue;
            }
            if ($minBalance <= 0.01) {
                $minBalance = 0.01;
            }
            if ($balanceType == 'global') {
                $collection->addFieldToFilter('main_table.balance', array('gteq' => $minBalance));
                $collection->getSelect()->columns(array('recurring_balance' => 'balance'));
            } else {
                $collection->getSelect()
                    ->joinLeft(array('b' => $collection->getTable('affiliateplus/account_value')),
                        "main_table.account_id = b.account_id AND b.attribute_code = 'balance' " .
                        "AND b.store_id = " . $store->getId(),
                        array('recurring_balance' => 'b.value')
                    )->where('b.value >= ?', $minBalance);
            }
            
            // ignore existed affiliate account
            if (count($receivedIds)) {
                $collection->addFieldToFilter('main_table.account_id', array('nin' => $receivedIds));
            }
            
            // filter by time received by recurring payment
            $collection->getSelect()->order('last_received_date ASC');
            $period = Mage::getStoreConfig('affiliateplus_payment/recurring/period', $store);
            $period = ($period > 1) ? $period : Mage::getStoreConfig('affiliateplus_payment/recurring/days', $store);
            if ($period >= 1) {
                $period = time() - (int)$period * 86400;
                $collection->getSelect()
                    ->where('(last_received_date IS NULL OR last_received_date <= ?)', date('Y-m-d', $period));
            }
            
            // filter status for account and account need use recurring payment
            $collection->getSelect()
                ->joinLeft(array('s' => $collection->getTable('affiliateplus/account_value')),
                    "main_table.account_id = s.account_id AND s.attribute_code = 'status' " .
                    "AND s.store_id = " . $store->getId(),
                    array()
                )->where("IF(s.value IS NULL, main_table.status, s.value) = ?", 1)
                ->where("main_table.recurring_payment = ?", 1);
            
            // filter account that has pending payment
            $condition = "main_table.account_id = p.account_id AND p.status = 1 AND FIND_IN_SET({$store->getId()},p.store_ids)";
            if (Mage::getStoreConfig('affiliateplus_payment/recurring/pending_request', $store)) {
                if ($balanceType == 'global') {
                    $balance = "main_table.balance";
                } else {
                    $balance = "b.value";
                }
                $condition .= " AND p.is_reduced_balance = 0 AND (p.is_recurring = '1' OR ";
                if ($amountType == 'percentage') {
                    $percent = $amountValue / 100;
                    $condition .= "$balance < ($percent * $balance +  p.amount) )";
                } else {
                    $condition .= "$balance < ($amountValue + p.amount) )";
                }
            }
            $collection->getSelect()
                ->joinLeft(array('p' => $collection->getTable('affiliateplus/payment')),
                    $condition,
                    array()
                )->where("payment_id IS NULL");
            
            // limit number of acount can received
            $maxAccount = (int)Mage::getStoreConfig('affiliateplus_payment/recurring/max_account', $store);
            if ($maxAccount) {
                $collection->getSelect()->limit($maxAccount);
            }
            
            // start auto pay to Affiliate account
            $transactions = array();
            foreach ($collection as $acc) {
                if ($amountType == 'percentage') {
                    $amountValue = ($amountValue < 100) ? $amountValue : 100;
                    $amount = $acc->getData('recurring_balance') * $amountValue / 100;
                } else {
                    $amount = $amountValue;
                }
                $account = Mage::getModel('affiliateplus/account');
                if ($balanceType != 'global') {
                    $account->setStoreId($store->getId());
                }
                $account->load($acc->getId());
                $account->setData('last_received_date', now(true));
                $account->setData('is_created_by_recurring', 1);
                
                // Prepare amount for tax calculation
                if ($rate = Mage::helper('affiliateplus/payment_tax')->getTaxRate($account)) {
                    $amount = $amount * 100 / (100 + $rate);
                }
                
                $payoutTransaction = self::automaticPayout($account, $amount, $store);
                if (is_array($payoutTransaction) && $payoutTransaction) {
                    $transactions[] = $payoutTransaction;
                    $receivedIds[] = $account->getId();
                }
            }
            self::sendEmailToAdmin($transactions, $store);
        }
    }
    
    /**
     * Automatic Payout for Affiliate (using API)
     * 
     * @param Magestore_Affiliateplus_Model_Account $account
     * @param float $amount
     * @param Mage_Core_Model_Store $store
     * @return array (account, payment) | false
     */
    public function automaticPayout($account, $amount, $store) {
        if (!Mage::getStoreConfig('affiliateplus_payment/recurring/auto_complete', $store)) {
            $payment = Mage::getModel('affiliateplus/payment')
                ->setPaymentMethod($account->getData('recurring_method'))
                ->setAmount($amount)
                ->setAccountId($account->getId())
                ->setAccountName($account->getName())
                ->setAccountEmail($account->getEmail())
                ->setStoreIds($store->getId())
                ->setIsPayerFee(0)
                ->setRequestTime(now())
                ->setStatus(1)
                ->setData('is_created_by_recurring', 1)
                ->setData('is_recurring', 1)
                ->setIsRequest(0);
            if (Mage::getStoreConfig('affiliateplus/payment/who_pay_fees', $store) == 'payer') {
                $payment->setIsPayerFee(1);
            }
            $payment->setData('affiliateplus_account', $account);
            try {
                $payment->save();
                $payment->getPayment()
                    ->setEmail($account->getData($account->getData('recurring_method').'_email'))
                    ->savePaymentMethodInfo();
                self::sendEmailToAccount($payment, $account, $store);
            } catch (Exception $e) {
                return false;
            }
            return array(
                'account'   => $account,
                'payment'   => $payment,
            );
        }
        if ($account->getData('recurring_method') == 'paypal') {
            // Paypal Recurring Payment
            $payment = Mage::helper('affiliatepluspayment/paypal')
                ->payoutByApi($account, $amount, $store->getId());
            if ($payment && $payment->getId()) {
                self::sendEmailToAccount($payment, $account, $store);
                return array(
                    'account'   => $account,
                    'payment'   => $payment
                );
            }
        } elseif ($account->getData('recurring_method') == 'moneybooker') {
            // Moneybooker Recurring Payment
            $payment = Mage::helper('affiliatepluspayment/moneybooker')
                ->payoutByApi($account, $amount, $store->getId());
            if ($payment && $payment->getId()) {
                self::sendEmailToAccount($payment, $account, $store);
                return array(
                    'account'   => $account,
                    'payment'   => $payment
                );
            }
        }
        return false;
    }
    
    /**
     * send email notification to Admin
     * 
     * @param array $transactions
     * @param mixed $store
     * @return type
     */
    public function sendEmailToAdmin($transactions, $store = null) {
        if (!is_array($transactions) || !count($transactions)) {
            return false;
        }
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        
        $store = Mage::app()->getStore($store);
        $template = Mage::getStoreConfig('affiliateplus_payment/recurring/admin_template', $store);
        $sender   = Mage::getStoreConfig('trans_email/ident_general', $store);
        
        Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area'  => 'frontend',
                'store' => $store->getId(),
            ))->sendTransactional(
                $template,
                $sender,
                Mage::getStoreConfig('trans_email/ident_sales/email', $store),
                Mage::getStoreConfig('trans_email/ident_sales/name', $store),
                array(
                    'store' => $store,
                    'transactions'  => $transactions,
                    'sales_name'    => Mage::getStoreConfig('trans_email/ident_sales/name', $store),
                    'request_time'  => Mage::helper('core')->formatDate(now(), 'medium'),
                )
            );
        
        $translate->setTranslateInline(true);
        return true;
    }
    
    /**
     * send email notification to 
     * 
     * @param Magestore_Affiliateplus_Model_Payment $payment
     * @param Magestore_Affiliateplus_Model_Account $account
     * @param mixed $store
     * @return type
     */
    public function sendEmailToAccount($payment, $account, $store = null) {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        
        $store = Mage::app()->getStore($store);
        $template = Mage::getStoreConfig('affiliateplus_payment/recurring/account_template', $store);
        $sender   = Mage::getStoreConfig('trans_email/ident_sales', $store);
        
        $status = array(
            1 =>  Mage::helper('affiliateplus')->__('Pending'),
            2 =>  Mage::helper('affiliateplus')->__('Processing'),
            3 =>  Mage::helper('affiliateplus')->__('Completed'),
            4 =>  Mage::helper('affiliateplus')->__('Canceled')
        );
        Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area'  => 'frontend',
                'store' => $store->getId(),
            ))->sendTransactional(
                $template,
                $sender,
                $account->getEmail(),
                $account->getName(),
                array(
                    'store' => $store,
                    'account'   => $account,
                    'payment'   => $payment,
                    'request_time'      => Mage::helper('core')->formatDate($payment->getRequestTime(), 'medium'),
                    'email_label'       => ucfirst($payment->getData('payment_method')),
                    'email_address'     => $account->getData($payment->getData('payment_method').'_email'),
                    'pay_amount'        => Mage::helper('core')->currency($payment->getAmount()),
                    'pay_fee'           => Mage::helper('core')->currency($payment->getFee()),
                    'pay_status'        => $status[$payment->getStatus()],
                    'account_balance'   => Mage::helper('core')->currency($account->getBalance())
                )
            );
        
        $translate->setTranslateInline(true);
        return true;
    }
}

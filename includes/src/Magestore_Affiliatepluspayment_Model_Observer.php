<?php

class Magestore_Affiliatepluspayment_Model_Observer extends Varien_Object {

    public function requestPaymentActionOffline($observer) {
        $paymentObj = $observer->getEvent()->getPaymentObj();
        $payment = $observer->getEvent()->getPayment();
        $paymentMethod = $observer->getEvent()->getPaymentMethod();
        $request = $observer->getEvent()->getRequest();

        $account = Mage::getSingleton('affiliateplus/session')->getAccount();

        $customer = Mage::getModel('customer/session')->getCustomer();
        $data = $request->getPost();
        if ($data['account_address_id']) {
            $address = Mage::getModel('customer/address')->load($data['account_address_id']);
        } else {
            $address_data = $request->getPost('account');
            $address = Mage::getModel('customer/address')
                    ->setData($address_data)
                    ->setParentId($customer->getId())
                    ->setFirstname($customer->getFirstname())
                    ->setLastname($customer->getLastname())
                    ->setId(null);
            $customer->addAddress($address);
            $errors = $address->validate();
            if (!is_array($errors))
                $errors = array();
            $validationResult = (count($errors) == 0);
            try {
                if (true === $validationResult) {
                    $address->save();
                } else {
                    foreach ($errors as $error)
                        Mage::getSingleton('core/session')->addError($error);
                    return $this;
                }
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                return $this;
            }
        }
        if ($address->getId()) {
            $paymentMethod->setAddressId($address->getId());
            $paymentMethod->setAddressHtml($address->format('html'));
            $paymentObj->setRequired(false);
            $fee = Mage::getStoreConfig('affiliateplus_payment/offline/fee_value');
            if (Mage::getStoreConfig('affiliateplus_payment/offline/fee_type') == 'percentage')
                $fee = $payment->getAmount() * $fee / 100;
            $payment->setFee($fee);
        }
        return $this;
    }

    public function paymentMethodFormOffline($observer) {
        $form = $observer->getEvent()->getForm();
        $fieldset = $observer->getEvent()->getFieldset();

        $data = array_merge(array(
            'is_request'    => '',
            'payment_method'    => '',
            'status'        => '',
            'account_id'    => '',
            'offline_address_html'  => '',
        ), $form->getFormValues());
        if (($data['is_request'] && $data['payment_method'] == 'offline') || $data['status'] == 3){
            $accountId = $data['account_id'];
            $addressId = $data['payment']->getAddressId();
            $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($accountId, $addressId, 'offline');
            if($verify->isVerified())
                $elementHtml .= '<div class="element-success"><h2>Verified</h2></div>';
            else
                $elementHtml .= '<div class="element-failed"><h2>Not verified</h2></div>';
            $fieldset->addField('offline_address_html', 'note', array(
                'label' => Mage::helper('affiliatepluspayment')->__('Address'),
                'text' => $data['offline_address_html'],
                'after_element_html' => $elementHtml
            ));
        }else {
            $customerId = Mage::getModel('affiliateplus/account')->load($data['account_id'])->getCustomerId();
            $addresses = Mage::getModel('customer/customer')->load($customerId)->getAddresses();
            $options = array();
            foreach ($addresses as $address)
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            $fieldset->addField('offline_address_id', 'select', array(
                'label' => Mage::helper('affiliatepluspayment')->__('Address'),
                'name' => 'offline_address_id',
                'class' => 'required-entry',
                'required' => true,
                'values' => $options,
            ));
        }
        if(isset($data['offline_address_id'])){
            $address = Mage::getModel('customer/address')->load($data['offline_address_id']);
            if($address->getId()){
                if(isset($data['account_id'])){
                    $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($data['account_id'], $address->getId(), 'offline');
                    if($verify->getInfo()){
                        $fieldset->addField('invoice_address', 'note', array(
                            'label' => Mage::helper('affiliateplus')->__('Invoice Address'),
                            'text' => '<img width="250" src="'.Mage::getBaseUrl('media').'/affiliateplus/payment/'.$verify->getInfo().'" />',
                        ));
                    }
                }
            }
        }
        $fieldset->addField('offline_transfer_info', 'text', array(
            'label' => Mage::helper('affiliatepluspayment')->__('Transfer Information'),
            'name' => 'offline_transfer_info',
            'class' => 'required-entry',
            'required' => true,
        ));
        $fieldset->addField('offline_message', 'textarea', array(
            'label' => Mage::helper('affiliatepluspayment')->__('Message'),
            'name' => 'offline_message',
            'required' => false,
        ));
    }

    public function requestPaymentActionBank($observer) {
        $paymentObj = $observer->getEvent()->getPaymentObj();
        $payment = $observer->getEvent()->getPayment();
        $paymentMethod = $observer->getEvent()->getPaymentMethod();
        $request = $observer->getEvent()->getRequest();

        $account = Mage::getSingleton('affiliateplus/session')->getAccount();

        $data = $request->getPost();
        if (isset($data['payment_bankaccount_id']) && $data['payment_bankaccount_id']) {
            $bankAccount = Mage::getModel('affiliatepluspayment/bankaccount')->load($data['payment_bankaccount_id']);
        } else {
            $bank_account_data = $request->getPost('bank');
            $bankAccount = Mage::getModel('affiliatepluspayment/bankaccount')
                    ->setData($bank_account_data)
                    ->setAccountId($account->getId())
                    ->setId(null);
            $errors = $bankAccount->validate();
            if (!is_array($errors))
                $errors = array();
            $validationResult = (count($errors) == 0);
            try {
                if (true === $validationResult) {
                    $bankAccount->save();
                } else {
                    foreach ($errors as $error)
                        Mage::getSingleton('core/session')->addError($error);
                    return $this;
                }
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                return $this;
            }
        }
        if ($bankAccount->getId()) {
            $paymentMethod->setBankaccountId($bankAccount->getId())
                    ->setBankaccountHtml($bankAccount->format(true));
            $paymentObj->setRequired(false);
            $fee = Mage::getStoreConfig('affiliateplus_payment/bank/fee_value');
            if (Mage::getStoreConfig('affiliateplus_payment/bank/fee_type') == 'percentage')
                $fee = $payment->getAmount() * $fee / 100;
            $payment->setFee($fee);
        }
        return $this;
    }

    public function requestPaymentActionMoneybooker($observer) {
        $paymentObj = $observer->getEvent()->getPaymentObj();
        $payment = $observer->getEvent()->getPayment();
        $paymentMethod = $observer->getEvent()->getPaymentMethod();
        $request = $observer->getEvent()->getRequest();
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $storeId = Mage::app()->getStore()->getId();

        $moneybooker_email = $request->getParam('moneybooker_email');
        if(!$moneybooker_email)
            if(Mage::getSingleton('affiliateplus/session')->getPayment())
                $moneybooker_email = Mage::getSingleton('affiliateplus/session')->getPayment()->getEmail();
   
        //change money booker email for affiliate account
        if ($moneybooker_email && $moneybooker_email != $account->getMoneybookerEmail()) {
            $accountModel = Mage::getModel('affiliateplus/account')
                    ->setStoreId($storeId)
                    ->load($account->getId())
            ;
            try {
                $accountModel->setMoneybookerEmail($moneybooker_email)
                        ->setId($account->getId())
                        ->save();
            } catch (Exception $e) {
                
            }
        }
        $moneybooker_email = $moneybooker_email ? $moneybooker_email : $account->getMoneybookerEmail();
        if ($moneybooker_email) {
            $paymentMethod->setEmail($moneybooker_email);
            $paymentObj->setRequired(false);
        }
    }

    public function paymentMethodFormBank($observer) {
        $form = $observer->getEvent()->getForm();
        $fieldset = $observer->getEvent()->getFieldset();

        $data = $form->getFormValues();        
        if (($data['is_request'] && $data['payment_method'] == 'bank') || $data['status'] == 3){
            $elementHtml = '';
            if(isset($data['account_id']) && isset($data['payment'])){
                $accountId = $data['account_id'];
                $bankaccountId = $data['payment']->getBankaccountId();
                $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($accountId, $bankaccountId, 'bank');
                if($verify->isVerified())
                    $elementHtml .= '<div class="element-success"><h2>Verified</h2></div>';
                else
                    $elementHtml .= '<div class="element-failed"><h2>Not verified</h2></div>';
            }
            $fieldset->addField('bank_bankaccount_html', 'note', array(
                'label' => Mage::helper('affiliateplus')->__('Bank Account'),
                'text' => isset($data['bank_bankaccount_html']) ? $data['bank_bankaccount_html'] : '',
                'after_element_html' => $elementHtml
            ));
        }else {
            $bankAccounts = Mage::getResourceModel('affiliatepluspayment/bankaccount_collection')
                    ->addFieldToFilter('account_id', $data['account_id']);
            $options = array();
            foreach ($bankAccounts as $bankAccount)
                $options[] = array(
                    'value' => $bankAccount->getId(),
                    'label' => $bankAccount->format(false)
                );
            
            $fieldset->addField('bank_bankaccount_id', 'select', array(
                'label' => Mage::helper('affiliatepluspayment')->__('Bank Account'),
                'name' => 'bank_bankaccount_id',
                'required' => false,
                'values' => $options,
            ));
        }
        if(isset($data['bank_bankaccount_id'])){
            $bankAccount = Mage::getModel('affiliatepluspayment/bankaccount')->load($data['bank_bankaccount_id']);
            if($bankAccount->getId()){
                $fieldset->addField('bank_statement', 'note', array(
                        'label' => Mage::helper('affiliateplus')->__('Bank Statement'),
                        'text' => '<img src="'.Mage::getBaseUrl('media').'/affiliateplus/payment/'.$bankAccount->getBankStatement().'" />',
                        
                ));
            }
        }
        $fieldset->addField('bank_invoice_number', 'text', array(
            'label' => Mage::helper('affiliatepluspayment')->__('Invoice Number'),
            'name' => 'bank_invoice_number',
            'required' => false,
        ));
        $fieldset->addField('bank_message', 'textarea', array(
            'label' => Mage::helper('affiliatepluspayment')->__('Message'),
            'name' => 'bank_message',
            'required' => false,
        ));
    }
    
    public function paymentMethodFormMoneybooker($observer) {
        $form = $observer->getEvent()->getForm();
        $fieldset = $observer->getEvent()->getFieldset();

        $data = $form->getFormValues();
        $readOnly = (isset($data['status']) && $data['status'] >= 3);
        $fieldset->addField('moneybooker_email', 'text', array(
            'label' => Mage::helper('affiliatepluspayment')->__('Moneybooker Email Address'),
            'name'  => 'moneybooker_email',
            'readonly'  => true,
            'required'  => true,
            'note'      => $readOnly ? null : Mage::helper('affiliateplus')->__('You can change this email address on the Edit Account page.'),
        ));
        
        if (!$readOnly || (isset($data['moneybooker_transaction_id']) && $data['moneybooker_transaction_id'])) {
            $fieldData = array(
                'label' => Mage::helper('affiliatepluspayment')->__('Transaction ID'),
                'name'  => 'moneybooker_transaction_id',
                // 'readonly'  => $readOnly,
                'required'  => false,
            );
            if ($readOnly) {
                $fieldData['readonly'] = true;
            }
            $fieldset->addField('moneybooker_transaction_id', 'text', $fieldData);
        }
    }
    
    // add field to edit account form
    public function addMoneybookerEmail($observer) {
        $fieldset = $observer->getEvent()->getFieldset();

        $fieldset->addField('moneybooker_email', 'text', array(
            'label' => Mage::helper('affiliatepluspayment')->__('Moneybooker Email Address'),
            'name'  => 'moneybooker_email',
            'required'  => false,
        ));
        
        $fieldset->addField('recurring_payment', 'select', array(
            'label' => Mage::helper('affiliatepluspayment')->__('Enable Recurring Payment'),
            'name'  => 'recurring_payment',
            'values'    => array(
                '1' => Mage::helper('affiliatepluspayment')->__('Yes'),
                '0' => Mage::helper('affiliatepluspayment')->__('No'),
            ),
        ));
        
        $fieldset->addField('recurring_method', 'select', array(
            'label' => Mage::helper('affiliatepluspayment')->__('Recurring Payment Method'),
            'name'  => 'recurring_method',
            'values'=> array(
                'paypal'        => Mage::helper('affiliatepluspayment')->__('PayPal'),
                'moneybooker'   => Mage::helper('affiliatepluspayment')->__('Moneybooker')
            )
        ));
    }

    public function checkPaymentMethod($observer) {
        $data = Mage::app()->getRequest()->getPost('groups');
        $isActivePaypal = $data['paypal']['fields']['active']['value'];
        $isActiveOffline = $data['offline']['fields']['active']['value'];
        $isActiveBank = $data['bank']['fields']['active']['value'];

        if (!$isActivePaypal && !$isActiveOffline && !$isActiveBank) {
            $store = $observer->getStore();
            $website = $observer->getWebsite();
            $groups['paypal']['fields']['active']['value'] = 1;

            Mage::getModel('adminhtml/config_data')
                    ->setSection('affiliateplus_payment')
                    ->setWebsite($website)
                    ->setStore($store)
                    ->setGroups($groups)
                    ->save();

            Mage::getSingleton('core/session')->addNotice(Mage::helper('affiliatepluspayment')->__('Need at least one active method payment'));
        }
    }
    
    public function affiliateplusPaymentPrepare($observer)
    {
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $object = $observer->getEvent()->getPaymentData();
        $files = $observer->getEvent()->getFile();
        $params = $object->getParams();
        if(isset($params['payment_method']) && $params['payment_method'] == 'offline')
        {
            if(isset($params['account_address_id']) && $params['account_address_id'])
            {
                
                $address = Mage::getModel('customer/address')->load($params['account_address_id']);
                if ($address->getId()) {
					/*an exist address selected*/
                    $html = $address->format('html');
                    if(isset($files['invoice_address']) && is_array($files['invoice_address'])){
                        if(isset($files['invoice_address']['name']) && $files['invoice_address']['name'] != '') {
                            $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($account->getId(), $address->getId(), 'offline');
                            if(!$verify->isVerified()){
								$filename = Mage::helper('affiliateplus/payment')->uploadVerifyImage('invoice_address',$files);
                                
                                if($filename){
                                    
                                    $verify->setData('info',$filename);
                                    $verify->setData('account_id',$account->getId());
                                    $verify->setData('payment_method','offline');
                                    $verify->setData('field',$address->getId());
                                    try{
                                        $verify->save();
                                        $params['invoice_address'] = $filename;
                                    }  catch (Exception $e){
                                        
                                    }
                                }
                            }else{
                                //$html .= '<br/>'.Mage::helper('affiliatepluspayment')->__('Invoice Address <br/> %s','<img width="250" src="'.Mage::getBaseUrl('media').'/affiliateplus/payment/'.$verify->getInfo().'" />');
                            }
                        }
                    }
                    $params['address_html'] = $html;
                }
            }else{
				/*new address offline*/
                $account_data = $params['account'];
                $html = Mage::helper('affiliatepluspayment')->__('%s',$account->getName());
                $address = Mage::getModel('customer/address')->setData($account_data);
                $html .= $address->format('html');
                if(isset($files['invoice_address']) && is_array($files['invoice_address'])){
                    if(isset($files['invoice_address']['name']) && $files['invoice_address']['name'] != '') {
                        $filename = Mage::helper('affiliateplus/payment')->uploadVerifyImage('invoice_address',$files);

                       if($filename){
                            $verify = Mage::getModel('affiliateplus/payment_verify');
                            $verify->setData('info',$filename);
                            $verify->setData('account_id',$account->getId());
                            $verify->setData('payment_method','offline');
                            $verify->setData('field',0);
                            try{
                                $verify->save();
                                $params['invoice_address'] = $filename;
                                //$html .= Mage::helper('affiliatepluspayment')->__('Bank Statement <br/> %s','<img width="250" src="'.Mage::getBaseUrl('media').'/affiliateplus/payment/'.$params['bank_statement'].'" />');
                            }  catch (Exception $e){

                            }
                        }
                            //$html .= '<br/>'.Mage::helper('affiliatepluspayment')->__('Invoice Address <br/> %s','<img width="250" src="'.Mage::getBaseUrl('media').'/affiliateplus/payment/'.$params['invoice_address'].'" />');
                    }
                }
                $params['address_html'] = $html;
            }
            
        }
        if(isset($params['payment_method']) && $params['payment_method'] == 'bank'){
            if (isset($params['payment_bankaccount_id']) && $params['payment_bankaccount_id']) {
                $bankAccount = Mage::getModel('affiliatepluspayment/bankaccount')->load($params['payment_bankaccount_id']);
                if ($bankAccount->getId()) {
					/*an exist bank account selected*/
                    $html = $bankAccount->format(true);
                    if(isset($files['bank_statement']) && is_array($files['bank_statement'])){
                        if(isset($files['bank_statement']['name']) && $files['bank_statement']['name'] != '') {
                            $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($account->getId(), $bankAccount->getId(), 'bank');
                            if(!$verify->isVerified()){
                                
                                $filename = Mage::helper('affiliateplus/payment')->uploadVerifyImage('bank_statement',$files);
                                
                                if($filename){
                                    
                                    $verify->setData('info',$filename);
                                    $verify->setData('account_id',$account->getId());
                                    $verify->setData('payment_method','bank');
                                    $verify->setData('field',$bankAccount->getId());
                                    try{
                                        $verify->save();
                                        $params['bank_statement'] = $filename;
                                        //$html .= Mage::helper('affiliatepluspayment')->__('Bank Statement <br/> %s','<img width="250" src="'.Mage::getBaseUrl('media').'/affiliateplus/payment/'.$params['bank_statement'].'" />');
                                    }  catch (Exception $e){
                                        
                                    }
                                }
                            }
                        }
                    }
                    $params['bankaccount_html'] = $html;
                }
            }else{
				/*new bank account*/
                $bank_account_data = $params['bank'];
                $html = Mage::helper('affiliatepluspayment')->__('Bank: %s',$bank_account_data['name']).'<br />';
                $html .= Mage::helper('affiliatepluspayment')->__('Account: %s',$bank_account_data['account_name']).'<br />';
                $html .= Mage::helper('affiliatepluspayment')->__('Acc Number: %s',$bank_account_data['account_number']).'<br />';
                if (isset($bank_account_data['routing_code']))
                    $html .= Mage::helper('affiliatepluspayment')->__('Routing Code: %s',$bank_account_data['routing_code']).'<br />';
                if (isset($bank_account_data['address']))
                    $html .= Mage::helper('affiliatepluspayment')->__('Bank Address: %s',$bank_account_data['address']).'<br />';
                if(isset($files['bank_statement']) && is_array($files['bank_statement'])){
                    if(isset($files['bank_statement']['name']) && $files['bank_statement']['name'] != '') {
                        $filename = Mage::helper('affiliateplus/payment')->uploadVerifyImage('bank_statement',$files);
                                
                        if($filename){
                            $verify = Mage::getModel('affiliateplus/payment_verify');
                            $verify->setData('info',$filename);
                            $verify->setData('account_id',$account->getId());
                            $verify->setData('payment_method','bank');
                            $verify->setData('field',0);
                            try{
                                $verify->save();
                                $params['bank_statement'] = $filename;
                                //$html .= Mage::helper('affiliatepluspayment')->__('Bank Statement <br/> %s','<img width="250" src="'.Mage::getBaseUrl('media').'/affiliateplus/payment/'.$params['bank_statement'].'" />');
                            }  catch (Exception $e){

                            }
                        }
                    }
                }
                $params['bankaccount_html'] = $html;
            }
        }
        $object->setParams($params);
    }

    /* Magic 28/11/2012 */

    public function affiliateplusAccountEditPost($observer) {
        $action = $observer->getEvent()->getControllerAction();
        $data = $action->getRequest()->getParams();
        $session = Mage::getSingleton('affiliateplus/session');
        // $coreSession=Mage::getSingleton('core/session');
        $account = Mage::getModel('affiliateplus/account')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($session->getAccount()->getId());
        try {
            if (isset($data['recurring_payment']))
                $account->setData('recurring_payment', $data['recurring_payment']);
            else
                $account->setData('recurring_payment', 0);
            if (isset($data['recurring_method']) && $data['recurring_method'])
                $account->setData('recurring_method', $data['recurring_method']);
            if (isset($data['moneybooker_email']) && $data['moneybooker_email'])
                $account->setData('moneybooker_email', $data['moneybooker_email']);
            $account->save();
        } catch (Exception $e) {
            //$coreSession->addError($e->getMessage());
        }
        return;
    }
    
    public function changeColumnPaymentGrid($observer) {
        $grid = $observer['grid'];
        $grid->addColumn('is_recurring', array(
            'header'    => Mage::helper('affiliateplus')->__('Is Recurring'),
            'align'     => 'left',
            'index'     => 'is_recurring',
            'type'      => 'options',
            'options'   => array(
                '0' => Mage::helper('affiliateplus')->__('No'),
                '1' => Mage::helper('affiliateplus')->__('Yes'),
            )
        ));
    }
    
    public function addFieldPaymentForm($observer) {
        $fieldset = $observer['fieldset'];
        $data = $observer->getEvent()->getForm()->getFormValues();
        if (isset($data['is_recurring']) && $data['is_recurring']) {
            $fieldset->addField('is_recurring', 'note', array(
                'label' => Mage::helper('affiliateplus')->__('Is Recurring Payment'),
                'text'  => '<strong>' . Mage::helper('affiliateplus')->__('Yes') . '</strong>',
            ));
        }
    }
}

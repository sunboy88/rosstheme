<?php

class Magestore_Affiliatepluspayment_Block_Recurring_Payment extends Magestore_Affiliateplus_Block_Account_Edit {

    protected function _construct() {

        parent::_construct();
        if (Mage::getStoreConfig('affiliateplus_payment/recurring/enable') && count($this->getMethodArr())) {
            $this->setTemplate('affiliatepluspayment/recurring/payment.phtml');
        } else {
            $this->setTemplate('affiliateplus/account/edit.phtml');
        }
        return $this;
    }

    public function getMethodArr() {
        $methodpayment = array();
        if ($this->paypalActive())
            $methodpayment['paypal'] = 'PayPal';
        if ($this->moneybookerActive())
            $methodpayment['moneybooker'] = 'MoneyBookers';
        return $methodpayment;
    }

    public function getRecurringPayment() {
        return $this->getAccount()->getRecurringPayment();
    }

    public function getRecurringMethod() {
        return $this->getAccount()->getRecurringMethod();
    }

    public function getMoneybookerEmail() {
        return $this->getAccount()->getMoneybookerEmail();
    }

    public function moneybookerActive() {
        return Mage::getStoreConfig('affiliateplus_payment/moneybooker/active');
    }

    public function paypalActive() {
        return Mage::getStoreConfig('affiliateplus_payment/paypal/active');
    }

    public function moneybookerDisplay() {
        if (!$this->paypalActive())
            return TRUE;
        if (($this->moneybookerActive() && ($this->getRecurringMethod() == 'moneybooker')))
            return TRUE;
    }

}
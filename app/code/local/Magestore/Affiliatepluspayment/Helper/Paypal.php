<?php

class Magestore_Affiliatepluspayment_Helper_Paypal extends Magestore_Affiliateplus_Helper_Payment_Paypal
{
    public function getPaymanetUrl($data) {
        $url = $this->_getMasspayUrl();
        $i = 0;
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        foreach ($data as $item) {
            $url .= '&L_EMAIL' . $i . '=' . $item['email'] . '&L_AMT' . $i . '=' . $item['amount'] . '&CURRENCYCODE' . $i . '=' . $baseCurrencyCode;
            $i++;
        }
        return $url;
    }

    protected function _getMasspayUrl() {
        $url = $this->_getApiEndpoint();
        $url .= '&METHOD=MassPay&RECEIVERTYPE=EmailAddress';
        return $url;
    }

    protected function _getApiEndpoint() {
        if (Mage::getStoreConfig('affiliateplus_payment/paypal/user_mechant_email_default')) {
            $isSandbox = Mage::getStoreConfig('paypal/wpp/sandbox_flag');
        } else {
            $isSandbox = Mage::getStoreConfig('affiliateplus_payment/paypal/sandbox_mode');
        }
        
        $paypalApi = $this->_getPaypalApi();
        $url = sprintf('https://api-3t%s.paypal.com/nvp?', $isSandbox ? '.sandbox' : '');
        $url .= 'USER=' . $paypalApi['api_username'] . '&PWD=' . $paypalApi['api_password'] . '&SIGNATURE=' . $paypalApi['api_signature'] . '&VERSION=62.5';
        return $url;
    }

    protected function _getPaypalApi() {
        if (Mage::getStoreConfig('affiliateplus_payment/paypal/user_mechant_email_default')) {
            $data['api_username'] = Mage::getStoreConfig('paypal/wpp/api_username');
            $data['api_password'] = Mage::getStoreConfig('paypal/wpp/api_password');
            $data['api_signature'] = Mage::getStoreConfig('paypal/wpp/api_signature');
        } else {
            $data['api_username'] = Mage::getStoreConfig('affiliateplus_payment/paypal/api_username');
            $data['api_password'] = Mage::getStoreConfig('affiliateplus_payment/paypal/api_password');
            $data['api_signature'] = Mage::getStoreConfig('affiliateplus_payment/paypal/api_signature');
        }
        return $data;
    }
}
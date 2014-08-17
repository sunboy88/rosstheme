<?php
class Magestore_Onestepcheckout_Block_Onestepcheckout extends Mage_Checkout_Block_Onepage_Abstract {
    var $configData = array();
    public function __construct() 
    {
        $this->configData = $this->_getConfigData();
        //when customer is logged in, we need to set postcode from customer address
        if ($this->isCustomerLoggedIn() && !$this->getOnepage()->getQuote()->getBillingAddress())  {			
            $this->_setAddress();
        }
        else {
            if(!$this->allowDetectCountry()){
                $this->_setDefaultBillingAddress();
                $this->_setDefaultShippingAddress();
            }else{
                $this->_setCountryBillingDetected();
                $this->_setCountryShippingDetected();
            }
        }

        //set default shipping && payment method 
        $this->_setDefaultShippingMethod();
        $this->_setDefaultPaymentMethod();
    }

    protected function _setAddress() 
    {
        $billing_address = $this->getOnepage()->getQuote()->getBillingAddress();
        $shipping_address = $this->getOnepage()->getQuote()->getShippingAddress();
        $postcode = $shipping_address->getPostcode();
        if (!$postcode) {
            $primary = $this->getQuote()->getCustomer()->getPrimaryShippingAddress();
            if ($primary) {
                $postcode = $primary->getPostcode();
            }
            if (!$postcode || $postcode == '') {
                $postcode = $billing_address->getPostcode();
            }
        }
        $shipping_address->setPostcode($postcode)->setCollectShippingRates(true)->save();
    }

    protected function _setDefaultShippingMethod() 
    {
        $shipping_address = $this->getOnepage()->getQuote()->getShippingAddress();
        $shipping_method = $shipping_address->getShippingMethod();
        if (!$shipping_method || $shipping_method == '') {
            //set default shipping method
            $default_shipping_method = $this->configData['default_shipping'];
            if ($default_shipping_method != '') {
                //Mage::helper('onestepcheckout')->saveShippingMethod($default_shipping_method);
                $this->getOnePage()->getQuote()->getShippingAddress()->setShippingMethod($default_shipping_method);
            }
            else {			
                // if no default shipping method and only one shipping method is available, set it as default
                if ($method = $this->hasOnlyOneShippingMethod()){
                        //Mage::helper('onestepcheckout')->saveShippingMethod($method);
                        $this->getOnePage()->getQuote()->getShippingAddress()->setShippingMethod($method);
                }
            }
        }
        $this->getOnePage()->getQuote()->collectTotals()->save();
    }

    /*
    * set default payment method
    */
    protected function _setDefaultPaymentMethod() 
    {
        $paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();
        if (!$paymentMethod || $paymentMethod == '') {
            $default_payment_method = $this->configData['default_payment'];
            if ($default_payment_method != '') {
                $payment = array('method' => $default_payment_method);
                try {
                        Mage::helper('onestepcheckout')->savePaymentMethod($payment);
                }
                catch (Exception $e) {
                    // ignore error
                }
            }
            else {
            }
        }
    }

    /*
    * check if only one shipping method is enabled
    */
    public function hasOnlyOneShippingMethod() 
    {
        $rates = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingRatesCollection();
        $rateCodes = array();
        foreach($rates as $rate){
            if(!in_array($rate->getCode(), $rateCodes)){
                $rateCodes[] = $rate->getCode();
            }
        }
        if(count($rateCodes) == 1)  {
            return $rateCodes[0];
        }		
        return false;
    }

    protected function _getConfigData() 
    {
        return Mage::helper('onestepcheckout')->getConfigData();
    }

    public function getCheckoutTitle() 
    {
        return $this->configData['checkout_title'];
    }

    public function getCountryHtmlSelect($type)
    {
        if($type == 'billing')	{
            $address = $this->getQuote()->getBillingAddress();	
        }
        else{
            $address = $this->getQuote()->getShippingAddress();
        }

        $countryId = $address->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::getStoreConfig('onestepcheckout/general/country_id',Mage::app()->getStore(true)->getId());
        }
        $select = $this->getLayout()->createBlock('core/html_select')
                        ->setName($type.'[country_id]')
                        ->setId($type.':country_id')
                        ->setTitle(Mage::helper('onestepcheckout')->__('Country'))
                        ->setClass('validate-select')
                        ->setValue($countryId)
                        ->setOptions($this->getCountryOptions())
                        ->setExtraParams('style="width:135px"');
        //if ($type === 'shipping') {
        //	$select->setExtraParams('onchange="shipping.setSameAsBilling(false);"');
        //}

        return $select->getHtml();
    }

    public function getCity() 
    {
        $city = $this->getAddress()->getCity();
        $primary = $this->getQuote()->getCustomer()->getPrimaryBillingAddress();         
        if(empty($city)  && $this->_isLoggedIn() && $primary)	{
            return $this->getQuote()->getCustomer()->getPrimaryBillingAddress()->getCity();
        }
        return $city;
    }

    public function getCompany()
    {
        $company = $this->getAddress()->getCompany();
        $primary = $this->getQuote()->getCustomer()->getPrimaryBillingAddress();       
        if(empty($company) && $this->_isLoggedIn() && $primary)	{
                return $this->getQuote()->getCustomer()->getPrimaryBillingAddress()->getCompany();
        }
        return $company;
    }		

    public function getBillingAddress() 
    {	
        return $this->getQuote()->getBillingAddress();		
    }

    public function getShippingAddress() 
    {
        if (!$this->isCustomerLoggedIn()) {
            return $this->getQuote()->getShippingAddress();
        } else {
            return Mage::getModel('sales/quote_address');
        }
    }

    public function isAjaxBillingField($field_name) 
    {
        $fields = explode(',', $this->configData['ajax_fields']);
        if(in_array($field_name, $fields))	{
            return true;
        }		
        return false;
    }

    public function isShowShippingAddress() 
    {
        if($this->getOnepage()->getQuote()->isVirtual())	{
            return false;
        }
        if($this->configData['show_shipping_address'])	{
            return true;
        }
        return false;
    }

    public function isCustomerLoggedIn() 
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    private function _setDefaultShippingAddress() 
    {
        $quote = $this->getOnepage()->getQuote();
        $shipping = $quote->getShippingAddress();	
        if ($shipping->getCountryId() == '') $shipping->setCountryId($this->configData['country_id']);
        if ($shipping->getRegionId() == '') $shipping->setRegionId($this->configData['region_id']);
        if ($shipping->getRegion() == '') $shipping->setRegion($this->configData['region_id']);
        if ($shipping->getPostcode() == '') $shipping->setPostcode($this->configData['postcode']);
        if ($shipping->getCity() == '') $shipping->setCity($this->configData['city']);		
        $shipping->setCollectShippingRates(true)->save();
    }

    private function _setDefaultBillingAddress() 
    {
        $quote = $this->getOnepage()->getQuote();	
        $billing = $quote->getBillingAddress();	
        if ($billing->getCountryId() == '') $billing->setCountryId($this->configData['country_id']);
        if ($billing->getRegionId() == '') $billing->setRegionId($this->configData['region_id']);
        if ($billing->getRegion() == '') $billing->setRegion($this->configData['region_id']);
        if ($billing->getPostcode() == '') $billing->setPostcode($this->configData['postcode']);
        if ($billing->getCity() == '') $billing->setCity($this->configData['city']);		
        $billing->save();
    }

    private function _setCountryBillingDetected() 
    {
        $quote = $this->getOnepage()->getQuote();	
        $billing = $quote->getBillingAddress();				
        $cookieCountryId = Mage::getSingleton('core/cookie')->get('detected_country_id');
        if(!$cookieCountryId){
            $countryId = Mage::helper('onestepcheckout')->detectCountryIp();
            Mage::getSingleton('core/cookie')->set('detected_country_id', $countryId);
        }else{
            $countryId = $cookieCountryId;
        }
        $billing->setCountryId($countryId);			
        $billing->save();		
    }

    private function _setCountryShippingDetected() 
    {
        $quote = $this->getOnepage()->getQuote();			
        $shipping = $quote->getShippingAddress();
        $cookieCountryId = Mage::getSingleton('core/cookie')->get('detected_country_id');
        if(!$cookieCountryId){
            $countryId = Mage::helper('onestepcheckout')->detectCountryIp();
            Mage::getSingleton('core/cookie')->set('detected_country_id', $countryId);
        }else{
            $countryId = $cookieCountryId;
        }
        $shipping->setCountryId($countryId);			
        $shipping->setCollectShippingRates(true)->save();
    }

    public function allowDetectCountry()
    {
        $controllerName = $this->getRequest()->getControllerName();		
        if($controllerName=='index')
            return Mage::helper('onestepcheckout')->allowDetectCountry();
        else	
            return false;
    }

    public function getOnepage() 
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function isShowLoginLink() 
    {
        if ($this->configData['show_login_link']) {
            return true;
        }
        return false;
    }

    public function getCheckoutUrl() 
    {
        return $this->getUrl('onestepcheckout/index/saveOrder', array('_secure' => true));
    }

    public function getAddress()
    {
        if ($this->isCustomerLoggedIn()){
            $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
            if ($customerAddressId){
                $billing = Mage::getModel('customer/address')->load($customerAddressId);
            }else{
                $billing = $this->getQuote()->getBillingAddress();
            }
            if(!$billing->getCustomerAddressId()){
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $default_address = $customer -> getDefaultBillingAddress();
                 if ($default_address) {
                    if ($default_address->getId()) {
                        if ($default_address->getPrefix()) {
                            $billing->setPrefix($default_address->getPrefix());
                        }
                        if ($default_address->getData('firstname')) {
                            $billing->setData('firstname', $default_address->getData('firstname'));
                        }
                        if ($default_address->getData('middlename')) {
                            $billing->setData('middlename', $default_address->getData('middlename'));
                        }if ($default_address->getData('lastname')) {
                            $billing->setData('lastname', $default_address->getData('lastname'));
                        }if ($default_address->getData('suffix')) {
                            $billing->setData('suffix', $default_address->getData('suffix'));
                        }if ($default_address->getData('company')) {
                            $billing->setData('company', $default_address->getData('company'));
                        }if ($default_address->getData('street')) {
                            $billing->setData('street', $default_address->getData('street'));
                        }if ($default_address->getData('city')) {
                            $billing->setData('city', $default_address->getData('city'));
                        }if ($default_address->getData('region')) {
                            $billing->setData('region', $default_address->getData('region'));
                        }if ($default_address->getData('region_id')) {
                            $billing->setData('region_id', $default_address->getData('region_id'));
                        }if ($default_address->getData('postcode')) {
                            $billing->setData('postcode', $default_address->getData('postcode'));
                        }if ($default_address->getData('country_id')) {
                            $billing->setData('country_id', $default_address->getData('country_id'));
                        }if ($default_address->getData('telephone')) {
                            $billing->setData('telephone', $default_address->getData('telephone'));
                        }if ($default_address->getData('fax')) {
                            $billing->setData('fax', $default_address->getData('fax'));
                        }
                        $billing->setCustomerAddressId($default_address->getId())
                                ->save();
                    }
                } else {
                    return $billing;
                }
            }
            return $billing;
        } else {
            return Mage::getModel('sales/quote_address');
        }
    }
	
    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                        'value'=>$address->getId(),
                        'label'=>$address->format('oneline')
                );
            }
            $addressId = $this->getAddress()->getId();
            $shippingAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping();
            if ($shippingAddressId != $addressId && $type == 'shipping'){
                $addressId = $shippingAddressId;
            }
            if (empty($addressId)) {
                if ($type=='billing') {
                        $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                        $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                        $addressId = $address->getId();
                }
            }
            $select = $this->getLayout()->createBlock('core/html_select')
                            ->setName($type.'_address_id')
                            ->setId($type.'-address-select')
                            ->setClass('address-select')
                            ->setExtraParams('style="width:350px"')
                            ->setValue($addressId)
                            ->setOptions($options);
            $select->addOption('', Mage::helper('checkout')->__('New Address'));
            return $select->getHtml();
        }
        return '';
    }
	
    public function isVirtual() 
    {
        return $this->getQuote()->isVirtual();
    }

    public function _getDefaultShippingMethod()
    {
        $_helper = Mage::helper('onestepcheckout');
        $_config = $_helper->getConfigData();
        if($_config['default_shipping'] != '')    {
            return $_config['default_shipping'];
        }else{
            $check_single = $this->_checkSingleShippingMethod();
            if($check_single)   {
                return $check_single;
            }
        }
    }

    protected function _checkSingleShippingMethod()
    {
        $rates = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingRatesCollection();
        $rateCodes = array();
        foreach($rates as $rate){
            if(!in_array($rate->getCode(), $rateCodes)){
                $rateCodes[] = $rate->getCode();
            }
        }
        if(count($rateCodes) == 1)  {
            return $rateCodes[0];
        }
        return false;
    }
    
    public function getCountryHtmlSelectBackend($type) {
        if($type == 'billing')	{
            $address = $this->getQuote()->getBillingAddress();	
        }
        else{
            $address = $this->getQuote()->getShippingAddress();
        }

        $countryId = $address->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::getStoreConfig('onestepcheckout/general/country_id',Mage::app()->getStore(true)->getId());
        }
        $select = $this->getLayout()->createBlock('core/html_select')
                        ->setName('order['.$type.'_address][country_id]')
                        ->setId('order-'.$type.'_address_country_id')
                        ->setTitle(Mage::helper('onestepcheckout')->__('Country'))
                        ->setClass('validate-select')
                        ->setValue($countryId)
                        ->setOptions($this->getCountryOptions())
                        ->setExtraParams('style="width:135px"');
        return $select->getHtml();
    }
}
<?php
class Magestore_Affiliatepluspayment_Block_Offline_Form extends Magestore_Affiliateplus_Block_Payment_Form
{
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('affiliatepluspayment/offline/form.phtml');
		return $this;
    }
    
    public function getPostData(){
        $data = Mage::app()->getRequest()->getParams();
        return $data;
    }
    
    protected function _getSession(){
		return Mage::getSingleton('affiliateplus/session');
	}
    
    public function customerLoggedIn(){
    	return Mage::helper('affiliateplus/account')->customerLoggedIn();
    }
    
    public function isLoggedIn(){
    	return $this->_getSession()->isLoggedIn();
    }
    
    public function getCustomer(){
    	return Mage::getSingleton('customer/session')->getCustomer();
    }
     
    public function getAccount(){
    	return $this->_getSession()->getAccount();
    }
    
    public function getAddress() {	
		$address = Mage::getModel('customer/address');
        $data = $this->getDataPost();
        if($this->isShowForm() && isset($data['account'])){
            $address->setData($data['account']);
        }else{
            if($this->isLoggedIn()){
                $address->load($this->getAccount()->getAddressId());
            } elseif($this->customerLoggedIn()){
                if(!$address->getFirstname())
                    $address->setFirstname($this->getCustomer()->getFirstname());
                if(!$address->getLastname())
                    $address->setLastname($this->getCustomer()->getLastname());
            }
        }
		return $address;
    }
    
    public function customerHasAddresses(){
    	return $this->getCustomer()->getAddressesCollection()->getSize();
    }
    
    public function getDataPost(){
        $data = $this->getPostData();
        return $data;
    }
    
    public function isShowForm(){
        $data = $this->getDataPost();
        if(isset($data['account_address_id']))
            if(!$data['account_address_id'])
                return true;
        return false;
    }


    public function getAddressesHtmlSelect($type){
        $data = $this->getDataPost();
        
        if ($this->customerLoggedIn()){
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                    $data['account']['address_id'] = $options[] = array(
                        'value'=>$address->getId(),
                        'label'=>$address->format('oneline')
                    );
            }
            if(isset($data['account_address_id'])){
                $addressId = $data['account_address_id'];
            }else{
                $addressId = $this->getAddress()->getId();
                if (empty($addressId)) {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                }
            }
            if($addressId)
                $this->setAddressId($addressId);
            
            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
                ->setExtraParams('onchange=lsRequestTrialNewAddress(this.value);')
                ->setValue($addressId)
                ->setOptions($options);            
            $select->addOption('', Mage::helper('checkout')->__('New Address'));
            return $select->getHtml();
        }
        return '';
    }
    
    public function addressIsVerified(){
        $addressId = $this->getAddressId();
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $verifyCollection = Mage::getModel('affiliateplus/payment_verify')
            ->getCollection()
            ->addFieldToFilter('account_id',$account->getId())
            ->addFieldToFilter('payment_method','offline')
            ->addFieldToFilter('field',$addressId)
            ->addFieldToFilter('verified','1')
            ;
        if($verifyCollection->getSize())
            return true;
        return false;
    }
    
    public function getCountryHtmlSelect($type){
        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::getStoreConfig('general/country/default');
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());

        return $select->getHtml();
    }
    
    public function getRegionCollection(){
        if (!$this->_regionCollection){
            $this->_regionCollection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($this->getAddress()->getCountryId())
                ->load();
        }
        return $this->_regionCollection;
    }
    
    public function getRegionHtmlSelect($type){
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[region]')
            ->setId($type.':region')
            ->setTitle(Mage::helper('checkout')->__('State/Province'))
            ->setClass('required-entry validate-state')
            ->setValue($this->getAddress()->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray());

        return $select->getHtml();
    }
    
    public function getCountryCollection(){
        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getSingleton('directory/country')->getResourceCollection()
                ->loadByStore();
        }
        return $this->_countryCollection;
    }
    
    public function getCountryOptions(){
        $options    = false;
        $useCache   = Mage::app()->useCache('config');
        if ($useCache) {
            $cacheId    = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
            $cacheTags  = array('config');
            if ($optionsCache = Mage::app()->loadCache($cacheId)) {
                $options = unserialize($optionsCache);
            }
        }

        if ($options == false) {
            $options = $this->getCountryCollection()->toOptionArray();
            if ($useCache) {
                Mage::app()->saveCache(serialize($options), $cacheId, $cacheTags);
            }
        }
        return $options;
    }
    
    /*public function getFee(){
    	return Mage::getStoreConfig('affiliateplus_payment/offline/fee_value');
    }
    
    public function getFeeFormated(){
    	$fee = $this->getFee();
    	if (Mage::getStoreConfig('affiliateplus_payment/offline/fee_type') == 'percentage'){
    		return sprintf("%.2f",$fee).'%';
    	}else {
    		return Mage::app()->getStore()->getBaseCurrency()->format($fee);
    	}
    }*/
}
<?php
class Devinc_Occ_Block_Occ extends Mage_Checkout_Block_Onepage_Abstract
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    //sets the current url as the redirect url in case the session expires or occ returns errors
    public function setRedirectUrl() 
    {
    	$url = $this->helper('core/url')->getCurrentUrl();
		Mage::getModel('core/cookie')->set('redirect_url', $url, 86400);
    }

	//returns the billing or shipping dropdowns
    public function getAddressesHtmlSelect($type)
    {
        return Mage::getModel('occ/occ')->getAddressesHtmlSelect($type);
    }	 
    
    //retrieves the default address id for either the billing or shipping address
    public function getDefaultAddressId($type)
    {
        return Mage::getModel('occ/occ')->getDefaultAddressId($type);    	
    }
    
    public function loadShippingAddress() {
    	$product = Mage::registry('current_product');
    	if ($product) {
    		if ($product->isVirtual() && !Mage::getModel('occ/occ')->hasNonVirtualItems()) {
    			return false;
    		}
    	} else if (!Mage::getModel('occ/occ')->hasNonVirtualItems()) {
    		return false;
    	}
    	
    	return true;
    }
    
    //returns the occ initialize url with all the add to cart parameters
    public function getInitUrl()
    {
    	$product = Mage::registry('current_product');   
    	if ($product) {
			$block = new Mage_Catalog_Block_Product_View;   	  	
    		$addToCartUrl = $block->getSubmitUrl($product);
    	
			$params = Mage::helper('occ')->getUrlParams($addToCartUrl); 
		} else {
			$params = array();
		}
		
		return $this->getUrl('occ/index/init', $params);
    }   
    
    public function getSuccessUrl()
    {
    	$product = Mage::registry('current_product');   
    	if ($product) {    	
    		return $this->getUrl('occ/index/success');
    	} else {
    		return $this->getUrl('checkout/onepage/success');
    	}
    }    

    public function isAwAjaxCartEnabled() {
        return Mage::helper('core')->isModuleEnabled('AW_Ajaxcartpro');
    }
    
}
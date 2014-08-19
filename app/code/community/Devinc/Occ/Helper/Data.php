<?php

class Devinc_Occ_Helper_Data extends Mage_Core_Helper_Abstract
{
	//check if extension is enabled
	public static function isEnabled()
	{
		$storeId = Mage::app()->getStore()->getId();
		$isModuleEnabled = Mage::getStoreConfig('advanced/modules_disable_output/Devinc_Occ');
		$isEnabled = Mage::getStoreConfig('occ/configuration/enabled', $storeId);
		return ($isModuleEnabled == 0 && $isEnabled == 1);
	}
	
	//allow occ to run only for selected customer groups
	public function allowCustomerGroup()
	{
		$allowedGroups = explode(',',Mage::getStoreConfig('occ/configuration/customer_groups'));
		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if (empty($allowedGroups) || !in_array((string)$groupId, $allowedGroups)) {
        	return false;
        }
        
        return true;
	}
	
	//returns the parameters of a url in array format
	public function getUrlParams($_url)
	{
		$baseUrl = Mage::getBaseUrl();
		$path = str_replace($baseUrl, '', $_url);
		$paramsArray = explode('/', $path);
		unset($paramsArray[count($paramsArray)-1]);
		unset($paramsArray[0]);
		unset($paramsArray[1]);
		unset($paramsArray[2]);
		$paramsArray = array_merge(array(), $paramsArray);
		
		$params = array();
		for ($i = 0; $i<count($paramsArray); $i=$i+2) {
			$params[$paramsArray[$i]] = $paramsArray[$i+1];
		}
		
		return $params;
	}

	public function getMagentoVersion() {
		return (int)str_replace(".", "", Mage::getVersion());
    }

	/**
	 * returns true if the edition of Magento is Enterprise
	 * @return boolean
	 */
	public function isMagentoEnterprise() {
	    return Mage::getConfig ()->getModuleConfig ('Enterprise_Enterprise') && Mage::getConfig ()->getModuleConfig ('Enterprise_AdminGws') && Mage::getConfig ()->getModuleConfig ('Enterprise_Checkout') && Mage::getConfig ()->getModuleConfig ('Enterprise_Customer');
	}

	public function getBlockNameByType($type) 
	{
		$occBlocks = unserialize(Mage::getSingleton('customer/session')->getOccBlocks());
		
		if (isset($occBlocks[$type])) {
			return $occBlocks[$type];
		} else {
			return false;
		}
	}
    
    public function isMobile()
    {           
        // if (Mage::getModel('license/module')->isMobile()) {
        //     return true;
        // }
        
        return false;
    }    
    
    public function isTablet()
    {           
        // if (Mage::getModel('license/module')->isTablet()) {
        //     return true;
        // }
        
        return false;
    }       
        
    public function getCartHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

	    Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        if (Mage::helper('core')->isModuleEnabled('AW_Points') && Mage::getStoreConfig('points/general/enable')) {
            $update->load('occ_index_awpointscart');
	        $layout = $_controller->getLayout();
	        $update = $layout->getUpdate();
	        $layout->generateXml();
	        $layout->generateBlocks();
        } else {
	        $update->addHandle('default');
	        $update->addUpdate('<remove name="messages"/>');
	        $update->load();
	        $layout->generateXml();
	        $layout->generateBlocks();
	    }

        $output = array();
		if ($this->getBlockNameByType('checkout/cart_sidebar')) {
		    foreach ($this->getBlockNameByType('checkout/cart_sidebar') as $block) {
		        if ($layout->getBlock($block)) {
			        $output[] = $layout->getBlock($block)->toHtml();
			    }  
		    }
		}
        Mage::getSingleton('customer/session')->setOccRequest();
        
        return $output;
    }    
}
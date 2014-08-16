<?php

class Magestore_Seonavigation_Model_Observer
{
	/**
	 * get module helper
	 * 
	 * @return Magestore_Seonavigation_Helper_Data
	 */
	public function getHelper(){
		return Mage::helper('seonavigation');
	}
	
	public function cmsIndexNoroute($observer){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seonavigation')){return;}
		if (!$this->getHelper()->getConfig('enable')) return $this;
		$action = $observer->getEvent()->getControllerAction();
		$requestPath = trim($action->getRequest()->getRequestString(),'/');
		
		$urlRewrite = Mage::getResourceModel('seonavigation/seonavigation_collection')
			->addFieldToFilter('request_path',$requestPath)
			->getFirstItem();
		
		if ($urlRewrite && $urlRewrite->getId()){
			$url = Mage::getUrl(null,array('_direct' => $urlRewrite->getClearUrl()));
			$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
			$action->getResponse()->setRedirect($url);
		}
	}
}
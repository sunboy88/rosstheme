<?php

class Magestore_Seonavigation_Block_Catalog_Layer_State extends Mage_Catalog_Block_Layer_State
{
	/**
	 * get module helper
	 * 
	 * @return Magestore_Seonavigation_Helper_Data
	 */
	public function getSeoHelper(){
		return Mage::helper('seonavigation');
	}
	
	public function getClearUrl(){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seonavigation')){
			return parent::getClearUrl();
		}
		if ($this->getSeoHelper()->getConfig('enable')){
			$requestPath = trim($this->getRequest()->getRequestString(),'/');
			$model = Mage::getModel('seonavigation/seonavigation')->load($requestPath,'request_path');
			if ($model->getClearUrl())
				return Mage::getUrl(null,array('_direct' => $model->getClearUrl()));
		}
		return parent::getClearUrl();
	}
}

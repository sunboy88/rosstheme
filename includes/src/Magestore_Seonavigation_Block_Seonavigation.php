<?php

class Magestore_Seonavigation_Block_Seonavigation extends Mage_Core_Block_Template
{
	/**
	 * get module helper
	 * 
	 * @return Magestore_Seonavigation_Helper_Data
	 */
	public function getSeoHelper(){
		return Mage::helper('seonavigation');
	}
	
	public function _prepareLayout(){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seonavigation')){
			return parent::_prepareLayout();
		}
		$headBlock = $this->getLayout()->getBlock('head');
		$requestPath = trim($this->getRequest()->getRequestString(),'/');
		$model = Mage::getModel('seonavigation/seonavigation')->load($requestPath,'request_path');
		if ($this->getSeoHelper()->getConfig('enable') && $headBlock && $model->getId()){
			if ($title = $model->getMetaTitle())
				$headBlock->setTitle($title);
			if ($keywords = $model->getMetaKeywords())
				$headBlock->setKeywords($keywords);
			if ($description = $model->getMetaDescription())
				$headBlock->setDescription($description);
			if ($this->getSeoHelper()->getConfig('canonical_tag') && $model->getClearUrl())
				$headBlock->addLinkRel('canonical',Mage::getUrl(null,array('_direct' => $model->getClearUrl())));
		}
		return parent::_prepareLayout();
	}
}
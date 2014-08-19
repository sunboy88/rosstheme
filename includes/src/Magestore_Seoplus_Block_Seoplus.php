<?php

class Magestore_Seoplus_Block_Seoplus extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seoplus')){
			return parent::_prepareLayout();
		}
		$headBlock = $this->getLayout()->getBlock('head');
		$query = Mage::helper('catalogsearch')->getQuery();
		if (Mage::helper('seoplus')->getConfig('enable') && $headBlock && $query->getQueryText()){
			if ($title = $query->getMetaTitle())
				$headBlock->setTitle($title);
			if ($keywords = $query->getMetaKeywords())
				$headBlock->setKeywords($keywords);
			if ($description = $query->getMetaDescription())
				$headBlock->setDescription($description);
		}
		return parent::_prepareLayout();
	}
}
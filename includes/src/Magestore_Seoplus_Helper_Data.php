<?php

class Magestore_Seoplus_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getConfig($code, $store = null){
		return Mage::getStoreConfig("seoplus/general/$code",$store);
	}
	
	public function prepareTargetPath($queryName){
		return "catalogsearch/result/index/q/$queryName/";
	}
}
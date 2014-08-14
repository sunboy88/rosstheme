<?php

class Magestore_Seobytag_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfig($code, $store = null) {
        return Mage::getStoreConfig("seoplus/seobytag/$code", $store);
    }
    
    public function prepareTargetPath($tagId){
    	return "tag/product/list/tagId/$tagId/";
    }
}
<?php

class Eb_Ajaxcatalog_Helper_Data extends Mage_Core_Helper_Abstract
{
    // Get Enable Ajaxcatalog
    // Return config yes or no
    public function getConfigEnable(){
        return Mage::getStoreConfig('ajaxcatalog/general/enabled');
    }
    //Get Display Pager
    // Return Yes or No
    public function isPagerDisplay(){
        return Mage::getStoreConfig('ajaxcatalog/general/displaypage');
    }
}
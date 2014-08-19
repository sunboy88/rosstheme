<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Changestore Block
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Block_Changestore extends Mage_Core_Block_Template {
    
    
    public function getStoreList(){
        $stores = Mage::app()->getStores(true);
        return $stores;
    }
    
    /**
     * get current store id
     * return int store_id
     */
    public function currentStore(){
        $storeId  =  0;
        if(Mage::getSingleton('adminhtml/session')->getMobileStoreId() != ''){
            $storeId = Mage::getSingleton('adminhtml/session')->getMobileStoreId();
        }
        return $storeId;
    }
}

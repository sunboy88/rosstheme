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
 * Bestsellers Block
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Block_Bestsellers extends Mage_Core_Block_Template {
    
    /**
     * get time to show how many last days
     */
    public function getTitleTime(){
        return Mage::helper('simisalestrackingapi')->getBestsellersTitleTime();
    }
    
    /**
     * get time of Bestsellers updated
     * return string datetime db
     */
    public function getUpdatedTime(){
        $time = Mage::getSingleton('adminhtml/session')->getSalestrackingTimeRefreshBestsellers();
        if($time == ''){
            $time = Mage::getModel('simisalestrackingapi/settings')->getSetting('time_refresh_bestsellers');
        }
        if($time == ''){
            return '';
        }
        return Mage::helper('core')->formatDate($time, 'medium', true);
    }
    
    /**
     * check bestseller is old
     */
    public function isOld(){
        $order_ids = Mage::getResourceModel('simisalestrackingapi/bestsellers_orderchange')->getOrderIds();
        if(!empty($order_ids)){
            return true;
        }else{
            return false;
        }
    }
    
}

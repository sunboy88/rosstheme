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
 * Settings Block
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Block_Setting extends Mage_Core_Block_Template 
{
    public function limit(){
        return Mage::getModel('simisalestrackingapi/settings')->getSetting('limit');
    }
    
    public function selectedTime($value){
        $settings = Mage::getModel('simisalestrackingapi/settings');
        $time = $settings->getSetting(Magestore_Simisalestrackingapi_Model_Settings::_TIME_BESTSELLERS);
        if(is_null($time)) {
            $time = '30d';
            $settings->saveSetting($time, Magestore_Simisalestrackingapi_Model_Settings::_TIME_BESTSELLERS);
        }
        if($value == $time){
            return 'selected';
        }else{
            return '';
        }
    }
    
    
    /**
     * get orders status config
     * @return array
     */
    public function getOrderStatus(){
        return Mage::getSingleton('sales/order_config')->getStatuses();
    }
    
    public function getStatusConfig(){
        $settings = Mage::getModel('simisalestrackingapi/settings');
        $order_text = $settings->getSetting(Magestore_Simisalestrackingapi_Model_Settings::_STATUS_ORDERS);
        return explode(";", $order_text);
    }
}

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
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiSalestracking Api Dashboard Server Model
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Api_Settings extends Magestore_Simisalestrackingapi_Model_Api_Abstract
{
    /**
     * api page Dashboard
     */
    /**
     * call=settings &
     * params={
     *      date_range: 1d|7d|15d|30d|3m|6m|1y|2y|lt,
     *      order_status: string|array('pending','complete')
     * }
     */
    public function apiIndex($params){
        if(!isset($params['date_range']) && !isset($params['order_status'])){
            throw new Exception($this->_helper->__('Params is specified'), 23);
        }
        $settings = Mage::getModel('simisalestrackingapi/settings');
        if(isset($params['date_range']) && $params['date_range'] !=''){
            $settings->saveSetting($params['date_range'], Magestore_Simisalestrackingapi_Model_Settings::_TIME_BESTSELLERS);
        }
        if(isset($params['order_status'])){
            if(!is_array($params['order_status'])){
                $params['order_status'] = array($params['order_status']);
            }
            $settings->saveSetting(implode(";", $params['order_status']), Magestore_Simisalestrackingapi_Model_Settings::_STATUS_ORDERS);
        }
        //clear cache
        Mage::app()->getCacheInstance()->cleanType("config");
        return true;
    }
}

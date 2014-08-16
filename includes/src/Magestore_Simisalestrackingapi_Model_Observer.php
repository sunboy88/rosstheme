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
 * SalestrackingAPI Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Observer
{
    protected $_order_status_before = '';
    protected $_order_status_after = '';

    /**
     * log orders has changed status
     */
    public function logOrderChangeBefore($observer){
        $this->_order_status_before = $observer['order']->getStatus();
    }
    /**
     * log orders has changed status
     */
    public function logOrderChangeAfter($observer){
        try{
            $this->_order_status_after = $observer['order']->getStatus();
            if($this->_order_status_before != $this->_order_status_after ){//&& $this->_order_status_before != ''){
                $orderchange = Mage::getModel('simisalestrackingapi/bestsellers_orderchange')
                    ->load($observer['order']->getId(), 'order_id');
                
                $orderchange->addData(
                    array(
                        'order_id'=>$observer['order']->getId(),
                        'before_status'=>$this->_order_status_before,
                        'after_status'=>$this->_order_status_after
                    ));
                
                $orderchange->save();
            }
        } catch(Exception $e){
            Mage::logException($e);
        }
    }
}
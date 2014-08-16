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
 * Simisalestrackingapi Bestsellers Mysql4 Model
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Mysql4_Bestsellers_Orderchange extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('simisalestrackingapi/bestsellers_orderchange', 'id');
    }
    
    /**
     * clear all entries
     */
    public function clear(){
        $write = $this->_getWriteAdapter();
        $write->truncate($this->getTable('simisalestrackingapi/bestsellers_orderchange'));
        return $this;
        $write->beginTransaction();
        //delete all
        $write->delete($this->getTable('simisalestrackingapi/bestsellers_orderchange'));
        $write->commit();
    }
    
    
    /**
     * get order ids
     * @return 
     */
    public function getOrderIds(){
        $db = $this->_getReadAdapter();
        $sql = "SELECT *  
                FROM ".$this->getTable('simisalestrackingapi/bestsellers_orderchange');
        $order_ids = $db->fetchPairs($sql); //array
        return $order_ids;
    }
}

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
 * Orders Block
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Block_Orders extends Mage_Core_Block_Template {
    
    protected $_sql = ''; //sql
    protected $_newOrders = array(); //array('id'=>'1');
    protected $_collection = '';
    protected $_total = 0; //total of all order show


    /**
     * count orders collection number of this
     */
    public function countOrders(){
        if($this->_collection){
            //return $this->_collection->getSize();
            return $this->_collection->getSize();
        }
        return 0;
    }
    
    /**
     * get orders status config
     * @return array
     */
    public function getOrderStatus(){
        return Mage::getSingleton('sales/order_config')->getStatuses();
    }
    
    /**
     * set collection for block
     * @param order $collection
     * @return Magestore_Simisalestrackingapi_Block_Orders
     */
    public function setCollection($collection){
        $this->_collection = $collection;
        $this->_sql = clone $this->_collection->getSelect();
        return $this;
    }

        /**
     * get total sales in list orders with refurn of status complete have refurn
     * @return int
     */
    public function getTotalSales(){
        //$resource = Mage::getSingleton('core/resource');
        //$readConnection = $resource->getConnection('core_read');
        //$rows = $readConnection->fetchAll($this->_sql);
        $query = clone $this->_sql;
        $query->reset(Zend_Db_Select::COLUMNS)
            ->columns(array('status','base_total_paid','base_total_refunded','base_grand_total'));
        Mage::getSingleton('core/resource_iterator')->walk(
            $query,
            array(array($this, 'calcTotalCallback')),
            array()
        );
        return $this->_total;
    }
    
    public function calcTotalCallback($args)
    {
        //Zend_debug::dump($args['row']);die;
        $r = $args['row'];
        $total = $this->_total;
        if($r['status'] == 'complete'){
            if($r['base_total_refunded'] == ''){
                $total += $r['base_total_paid'];
            }else{
                $total += $r['base_total_paid'] - $r['base_total_refunded'];
            }
        }else{
            $total += $r['base_grand_total'];
        }
        $this->_total = $total;
    }
    
    /**
     * bind new order static
     */
    public function _bindNewOrders(){
        $neworders = Mage::helper('simisalestrackingapi')->getNewOrderCollection();
        $neworders_last = Mage::getSingleton('adminhtml/session')->getNewOrders();
        $newUnreads = Mage::getSingleton('adminhtml/session')->getNewOrderUnreads();
        $productsCollection = $neworders;
        $productsCollection->setPageSize(100);
        $pages = $productsCollection->getLastPageNumber();
        $currentPage = 1;
        do {
            $productsCollection->setCurPage($currentPage);
            $productsCollection->load();

            foreach ($productsCollection as $order) {
                if(!isset($neworders_last[$order->getId()])){
                    $neworders_last[$order->getId()] = '1';
                }
                $this->_newOrders = $neworders_last;
                $newUnreads[$order->getId()] = '1';
            }
            $currentPage++;
            //clear collection and free memory
            $productsCollection->clear();
        } while ($currentPage <= $pages);
        
        Mage::getSingleton('adminhtml/session')->setNewOrders($neworders_last);
        Mage::getSingleton('adminhtml/session')->setNewOrderUnreads($newUnreads);
        //reset time after bind new
        Mage::helper('simisalestrackingapi')->resetTimeNewOrders();
    }

    /**
     * check order is new or not
     * $id id order
     * @return type
     */
    public function isNew($id){
        if(isset($this->_newOrders[$id])){
            if($this->_newOrders[$id] == '1'){
                //remove item new is show
                unset($this->_newOrders[$id]);
                $neworders_last = Mage::getSingleton('adminhtml/session')->getNewOrders();
                if(isset($neworders_last[$id])){
                    $neworders_last[$id] = '0';
                }
                Mage::getSingleton('adminhtml/session')->setNewOrders($neworders_last);
                return true;
            }
        }
        return false;
    }
    
    /**
     * check is unread new order
     * @param type $id
     * @return boolean
     */
    public function isUnread($id){
        $newUnreads = Mage::getSingleton('adminhtml/session')->getNewOrderUnreads();
        if(isset($newUnreads[$id])){
            if($newUnreads[$id] == '1'){
                return true;
            }
        }
        return false;
    }
}

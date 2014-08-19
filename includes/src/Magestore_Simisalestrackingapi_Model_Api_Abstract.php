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
 * SimiSalestracking API Resource Abstract
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
abstract class Magestore_Simisalestrackingapi_Model_Api_Abstract
{
    /**
     * @var Magestore_Simisalestracking_Helper_Data
     */
    protected $_helper;
    protected $mainTable = 'main_table';
    protected $_list_ids = array();


    public function __construct() {
        $this->_helper = Mage::helper('simisalestrackingapi');
        $this->mainTable = $this->_helper->getMainTable();
    }
    
    /**
     * bild and get store list with current store
     */
    public function getStoreList(){
        $changeStoreBlock = Mage::getBlockSingleton('simisalestrackingapi/changestore');
        $stores = $changeStoreBlock->getStoreList();
        $current = $changeStoreBlock->currentStore();
        $storeList = array();
        foreach ($stores as $st){
            if($st->getCode()==='admin'){
                $name = Mage::helper('simisalestrackingapi')->__('All Store Views');
            }else{
                $name = $st->getName();
            }
            $storeList[] = array('id'=>(int)$st->getId(), 'name'=>$name, 'cur_id'=>(int)$current);
        }
        return $storeList;
    }
    
    /**
     * convert collection of orders to data array
     */
    public function convertOrderData($collection, $group = '0'){
        $this->_helper->resetTimeNewOrders();
        //print_r($collection->getSelectSql(true)); die;
        // $big_data = $collection->getData();//zend_debug::dump($big_data); die;
        $data = array();
        $order_block = Mage::getBlockSingleton('simisalestrackingapi/orders');
        //$order_block->_bindNewOrders();
        if($group){
            $pre_date = '';
            $item_group = array();
            $ids_temp = array();
            foreach ($collection as $order_item){
            	$order = $order_item->getData();
                // $order_item = Mage::getModel('sales/order')->load($order['entity_id']);
                $ids_temp[] = $order['entity_id']; //list ids for next
                //$this->_helper->readNewOrder($order['entity_id']);//order is new read
                $items = $order_item->getItemsCollection();
                $sku = $items->getFirstItem()->getSku();
                if(count($items) > 1){
                    $sku .= ', ...';
                }
                $is_new = 0;
                $is_unread = 0;
                //if($order_block->isNew($order['entity_id'])){
                //    $is_new = 1;
                //}
                //if($order_block->isUnread($order['entity_id'])){
                //    $is_unread = 1;
                //}
                //$sub_str_date = substr(Mage::helper('core')->formatDate($order['updated_at'], 'short', true), 0, 10);
                //$zdate = Mage::app()->getLocale()->date();//new Zend_Date($order['updated_at'],Zend_Date::ISO_8601);
                //$zdate->setDate(new Zend_Date($order['updated_at'],Zend_Date::ISO_8601))->setTimezone('Etc/UTC');
                $zdate = Mage::app()->getLocale()->date(strtotime($order['updated_at']), null, null, true);
                if( $pre_date != ''){
                    if($zdate->compareDay($pre_date) !== 0 ){
                        $data[] = array(
                            'date_group'=> $pre_date->toString(Zend_Date::DATE_MEDIUM),//$item_group[0]['group_date'],
                            'items'     => $item_group
                        );
                        unset($item_group);
                        $item_group = array();
                    }
                }
                $item_group[] = array(
                    'id'                => (int)$order['entity_id'],
                    'customer_name'     => $order['customer_firstname'].' '.$order['customer_middlename'].' '.$order['customer_lastname'],//->getCustomerName(),
                    'customer_email'    => $order['customer_email'],//->getCustomerEmail(),
                    'increment'         => $order['increment_id'],//->getIncrementId(),
                    //'group_date'        => Mage::helper('core')->formatDate($order->getUpdatedAt(), 'medium', false),
                    'date'              => Mage::helper('core')->formatDate($order['updated_at'], 'medium', true),
                    'grand_total'       => Mage::helper('core')->currency($order['base_grand_total'], true, false),
                    'status'            => $order['status'],//->getStatus(),
                    'sku'               => $sku,
                    'is_new'            => $is_new,
                    'is_unread'         => $is_unread
                );
                $pre_date = clone $zdate;
            }
            $this->_list_ids = $ids_temp;
            unset($ids_temp);
            if(count($item_group)>0){
                $data[] = array(
                    'date_group'=> $pre_date->toString(Zend_Date::DATE_MEDIUM),
                    'items'     => $item_group
                );
            }
        }else{
            $ids_temp = array();
            foreach ($collection as $order_item){
            	$order = $order_item->getData();
                // $order_item = Mage::getModel('sales/order')->load($order['entity_id']);
                $ids_temp[] = $order['entity_id']; //list ids for next
                //$this->_helper->readNewOrder($order['entity_id']);//order is new read
                $items = $order_item->getItemsCollection();
                $sku = $items->getFirstItem()->getSku();
                if(count($items) > 1){
                    $sku .= ', ...';
                }
                $is_new = 0;
                $is_unread = 0;
                //if($order_block->isNew($order['entity_id'])){
                //    $is_new = 1;
                //}
                //if($order_block->isUnread($order['entity_id'])){
                //    $is_unread = 1;
                //}
                $data[] = array(
                    'id'                => (int)$order['entity_id'],
                    'customer_name'     => $order['customer_firstname'].' '.$order['customer_middlename'].' '.$order['customer_lastname'],//->getCustomerName(),
                    'customer_email'    => $order['customer_email'],//->getCustomerEmail(),
                    'increment'         => $order['increment_id'],//->getIncrementId(),
                    //'group_date'        => Mage::helper('core')->formatDate($order->getUpdatedAt(), 'medium', false),
                    'date'              => Mage::helper('core')->formatDate($order['updated_at'], 'medium', true),
                    'grand_total'       => Mage::helper('core')->currency($order['base_grand_total'], true, false),
                    'status'            => $order['status'],//->getStatus(),
                    'sku'               => $sku,
                    'is_new'            => $is_new,
                    'is_unread'         => $is_unread
                );
            }
            $this->_list_ids = $ids_temp;
            unset($ids_temp);
        }
        // unset($big_data);
        return $data;
    }
    
    public function getAllOrderIds($collection){
        //get all order ids
        //print_r((string)$collection->getSelect()); die;
        $_select = clone $collection->getSelect();
        $_select->reset(Zend_Db_Select::COLUMNS)
                ->reset(Zend_Db_Select::ORDER)->columns('entity_id');
        $_select->order("{$this->mainTable}.updated_at DESC")
                ->order("{$this->mainTable}.entity_id DESC");
        $_select->limit(1000);
        //print_r((string)$_select); die;
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $all_order_ids = $db->fetchCol($_select); // array date to clear index
        return $all_order_ids;
    }
}

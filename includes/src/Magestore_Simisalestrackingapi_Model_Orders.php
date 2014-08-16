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
 * Simisalestrackingapi Orders Model
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Orders extends Mage_Core_Model_Abstract
{
    protected $_main_table = 'main_table'; //default table alias in database
    protected $_helper;

    //filter
    
    
    public function __construct(){
        $this->_helper = Mage::helper('simisalestrackingapi');
        $this->_main_table = $this->_helper->getMainTable();
    }
    /**
     * get all orders
     * @param type $filter
     * @return collection order
     */
    public function getOrders($filter = ''){
        $from = Mage::app()->getLocale()->date();
        $from->setYear(1970);
        return $this->get3MonthsAgo($from,'',$filter);
    }
    
    
    /**
     * get today data tab
     * @param type $filter
     * @return type
     */
    public function getToday($filter = ''){
        $fromDate = Mage::app()->getLocale()->date();
        $fromDate->setTime('00:00:00');
        return $this->get3MonthsAgo($fromDate,'', $filter);
    }
    /**
     * get yesterday data tab
     * @param type $filter
     * @return type
     */
    public function getYesterday($filter = ''){
        $fromDate = Mage::app()->getLocale()->date();
        $fromDate->subDate(1);
        $toDate = clone $fromDate;
        $fromDate->setTime('00:00:00');
        $toDate->setTime('23:59:59');
        return $this->get3MonthsAgo($fromDate, $toDate, $filter);
    }
    
    public function getThisWeek($filter = ''){
        $fromDate = Mage::app()->getLocale()->date();
        $fromDate->setWeekday(1);
        $fromDate->setTime('00:00:00');
        return $this->get3MonthsAgo($fromDate, '', $filter);
    }
    
    public function getLastWeek($filter = ''){
        $fromDate = Mage::app()->getLocale()->date();
        $toDate = clone $fromDate;
        $fromDate->subWeek(1)->setWeekday(1)->setTime('00:00:00');
        $toDate->setWeekday(1)->subDate(1)->setTime('23:59:59');
        return $this->get3MonthsAgo($fromDate,$toDate,$filter);
    }
    
    public function getThisMonth($filter = ''){
        $fromDate = Mage::app()->getLocale()->date();
        $fromDate->setDay(1);
        $fromDate->setTime('00:00:00');
        return $this->get3MonthsAgo($fromDate,'', $filter);
    }
    
    public function getLastMonth($filter = ''){
        $fromDate = Mage::app()->getLocale()->date();
        $toDate = clone $fromDate;
        $fromDate->subMonth(1);
        $fromDate->setDay(1);
        $fromDate->setTime('00:00:00');
        $toDate->setDay(1);
        $toDate->subDate(1);
        $toDate->setTime('23:59:59');
        return $this->get3MonthsAgo($fromDate,$toDate, $filter);
    }
    
    public function get2MonthsAgo($filter = ''){
        $fromDate = Mage::app()->getLocale()->date();
        $fromDate->subMonth(2);//convert to GMT time
        return $this->get3MonthsAgo($fromDate, '', $filter);
    }
    
    public function get3MonthsAgo($fromDate = '', $toDate = '', $filter = ''){
        $collection = $this->getOrderCollection();
        $currentDate = Mage::app()->getLocale()->date();
        if($fromDate == ''){
            $fromDate = clone $currentDate;
            $fromDate->subMonth(3)->setDate(1);
        }
        if($toDate != ''){
            $currentDate = $toDate;
        }
        if($this->_helper->version(2)){
            $collection->addAttributeToFilter("updated_at", array(
            'from'=>gmdate("Y-m-d H:i:s", $fromDate->getTimestamp()),
            'to'=>gmdate("Y-m-d H:i:s", $currentDate->getTimestamp())
            ));
        }else{
            $collection->addAttributeToFilter("{$this->_main_table}.updated_at", array(
            'from'=>gmdate("Y-m-d H:i:s", $fromDate->getTimestamp()),
            'to'=>gmdate("Y-m-d H:i:s", $currentDate->getTimestamp())
            ));
        }
        $collection = $this->getCollectionFilter($collection, $filter);
        //sort list
        $collection->getSelect()->order("{$this->_main_table}.updated_at DESC");
        //print_r($collection->getSelectSql(true)); die;
        return $collection;
    }
    
    public function searchOrder($from_date = '', $to_date = '', $pro_sku = '', $status = '', $from_val = '', $to_val = '', $filter = ''){
        $collection = $this->getOrderCollection();
        //sort list
        $collection->getSelect()->order("{$this->_main_table}.updated_at DESC");
        //fill by date
        $cur_date = Mage::app()->getLocale()->date();
        $fromDate = clone $cur_date;
        $fromDate->setDate('1970-01-01','yyyy-MM-dd');
        $fromDate->setTime('00:00:00');
        if($from_date != ''){
            $fromDate->setDate($from_date, 'yyyy-MM-dd');
            $fromDate->setTime('00:00:00');
        }
        if($to_date != ''){
            $cur_date->setDate($to_date, 'yyyy-MM-dd');
            $cur_date->setTime('23:59:59');
        }
        if($from_date != '' || $to_date != ''){
            $collection->getSelect()
                ->where($this->_main_table.'.updated_at >= ?', gmdate("Y-m-d H:i:s", $fromDate->getTimestamp()))
                ->where($this->_main_table.'.updated_at <= ?', gmdate("Y-m-d H:i:s", $cur_date->getTimestamp()));
        }
        //fill by product sku
        if($pro_sku != ''){
            $collection->getSelect()
                ->joinLeft(
                    array('order_item' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                    "{$this->_main_table}.entity_id = order_item.order_id",
                    array('order_item.*'));
            $collection->getSelect()
                ->where(
                    "order_item.sku like '%{$pro_sku}%' 
                    OR order_item.name like '%{$pro_sku}%'"
                )
                ->group("{$this->_main_table}.entity_id");
        }
        //fill by from value
        if($from_val != ''){
            if($this->_helper->version(2)){
                $collection->addAttributeToFilter("base_grand_total", array('gteq'=>$from_val));
            }else{
                $collection->addAttributeToFilter("{$this->_main_table}.base_grand_total", array('gteq'=>$from_val));
            }
        }
        //fill by to value
        if($to_val != ''){
            if($this->_helper->version(2)){
                $collection->addAttributeToFilter("base_grand_total", array('lteq'=>$to_val));
            }else{
                $collection->addAttributeToFilter("{$this->_main_table}.base_grand_total", array('lteq'=>$to_val));
            }
        }
        
        $collection = $this->getCollectionFilter($collection, $filter);
        
        return $collection;
    }
    
    /**
     * This function get order collection by product id for action call from bestseller
     * get orders by product Id
     * @param type $id
     */
    public function getOrderByProduct($id, $from_date = '', $to_date = '', $status = '', $from_val = '', $to_val = '', $filter = ''){
        $collection = $this->getOrderCollection();
        //sort list
        $collection->getSelect()->order("{$this->_main_table}.updated_at DESC");
        //filter for bestsellers
        //fill by date
        $cur_date = Mage::app()->getLocale()->date();
        $fromDate = clone $cur_date;
        $fromDate->setDate('1970-01-01','yyyy-MM-dd');
        $fromDate->setTime('00:00:00');
        if($from_date != ''){
            if(is_object($to_date)){
                $fromDate = $to_date;
            }else{
                $fromDate->setDate($from_date, 'yyyy-MM-dd');
                $fromDate->setTime('00:00:00');
            }
        }
        if($to_date != ''){
            if(is_object($from_date)){
                $fromDate = $from_date;
            }else{
                $cur_date->setDate($to_date, 'yyyy-MM-dd');
                $cur_date->setTime('23:59:59');
            }
        }
        if($from_date != '' || $to_date != ''){
            $collection->getSelect()
                ->where($this->_main_table.'.updated_at >= ?', gmdate("Y-m-d H:i:s", $fromDate->getTimestamp()))
                ->where($this->_main_table.'.updated_at <= ?', gmdate("Y-m-d H:i:s", $cur_date->getTimestamp()));
        }
        //fill by status
        if($status != ''){
            $collection->addAttributeToFilter("status", $status);
        }
        //fill by from value
        if($from_val != ''){
            $collection->addAttributeToFilter("base_grand_total", array('gteq'=>$from_val));
        }
        //fill by to value
        if($to_val != ''){
            $collection->addAttributeToFilter("base_grand_total", array('lteq'=>$to_val));
        }
        // Filter order by Product ID
        $collection->getSelect()
            ->joinInner(
                array('order_item' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                "{$this->_main_table}.entity_id = order_item.order_id"
                . " AND order_item.product_id = '$id'",
                array()
            )->group("{$this->_main_table}.entity_id");
        
        $collection = $this->getCollectionFilter($collection, $filter);
        return $collection;
        
        //fill by product id
        $collection->getSelect()
            ->joinRight(
                array('order_item' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                "{$this->_main_table}.entity_id = order_item.order_id",
                array(''))
            ->joinLeft(
                array('order_item2' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                'order_item.parent_item_id = order_item2.item_id',
                array('order_item2.product_type'));
        $collection->getSelect()
            ->where("order_item.product_id = '{$id}'")// and {$this->_main_table}.status != 'canceled'")
            ->where("(order_item2.product_type != 'configurable' AND order_item2.product_type != 'bundle') OR order_item.parent_item_id IS NULL")
            ->group("{$this->_main_table}.entity_id");
        
        $collection = $this->getCollectionFilter($collection, $filter);
        return $collection;
    }
    
    /**
     * get orders by date
     * @param type $d
     * @return collection data
     */
    public function getOrdersByDate($date, $filter = '') {
        $collection = $this->getOrderCollection();
        //sort list
        $collection->getSelect()->order("{$this->_main_table}.updated_at DESC");
        //fill by date
        $cur_date = Mage::app()->getLocale()->date();
        $fromDate = clone $cur_date;
        $fromDate->setTime('00:00:00');
        if($date != ''){
            $fromDate->setDate($date, 'dd/MM/yyyy');
            $cur_date->setDate($date, 'dd/MM/yyyy');
        }
        $cur_date->setTime('23:59:59');
        $collection->getSelect()
            ->where($this->_main_table.'.updated_at >= ?', gmdate("Y-m-d H:i:s", $fromDate->getTimestamp()))
            ->where($this->_main_table.'.updated_at <= ?', gmdate("Y-m-d H:i:s", $cur_date->getTimestamp()));
        
        $collection = $this->getCollectionFilter($collection, $filter);
        //print_r($collection->getSelectSql(true)); die;
        return $collection;
    }
    
    public function getOrderCollection(){
        $collection = Mage::getResourceModel('sales/order_collection');
        $storeId = Mage::helper('simisalestrackingapi')->currentStoreId();
        if($storeId != 0){
            if($this->_helper->version(2)){
                $collection->addAttributeToFilter('store_id', $storeId);
            }else{
                $collection->addAttributeToFilter("{$this->_main_table}.store_id", $storeId);
            }
        }
        //fill by status
        $status = Mage::helper('simisalestrackingapi')->getOrderStatus();
        if(!empty($status)){
            if(is_array($status)){
                $status = implode("', '", $status); //convert to SQL
            }
            $collection->getSelect()->where(
                "{$this->_main_table}.status in ('{$status}')"
            );
        }else{
            $collection->getSelect()->where(
                "{$this->_main_table}.status in ('')" //fix array empty
            );
        }
        return $collection;
    }
    
    public function getCountNew(){
    	return count($this->getNewOrder());
    }
    
    /**
     * get new order collection
     * @return type
     */
    public function getNewOrder(){
    	if ($this->hasData('_new_order_collection')) {
    		return $this->getData('_new_order_collection');
    	}
        $lastTime = Mage::helper('simisalestrackingapi')->getTimeNewOrders();
        $collection = Mage::getResourceModel('sales/order_collection');
        //sort list
        $collection->getSelect()->order("{$this->_main_table}.updated_at", 'desc');
        //filter new orders
        $collection->addAttributeToFilter('created_at', array('from' => $lastTime));
        //filter store
        $storeId = Mage::helper('simisalestrackingapi')->currentStoreId();
        if($storeId != 0){
            $collection->addAttributeToFilter('store_id', $storeId);
        }
        $this->setData('_new_order_collection', $collection);
        return $collection;
    }
    
    /**
     * get new sales for new order number
     * @return string price
     */
    public function getNewSales(){
        $sales = 0;
        $collection = $this->getNewOrder();
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->columns('base_grand_total');
        foreach($collection as $item){
            $sales += $item->getBaseGrandTotal();
        }
        return Mage::helper('core')->currency($sales, true, false);
    }
    
    protected function getCollectionFilter($collection, $filter = ''){
        if($this->_helper->version(2)){//1.4.0.1
            $collection->getSelect()
                ->joinLeft( 
                    array('cus'=>'customer_entity'),
                    'e.customer_id = cus.entity_id',
                    array('customer_email'=>'cus.email',
                        'cus_entity_type_id'=>'cus.entity_type_id',
                        'cus_entity_id'=>'cus.entity_id')
                )
                ->joinLeft(
                    array('eav1'=>'eav_attribute'),
                    "cus.entity_type_id = eav1.entity_type_id AND eav1.attribute_code = 'firstname'",
                    array('eav1_attribute_id'=>'eav1.attribute_id') //attribute_id fistname
                )
                ->joinLeft(
                    array('eav2'=>'eav_attribute'),
                    "cus.entity_type_id = eav2.entity_type_id AND eav2.attribute_code = 'lastname'",
                    array('eav2_attribute_id'=>'eav2.attribute_id') //attribute_id lastname
                )
                ->joinLeft(
                    array('eav3'=>'eav_attribute'),
                    "cus.entity_type_id = eav3.entity_type_id AND eav3.attribute_code = 'middlename'",
                    array('eav3_attribute_id'=>'eav3.attribute_id') //attribute_id midname
                )
                ->joinLeft(
                    array('cus_eav1'=>'customer_entity_varchar'),
                    "cus_eav1.attribute_id = eav1.attribute_id AND cus_eav1.entity_id = cus.entity_id",
                    array('customer_firstname'=>'cus_eav1.value') //first name
                )
                ->joinLeft(
                    array('cus_eav2'=>'customer_entity_varchar'),
                    "cus_eav2.attribute_id = eav2.attribute_id AND cus_eav2.entity_id = cus.entity_id",
                    array('customer_lastname'=>'cus_eav2.value') //last name
                )
                ->joinLeft(
                    array('cus_eav3'=>'customer_entity_varchar'),
                    "cus_eav3.attribute_id = eav3.attribute_id AND cus_eav3.entity_id = cus.entity_id",
                    array('customer_middlename'=>'cus_eav3.value') //middle name
                )
                ->group('e.entity_id');
            //filter
            if($filter != ''){
                $filter = '%'.$filter.'%';
                $collection->getSelect()
                    ->joinLeft(
                        array('order_item' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                        "{$this->_main_table}.entity_id = order_item.order_id",
                        array('order_item.*'));
                $collection->getSelect()
                    ->where(
                        "{$this->_main_table}.increment_id like '$filter'"
                        . "OR cus.email like '$filter'"
                        . "OR CONCAT(cus_eav1.value,' ',cus_eav3.value,' ',cus_eav2.value) like '$filter'"
                        . "OR `{$this->_main_table}`.`base_grand_total` like '{$filter}'"
                        //filter with product
                        . "OR order_item.sku like '{$filter}'"
                        . "OR order_item.name like '{$filter}'"
                    )
                    ->group("{$this->_main_table}.entity_id");
            }
        }else{
            //filter
            if($filter != ''){
                $filter = '%'.$filter.'%';
                $collection->getSelect()
                    ->joinLeft(
                        array('order_item' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                        "{$this->_main_table}.entity_id = order_item.order_id",
                        array('order_item.*'));
                $collection->getSelect()
                    ->where(
                        "{$this->_main_table}.increment_id like '$filter'"
                        . "OR {$this->_main_table}.customer_email like '$filter'"
                        . "OR CONCAT(`{$this->_main_table}`.`customer_firstname`,' ',`{$this->_main_table}`.`customer_lastname`) like '$filter'"
                        . "OR `{$this->_main_table}`.`base_grand_total` like '{$filter}'"
                        //filter with product
                        . "OR order_item.sku like '{$filter}'"
                        . "OR order_item.name like '{$filter}'"
                    )
                    ->group("{$this->_main_table}.entity_id");

            }
        }
        return $collection;
    }
}

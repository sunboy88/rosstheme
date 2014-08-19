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
 * Simisalestrackingapi Bestsellers Model
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Bestsellers extends Mage_Core_Model_Abstract
{
    protected $_orderCollection = false;
    public $_period = array();
    protected $_collection = false;
    protected $_order_by = 'sales';
    protected $_helper = '';
    protected $_main_table;
    
    public function __construct()
    {
        $this->_helper = Mage::helper('simisalestrackingapi');
        $this->_main_table = $this->_helper->getMainTable();
        $this->_collection = Mage::getResourceModel('simisalestrackingapi/bestsellers_daily_collection');
    }

    /**
     * get order to collect datas
     * @param bool $is_all if true is all orders
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function _getOrderCollection($is_all = false){
        $collection = Mage::getModel('sales/order')->getCollection();
        if(!$is_all){
            $order_ids = Mage::getResourceModel('simisalestrackingapi/bestsellers_orderchange')->getOrderIds();
            if(empty($order_ids)){
                $order_ids = -1;
                $collection->getSelect()->where("{$this->_main_table}.entity_id < 0");
            } else {
                $collection->getSelect()
                        ->where("{$this->_main_table}.entity_id in (?)", array($order_ids));//fix all versions
            }
        }
        if($collection){
            $this->_orderCollection = $collection;
        }
        return $collection;
    }

    /**
     * return array data
     */
    public function refresh($all = false){
    	// Start refresh bestseller
    	$resource = Mage::getSingleton('core/resource');
    	$select = Mage::getResourceModel('sales/order_collection')->getSelect();
    	$select->reset()->from(array('e' => $resource->getTableName('sales/order')), array(
    	   'created_at'    => 'e.created_at',
    	   'updated_at'    => 'e.updated_at',
    	   'status'        => 'e.status',
    	   'store_id'      => 'e.store_id',
    	   'id'            => 'i.item_id',
    	   'product_id'    => 'i.product_id',
    	   'product_name'  => 'IFNULL(pn.value, i.name)',
    	   'sku'           => 'i.sku',
    	   'qty'           => "IF(e.status = 'complete', i.qty_invoiced - i.qty_refunded, i.qty_ordered)",
    	   'sales'         => "IF(e.base_subtotal > 0.0001 AND i.qty_ordered > 0.0001, "
    	                       . "IF(e.status = 'complete', i.qty_invoiced - i.qty_refunded"
    	                           . ", IF(e.status = 'canceled', i.qty_ordered, i.qty_ordered - i.qty_canceled)) / i.qty_ordered "
    	                       . "* IF(p.product_type = 'configurable', p.base_row_total, i.base_row_total) / e.base_subtotal "
    	                       . "* (e.base_grand_total - IFNULL(e.base_shipping_amount, 0) - IFNULL(e.base_tax_amount, 0)), "
    	                       . "0)"
    	));
    	$productTypes = array(
            Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
            Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
            Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
        );
    	$select->joinInner(
    	   array('i' => $resource->getTableName('sales/order_item')),
    	   'e.entity_id = i.order_id'
    	   . " AND i.product_type NOT IN('" . implode("', '", $productTypes) . "')",
    	   array()
    	);
    	// Join parent item
    	$select->joinLeft(
    	   array('p' => $resource->getTableName('sales/order_item')),
    	   'i.parent_item_id = p.item_id',
    	   array()
    	);
    	// Join product name
    	$product = Mage::getResourceSingleton('catalog/product');
    	$attr = $product->getAttribute('name');
    	$select->joinLeft(
    	   array('pn' => $attr->getBackend()->getTable()),
    	   'pn.entity_id = i.product_id'
    	   . ' AND pn.store_id = 0'
    	   . ' AND pn.entity_type_id = ' . $product->getTypeId()
    	   . ' AND pn.attribute_id = ' . $attr->getAttributeId(),
    	   array()
    	);
    	if (!$all) {
    		// Filter by changed status order
    		$select->joinInner(
    		    array('s' => $resource->getTableName('simisalestrackingapi/bestsellers_orderchange')),
    		    'e.entity_id = s.order_id',
    		    array()
    		);
    	}
    	// Update index data
    	Mage::getResourceModel('simisalestrackingapi/bestsellers_daily')->insertDataFromSelect($select, $all);
    	//clear table order_change when done
        Mage::getResourceModel('simisalestrackingapi/bestsellers_orderchange')->clear();
        //set time update or refresh
        $this->setTimeRefresh();
    	// End refresh bestseller
    	return true;
    	
        if($all){
            $this->_getOrderCollection(true);
        }else{
            $this->_getOrderCollection();
        }
        
        if($this->_orderCollection){
            try{
                //get date to clear table index daily
                $arr_date = '';
                if(!$all){
                    // get created_at in order
                    $arr_date = $this->getGroupDateArray();
                    //reset order collection where
                    if(!empty($arr_date)){
                        $this->_orderCollection->getSelect()->reset(Zend_Db_Select::WHERE);
                        if(!$this->_helper->version(2)){
                            $this->_orderCollection->addAttributeToFilter('DATE(main_table.created_at)', array('in'=>array($arr_date)));
                        }else{
                            //1.4.0.1
                            $this->_orderCollection->getSelect()
                                ->where("DATE(e.created_at) in (?)", array($arr_date));
                        }
                    }
                }
                // CREATE TEMPORARY TABLE
                $_temp_sql = "CREATE TEMPORARY TABLE simisalestrackingapi_best_sum_total ";
                $select = clone $this->_orderCollection->getSelect();
                $select->joinRight(
                        array('order_item' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                        "{$this->_main_table}.entity_id = order_item.order_id",
                        array('order_item.*'))
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->columns(array(
                        'order_id' => 'order_item.order_id',
                        'sum_base_row_total' => 'SUM(IF(order_item.base_row_total > order_item.base_discount_amount, order_item.base_row_total - order_item.base_discount_amount, 0))',
                        'sum_base_row_total_incl_tax' => 'SUM(IF(order_item.base_row_total_incl_tax > order_item.base_discount_amount, order_item.base_row_total_incl_tax - order_item.base_discount_amount, 0))'
                        //0.1.1
                        //'order_id' => 'order_item.order_id',
                        //'sum_base_row_total' => 'SUM(IF(order_item.base_row_total > order_item.base_discount_amount, order_item.base_row_total - order_item.base_discount_amount, 0))',
                        //'sum_base_row_total_incl_tax' => 'SUM(IF(order_item.base_row_total_incl_tax > order_item.base_discount_amount, order_item.base_row_total_incl_tax - order_item.base_discount_amount, 0))'
                        //old
                        //'order_id' => 'order_item.order_id',
                        //'sum_base_row_total' => 'SUM(order_item.base_row_total)',
                        //'sum_base_row_total_incl_tax' =>  'SUM(order_item.base_row_total_incl_tax)'
                    ))
                    ->where("order_item.parent_item_id IS NULL") //only parent item
                    ->group('order_item.order_id');
                $_temp_sql .= (string)$select;// print_r($_temp_sql); die;
                Mage::getSingleton('core/resource')->getConnection('core_write')->query($_temp_sql);
                
                // calculate data
                $_calc_data = $this->_orderCollection->getSelect();
                $_calc_data->joinRight(
                    array('order_item' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                    "{$this->_main_table}.entity_id = order_item.order_id",
                    array('order_item.*'))
                ->joinLeft(
                    array('order_item2' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                    'order_item.parent_item_id = order_item2.item_id',
                    array('order_item2.product_type'))
                ->joinLeft(
                    array('product' => Mage::getSingleton('core/resource')->getTableName('catalog/product')),
                    'product.entity_id = order_item.product_id',
                    array('order_item.*'))
                ->joinLeft(
                    array('sum_total' => 'simisalestrackingapi_best_sum_total'),
                    'order_item.order_id = sum_total.order_id',
                    array('sum_total.*'))
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array(
                    'product_id'    =>  'order_item.product_id',
                    'sku'           =>  'product.sku',
                    'product_name'  =>  'order_item.name',
                    'created_at'    =>  "{$this->_main_table}.created_at",
                    'updated_at'    =>  "{$this->_main_table}.updated_at",
                    'status'        =>  "{$this->_main_table}.status",
                    'store_id'      =>  "{$this->_main_table}.store_id",
                    'sales'         =>  'SUM(IF( order_item.base_row_total_incl_tax IS NOT NULL,'.
                                                ' IF( order_item.base_row_total_incl_tax > order_item.base_discount_amount,'.
                                                    " ({$this->_main_table}.base_grand_total - IFNULL({$this->_main_table}.base_shipping_amount, 0)) * (order_item.base_row_total_incl_tax - order_item.base_discount_amount)/sum_total.sum_base_row_total_incl_tax,".
                                                    ' 0),'.
                                                ' IF( order_item.base_row_total > order_item.base_discount_amount,'.
                                                    " ({$this->_main_table}.base_grand_total - IFNULL({$this->_main_table}.base_shipping_amount, 0)) * (order_item.base_row_total - order_item.base_discount_amount)/sum_total.sum_base_row_total,".
                                                    ' 0)'.
                                            '))',
                    //0.1.1
                    //'sales'         =>  'SUM(IF( order_item.base_row_total_incl_tax IS NOT NULL, IF(order_item.base_row_total_incl_tax > order_item.base_discount_amount, main_table.base_grand_total * (order_item.base_row_total_incl_tax - order_item.base_discount_amount)/sum_total.sum_base_row_total_incl_tax, 0), IF(order_item.base_row_total > order_item.base_discount_amount, main_table.base_grand_total * (order_item.base_row_total - order_item.base_discount_amount)/sum_total.sum_base_row_total, 0) ))',
                    //'sales'         =>  'SUM(IF( order_item.base_row_total_incl_tax IS NOT NULL, main_table.base_grand_total * order_item.base_row_total_incl_tax/sum_total.sum_base_row_total_incl_tax, main_table.base_grand_total * order_item.base_row_total/sum_total.sum_base_row_total))',
                    'qty'           =>  'SUM(order_item.qty_ordered)'))
                ->where("(order_item2.product_type != 'configurable' AND order_item2.product_type != 'bundle') OR order_item.parent_item_id IS NULL")// OR order_item2.product_type IS NULL") //not parent item
                ->group(array("DATE({$this->_main_table}.created_at)","DATE({$this->_main_table}.updated_at)", "{$this->_main_table}.status", "{$this->_main_table}.store_id", 'order_item.product_id'))
                ->order('sales desc');
                
                $db = Mage::getSingleton('core/resource')->getConnection('core_read');
                $datas = $db->fetchAll($_calc_data);
                //print_r($_calc_data->__toString()); die;
                //zend_debug::dump($datas); die;
                if(!$all && !empty($arr_date)){
                    //zend_debug::dump($arr_date);die;
                    Mage::getResourceModel('simisalestrackingapi/bestsellers_daily')->write($datas, $arr_date);
                }else if(!empty($datas)){
                    Mage::getResourceModel('simisalestrackingapi/bestsellers_daily')->write($datas);
                }
            }catch (Exception $e) {
                throw $e;
                return false;
            }
        }
        //clear table order_change when done
        Mage::getResourceModel('simisalestrackingapi/bestsellers_orderchange')->clear();
        //set time update or refresh
        $this->setTimeRefresh();
        return true;
    }
    
    /**
     * get bestsellers collection
     * @param Zend_Date $from with timezone locale
     * @param Zend_Date $to with timezone locale
     * @return Magestore_Simisalestrackingapi_Model_Bestsellers
     */
    public function setDateRange($from = '', $to = ''){
        if($this->_collection){
            if($from instanceof Zend_Date){
                $from->setTimezone('Etc/UTC');
                $this->_collection->addFieldToFilter('updated_at', array('from'=>$from->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)));
            }
            if($to instanceof Zend_Date){
                $to->setTimezone('Etc/UTC');
                $this->_collection->addFieldToFilter('updated_at', array('to'=>$to->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)));
            }
        }
        return $this;
    }
    
    /**
     * set store id to filter
     * @param int $store_id store id
     * @return Magestore_Simisalestrackingapi_Model_Bestsellers
     */
    public function setStoreId($store_id){
        if($this->_collection && $store_id != '0'){
            $this->_collection
                ->addFieldToFilter('store_id', $store_id);
        }
        return $this;
    }

    /**
     * set status to filter
     * @param array $status
     * @return Magestore_Simisalestrackingapi_Model_Bestsellers
     */
    public function setStatus($status = array()){
        if($this->_collection && !empty($status)){
            //if(count($status)==1 && $status[0]==''){
            //    return $this;
            //}
            $this->_collection->addFieldToFilter('status', array('in'=>$status));
        }else{
            $this->_collection->addFieldToFilter('status', array('in'=>array(''))); //filter by null
        }
        return $this;
    }
    
    /**
     * set order by
     * @param mixed $field_name
     * @return Magestore_Simisalestrackingapi_Model_Bestsellers
     */
    public function setOrderBy($field_name){
        $this->_order_by = $field_name;
        return $this;
    }
    /**
     * add order by multi columns
     * @param mixed $field_name
     * @return Magestore_Simisalestrackingapi_Model_Bestsellers
     */
    public function addOrderBy($field_name){
        $this->_order_by = $field_name;
        $sort_by = array();
        if(!is_array($this->_order_by) && $this->_order_by != ''){
            $sort_by[] = $this->_order_by;
        }else{
            $sort_by = $this->_order_by;
            $sort_by[] = $field_name;
        }
        $this->_order_by = $sort_by;
        return $this;
    }
    
    /**
     * set limit for list
     */
    public function setLimit($number){
        if($this->_collection){
            $this->_collection->setPage(0, $number);
        }
        return $this;
    }

    /**
     * get collection
     * @return collection Magestore_Simisalestrackingapi_Model_Mysql4_Bestsellers_Daily_Collection
     */
    public function getCollection(){
        if($this->_collection){
            if(is_array($this->_order_by)){
                foreach($this->_order_by as $field){
                    $this->_collection->setOrder($field, 'DESC');
                }
            }else{
                $this->_collection->setOrder($this->_order_by, 'DESC');
            }
            $this->_collection
                ->getSelect()
                ->columns(array('sales'=>'SUM(sales)', 'qty'=>'SUM(qty)'))
                ->group('product_id');
            $_select = clone $this->_collection->getSelect();
            $db = Mage::getSingleton('core/resource')->getConnection('core_read');
            $_select->reset(Zend_Db_Select::ORDER)
                ->reset(Zend_Db_Select::LIMIT_COUNT)
                ->reset(Zend_Db_Select::LIMIT_OFFSET)
                ->reset(Zend_Db_Select::COLUMNS)
                ->reset(Zend_Db_Select::GROUP);
            $_select->columns('COUNT(DISTINCT product_id)');
            $this->_size = (int)$db->fetchOne($_select);
            // $all_row = $db->fetchCol($_select); // array date to clear index
            // $this->_size = count($all_row);
            return $this->_collection;
        }
        return false;
    }
    
    public function getSize(){
        return $this->_size;
    }
    
    /**
     * set time update or refresh
     * return $this;
     */
    public function setTimeRefresh(){
        $date = Mage::app()->getLocale()->date();
        $date->setTimezone('Etc/UTC');
        //save to config
        Mage::getModel('simisalestrackingapi/settings')->setSetting($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT), 'time_refresh_bestsellers');
        //save to session
        Mage::getSingleton('adminhtml/session')->setSalestrackingTimeRefreshBestsellers($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        return $this;
    }
    
    /**
     * get group date visible in order collection to array 
     */
    protected function getGroupDateArray(){
        if($this->_orderCollection){
            $_select = clone $this->_orderCollection->getSelect();
            $_select->reset(Zend_Db_Select::COLUMNS)
                ->columns(array('created_at'=>'DATE(created_at)'))
                ->group('DATE(created_at)');
            $db = Mage::getSingleton('core/resource')->getConnection('core_read');
            $arr_date = $db->fetchCol($_select); // array date to clear index
            return $arr_date;
        }else{
            return array();
        }
    }
}

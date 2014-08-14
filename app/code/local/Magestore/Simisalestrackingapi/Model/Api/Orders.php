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
 * SimiSalestracking Api Orders Server Model
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Api_Orders extends Magestore_Simisalestrackingapi_Model_Api_Abstract
{
    /**
     * api page Dashboard
     *
     * ?call=<orders>
     * [& params={
     *      "tab":"null|today|yesterday|this_week|last_week|this_month|last_month|2_months_ago|3_months_ago",
     *      "filter":"abc",
     *      "page":1|2|3|...,
     *      "limit":10,
     *      "store":1
     * }]
     * 
     * @param type $params
     * @return json array
     */
    public function apiIndex($params){
        $collection = false;
        //if filter by order status
        if(isset($params['order_status'])){
            if(!is_array($params['order_status'])){
                $params['order_status'] = array($params['order_status']);
            }
            $this->_helper->setOrderStatus($params['order_status']);
        }
        $filter = '';
        if(isset($params['filter'])){
            $filter = $params['filter'];
        }
        if(!isset($params['tab'])){
            $params['tab'] = '';
        }
        $collection = $this->getOrderTab($params['tab'], $filter); //get order collection by tab
        if(!$collection){
            $data = array(
                    'id'                => '',
                    'customer_name'     => '',
                    'customer_email'    => '',
                    'increment'         => '',
                    'date'              => '',
                    'grand_total'       => Mage::helper('core')->currency('', true, false),
                    'status'            => '',
                    'sku'               => '',
                    'is_new'            => '',
                    'is_unread'         => ''
                );
            return $result = array(
                'num_order'     => 0,
                'total_sales'   => Mage::helper('core')->currency(0, true, false),
                'data'          => $data,
                'list_ids'      => array(),
                'store'         => array()
            );
        }
        //$all_order_ids = $this->getAllOrderIds($collection);
        if(isset($params['top']) && isset($params['slice'])){
            if(!empty($params['top']) && !empty($params['slice'])){
                $params['top'] = (int)$params['top'];
                if($params['slice'] == 'up'){
                    $collection->getSelect()->where("{$this->mainTable}.entity_id > {$params['top']}");
                }else if($params['slice'] == 'down'){
                    $collection->getSelect()->where("{$this->mainTable}.entity_id <= {$params['top']}");
                }
                //throw new Exception($this->_helper->__('No params top and slice'),130);
            }else{
                Mage::helper('simisalestrackingapi')->resetTimeNewOrders();//reset new order
            }
        }
        $collection->getSelect()->limit($params['limit'],$params['page']*$params['limit']);//limit page
        //print_r($collection->getSelectSql(true)); die;
        //if param group
        if(!isset($params['group'])){
            $params['group'] = '0';
        }
        if($params['group'] != '0' && $params['group'] != '1'){
            throw new Exception($this->_helper->__('group: 0 or 1'),14);
        }
        $data = $this->convertOrderData($collection, $params['group']);
        $result = array(
            'data'          => $data,
            'list_ids'      => $this->_list_ids
        );
        return $result;
    }
    
    /**
     * call=orders.view
     * &params={
     *      "order_status":"string|array(string1,string2,...)",
     *      "store":"int",
     *      
     *      /----tab order
     *      "tab":"this_month",
     *      "filter":"string",
     *      
     *      /----search_product
     *      "product_id":int|string,
     *      "from_date":"date",
     *      "to_date":"date",
     *      "from_value":int,
     *      "to_value":int,
     *      
     *      /----search
     *      "from_date":"date", //2013-12-13
     *      "to_date":"date",
     *      "product":"string",
     *      "from_value":int,
     *      "to_value":int,
     * }
     */
    public function apiTotalsales($params){
        $collection = $this->getOrderByParams($params);
        if($collection){
            //$num_order = $collection->getSize();
            // $collection = $this->getOrderByParams($params);
            $collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array(
                    'total_sales'=>"IF({$this->mainTable}.status = 'complete',"
                                        . "IF({$this->mainTable}.base_total_refunded IS NULL,"
                                            . "{$this->mainTable}.base_total_paid,"
                                            . "{$this->mainTable}.base_total_paid - {$this->mainTable}.base_total_refunded),"
                                        . "{$this->mainTable}.base_grand_total)",
                    'num_order' => "{$this->mainTable}.entity_id"
                ));

            $view = clone $collection->getSelect();
            $collection->getSelect()->reset()
                ->from(array('v' => new Zend_Db_Expr('(' . $view->__toString() . ')')), array())
                ->columns(array(
                    'total_sales'    => 'SUM(v.total_sales)',
                    'num_order'      => 'COUNT(v.num_order)'
                ));
            //zend_debug::dump($collection->getSelectSql(true)); die;
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $rows = $readConnection->fetchRow($collection->getSelect());
            if(!isset($rows) || $rows == ''){
                $rows = 0;
            }
            return array(
                'total_sales'   =>  Mage::helper('core')->currency($rows['total_sales'], true, false),
                'num_order'     =>  $rows['num_order']//$num_order
            );
        }else{
            return array(
                'total_sales'   =>  Mage::helper('core')->currency(0, true, false),
                'num_order'     =>  0
            );
        }
    }


    /**
     * advance search order
     * 
     * call=orders.search
     * &params={
     *      "from_date":"date", //2013-12-13
     *      "to_date":"date",
     *      "product":"string",
     *      "order_status":"string|array(string1,string2,...)",
     *      "from_value":int,
     *      "to_value":int,
     *      "page":int,
     *      "limit":int,
     *      "store":int
     * }
     * 
     * @param type $params
     * 
     */
    public function apiSearch($params){
        if(isset($params['from_date'])){
            if(!$this->checkDate($params['from_date'])){
                throw new Exception($this->_helper->__('Date format from_date error, example: 2013-12-13'),19);
            }
        }else{
            $params['from_date'] = '';
        }
        if(isset($params['to_date'])){
            if(!$this->checkDate($params['to_date'])){
                throw new Exception($this->_helper->__('Date format to_date error, example: 2013-12-13'),20);
            }
        }else{
            $params['to_date'] = '';
        }
        //if filter by order status
        if(isset($params['order_status'])){
            if(!is_array($params['order_status'])){
                $params['order_status'] = array($params['order_status']);
            }
            $this->_helper->setOrderStatus($params['order_status']);
        }
        $from_val = ''; $to_val = ''; $product = '';
        if(isset($params['from_value'])){
            $from_val = (string)$params['from_value'];
        }
        if(isset($params['to_value'])){
            $to_val = (string)$params['to_value'];
        }
        if(isset($params['product'])){
            $product = (string)$params['product'];
        }
        
        $collection = Mage::getModel('simisalestrackingapi/orders')
            ->searchOrder($params['from_date'], $params['to_date'], $product, $params['order_status'], $from_val, $to_val);
        
        //get all order ids
        //$all_order_ids = $this->getAllOrderIds($collection);
        if(isset($params['top']) && isset($params['slice'])){
            if(!empty($params['top']) && !empty($params['slice'])){
                $params['top'] = (int)$params['top'];
                if($params['slice'] == 'up'){
                    $collection->getSelect()->where("{$this->mainTable}.entity_id > {$params['top']}");
                }else if($params['slice'] == 'down'){
                    $collection->getSelect()->where("{$this->mainTable}.entity_id <= {$params['top']}");
                }
                //throw new Exception($this->_helper->__('No params top and slice'),130);
            }
        }
        $collection->getSelect()->limit($params['limit'],$params['page']*$params['limit']);//limit page
        //if param group
        if(!isset($params['group'])){
            $params['group'] = '0';
        }
        if($params['group'] != '0' && $params['group'] != '1'){
            throw new Exception($this->_helper->__('group: 0 or 1'),14);
        }
        $data = $this->convertOrderData($collection, $params['group']);
        $result = array(
            'data'          => $data,
            'list_ids'      => $this->_list_ids
        );
        return $result;
    }
    
    /**
     * advance search order by product
     * 
     * call=orders.search
     * &params={
     *      "from_date":"date",
     *      "to_date":"date",
     *      "product_id":int|string,
     *      "order_status":"string|array(string1,string2,...)",
     *      "from_value":int,
     *      "to_value":int
     * }
     * 
     * @param type $params
     * 
     */
    public function apiSearch_product($params){
        if(isset($params['from_date'])){
            if(!$this->checkDate($params['from_date'])){
                throw new Exception($this->_helper->__('Date format from_date error, example: 2013-12-13'),19);
            }
        }else{
            $params['from_date'] = '';
        }
        if(isset($params['to_date'])){
            if(!$this->checkDate($params['to_date'])){
                throw new Exception($this->_helper->__('Date format to_date error, example: 2013-12-13'),20);
            }
        }else{
            $params['to_date'] = '';
        }
        //if filter by order status
        if(isset($params['order_status'])){
            if(!is_array($params['order_status'])){
                $params['order_status'] = array($params['order_status']);
            }
            $this->_helper->setOrderStatus($params['order_status']);
        }else{
            $params['order_status'] = '';
        }
        $from_val = ''; $to_val = ''; $product_id = '';
        if(isset($params['from_value'])){
            $from_val = (string)$params['from_value'];
        }
        if(isset($params['to_value'])){
            $to_val = (string)$params['to_value'];
        }
        if(!isset($params['product_id']) || $params['product_id'] == ''){
            throw new Exception($this->_helper->__('No product_id'),13);
        }
        if(isset($params['product_id'])){
            $product_id = (string)$params['product_id'];
        }
        
        $collection = Mage::getModel('simisalestrackingapi/orders')
            ->getOrderByProduct($product_id, $params['from_date'], $params['to_date'], $params['order_status'], $from_val, $to_val);
        
        //get all order ids
        //$all_order_ids = $this->getAllOrderIds($collection);
        //get top or down list
        if(isset($params['top']) && isset($params['slice'])){
            if(!empty($params['top']) && !empty($params['slice'])){
                $params['top'] = (int)$params['top'];
                if($params['slice'] == 'up'){
                    $collection->getSelect()->where("{$this->mainTable}.entity_id > {$params['top']}");
                }else if($params['slice'] == 'down'){
                    $collection->getSelect()->where("{$this->mainTable}.entity_id <= {$params['top']}");
                }
                //throw new Exception($this->_helper->__('No params top and slice'),130);
            }
        }
        $collection->getSelect()->limit($params['limit'],$params['page']*$params['limit']);//limit page
        //if param group
        if(!isset($params['group'])){
            $params['group'] = '0';
        }
        if($params['group'] != '0' && $params['group'] != '1'){
            throw new Exception($this->_helper->__('group: 0 or 1'),14);
        }
        $data = $this->convertOrderData($collection, $params['group']);
        $result = array(
            'data'          => $data,
            'list_ids'      => $this->_list_ids
        );
        return $result;
    }
    
    /**
     * order view info
     * 
     * call=orders.view
     * &params={
     *      "id":"string",
     *      "order_status":"string|array(string1,string2,...)",
     *      "store":"int",
     *      
     *      /----tab order
     *      "tab":"this_month",
     *      "filter":"string",
     *      
     *      /----search_product
     *      "product_id":int|string,
     *      "from_date":"date",
     *      "to_date":"date",
     *      "from_value":int,
     *      "to_value":int,
     *      
     *      /----search
     *      "from_date":"date", //2013-12-13
     *      "to_date":"date",
     *      "product":"string",
     *      "from_value":int,
     *      "to_value":int,
     * }
     * 
     * @param type $params
     * 
     */
    public function apiView($params){
        if(!isset($params['id']) || $params['id'] == ''){
            throw new Exception($this->_helper->__('Not defined param name "id" or value is null'), 21);
        }
        $next_ids = $this->getNextIds($params);
        $prev_ids = $this->getPrevIds($params);
        //zend_debug::dump($prev_ids);
        //zend_debug::dump($next_ids); die;
        $order = Mage::getModel('sales/order')->load($params['id']);
        //zend_debug::dump($order->getData());die;
        if($order->getId() == ''){
            throw new Exception($this->_helper->__('No order'), 22);
        }
        Mage::register('current_order', $order); //set current order
        Mage::register('sales_order', $order);
        
        //load layout for update handle to get total info
        $controller = Mage::getControllerInstance('Mage_Core_Controller_Front_Action',
                Mage::app()->getRequest(), Mage::app()->getResponse());
        //if(version_compare(Mage::getVersion(),'1.5','<=')){//lt 1.4
            $controller->loadLayout('default');
        //}
        $controller->loadLayout('adminhtml_sales_order_view');
        
        $block = Mage::getBlockSingleton('simisalestrackingapi/orders_detail');
        $_totals = Mage::getBlockSingleton('simisalestrackingapi/orders_totals')->getTotals();
        if(version_compare(Mage::getVersion(),'1.5','>=')){
            $order_totals = $controller->getLayout()->getBlock('order_totals');
        }else{
            $order_totals = Mage::getBlockSingleton('adminhtml/sales_order_totals');
        }
        if(is_object($order_totals)){
            //add totals
            foreach($_totals as $total){
                $order_totals->addTotal($total, 'last');
            }
            //call init total function
            $childs = $order_totals->getChild();
            foreach($childs as $child){
                $child->initTotals();
            }
            $total_info = $order_totals->getTotals();
        }else{
            $total_info = $_totals;
        }
        $telephone = '';
        if(($billing_address = $order->getBillingAddress())){
            $telephone = $billing_address->getTelephone();
        }
        $created_date = Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false);
        $created_time = Mage::helper('core')->formatTime($order->getCreatedAtStoreDate(), 'medium');
        
        $order_items = array();
        foreach ($order->getItemsCollection() as $item){
            if ($item->getParentItem()) continue;
            $block->setItem($item);
            $options = array();
            if ($block->getOrderOptions()){
                foreach ($block->getOrderOptions() as $_option){
                    $options[] = array(
                        'label'=>$_option['label'],
                        'value'=> (isset($_option['custom_view']) && $_option['custom_view'])? 
                                $block->getCustomizedOptionValue($_option):
                                (is_array($_option['value'])) ? 
                                    $_option['value'][0]['qty'].' x '.$_option['value'][0]['title'].' ('.Mage::helper('core')->currency($_option['value'][0]['price'], true, false).')'
                                    : $_option['value']
                        
                    );
                }
            }
            
            $order_items[] = array(
                'name'      =>  $item->getName(),
                'sku'       =>  $block->getSku(),
                'options'   =>  $options,
                'qty'       =>  (int)$item->getQtyOrdered(),
                'row_total' =>  Mage::helper('core')->currency($item->getBaseRowTotal(), true, false)
            );
        }
        
        //total info to array data
        $total_inf = array();
        foreach ($total_info as $_code => $_row){
            $total_inf[] = array(
                'code'          =>  $_code,
                'is_strong'     =>  ($_row->getStrong())? 1:0,
                'label'         =>  ($_row->getLabel())?$_row->getLabel():$_code,
                'value'         =>  Mage::helper('core')->currency($_row->getValue(), true, false)
            );
        }
        
        $data = array(
            'id'            =>  (int)$order->getId(),
            'next_ids'      =>  $next_ids,
            'prev_ids'      =>  $prev_ids,
            'increment_id'  =>  $order->getIncrementId(),
            'customer_name' =>  $order->getCustomerName(),
            'customer_email'=>  $order->getCustomerEmail(),
            'customer_id'   =>  (int)$order->getCustomerId(),
            'created_date'  =>  $created_date,
            'created_time'  =>  $created_time,
            'telephone'     =>  $telephone,
            'status'        =>  $order->getStatus(),
            'order_items'   =>  $order_items,
            'total_info'    =>  $total_inf
        );
        $this->_helper->readNewOrder($order->getId()); //set is read new order
        return $data;
   }


   /**
     * get all status of order model
     * return array json [{"code":"name"},...]
     */
    public function apiGet_status(){
        $status = Mage::getSingleton('sales/order_config')->getStatuses();;
        foreach($status as $code => $name){
            $response[] = array('code'=>$code, 'name'=>$name);
        }
        if(empty($response)){
            throw new Exception($this->_helper->__('No status'), 23);
        }
        return $response;
    }
    
    /**
     * get status in system config
     * return array json ["complete","canceled","..."]
     */
    public function apiGet_status_config(){
        $status_arr = $this->_helper->getOrderStatus();
        return $status_arr;
    }
    
    public function checkDate($date){
        if (DateTime::createFromFormat('Y-m-d', $date) !== FALSE || $date == '') {
            return true;
        }
        //if(DateTime::createFromFormat('Y/m/d', $date) !== FALSE){
        //    return true;
        //}
        return false;
    }
    
    protected function getNextIds($params){
        $next_ids = array('','');
        $collection = $this->getOrderByParams($params);
        $collection->getSelect()
            ->where("{$this->mainTable}.entity_id < ?", $params['id'])
            ->limit(2);
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->columns('entity_id');
        $temp = $collection->getColumnValues('entity_id');
        $next_ids[0] = isset($temp[0])?$temp[0]:'';
        $next_ids[1] = isset($temp[1])?$temp[1]:'';
        return $next_ids;
    }
    
    protected function getPrevIds($params){
        $prev_ids = array('','');
        $collection = $this->getOrderByParams($params);
        $collection->getSelect()
            ->reset(Zend_Db_Select::ORDER)
            ->where("{$this->mainTable}.entity_id > ?", $params['id'])
            ->limit(2)
            ->order("{$this->mainTable}.updated_at ASC");
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->columns('entity_id');
        $temp = $collection->getColumnValues('entity_id');
        $prev_ids[0] = isset($temp[0])?$temp[0]:'';
        $prev_ids[1] = isset($temp[1])?$temp[1]:'';
        return $prev_ids;
    }
    
    protected function getOrderByParams($params){
        //set if filter by order status
        if(isset($params['order_status'])){
            if(!is_array($params['order_status'])){
                $params['order_status'] = array($params['order_status']);
            }
            $this->_helper->setOrderStatus($params['order_status']);
        }
        if(!isset($params['tab'])){
            $params['tab'] = '';
        }
        if(!isset($params['filter'])){
            $params['filter'] = '';
        }
        if(isset($params['tab']) && $params['tab'] != ''){
            return $this->getOrderTab($params['tab'], $params['filter']); //get order collection by tab
        }else if(isset($params['product_id']) && $params['product_id'] != ''){ //get total sales for bestsellers
            //check params
            //if filter by date range
            if(isset($params['date_range'])){
                $this->_helper->setBestsellersDateRangeCode($params['date_range']);
            }
            //filter by from to date time
            $from = $this->_helper->timeBestsellers(); //zend_date by time bestsellers
            
            //if filter by order status
            if(isset($params['order_status'])){
                if(!is_array($params['order_status'])){
                    $params['order_status'] = array($params['order_status']);
                }
                $this->_helper->setOrderStatus($params['order_status']);
            }else{
                $params['order_status'] = '';
            }
            //param filter
            $filter = '';
            if(isset($params['filter'])){
                $filter = $params['filter'];
            }
            $product_id = '';
            if(!isset($params['product_id']) || $params['product_id'] == ''){
                throw new Exception($this->_helper->__('No product_id'),13);
            }
            if(isset($params['product_id'])){
                $product_id = (string)$params['product_id'];
            }
            return Mage::getModel('simisalestrackingapi/orders')
                ->getOrderByProduct($product_id, $from, '', '', '', '', $filter);
        }else if(isset($params['date']) && $params['date'] != ''){
            $collection = false;
            $filter = '';
            if(isset($params['filter'])){
                $filter = $params['filter'];
            }
            //if filter by order status
            if(isset($params['order_status'])){
                if(!is_array($params['order_status'])){
                    $params['order_status'] = array($params['order_status']);
                }
                $this->_helper->setOrderStatus($params['order_status']);
            }

            //get date from param request
            $date = '';
            if(isset($params['date'])){
                if (DateTime::createFromFormat('d/m/Y', $params['date']) !== FALSE) {
                    $date = $params['date'];
                }else{
                    throw new Exception($this->_helper->__('Incorrect param date format like 31/01/1990'), 24);
                }
            }
            //get order collection
            return Mage::getModel('simisalestrackingapi/orders')->getOrdersByDate($date, $filter);
            
        }else{
            if(isset($params['from_date'])){
                if(!$this->checkDate($params['from_date'])){
                    throw new Exception($this->_helper->__('Date format from_date error, example: 2013-12-13'),19);
                }
            }else{
                $params['from_date'] = '';
            }
            if(isset($params['to_date'])){
                if(!$this->checkDate($params['to_date'])){
                    throw new Exception($this->_helper->__('Date format to_date error, example: 2013-12-13'),20);
                }
            }else{
                $params['to_date'] = '';
            }
            //if filter by order status
            if(isset($params['order_status'])){
                if(!is_array($params['order_status'])){
                    $params['order_status'] = array($params['order_status']);
                }
                $this->_helper->setOrderStatus($params['order_status']);
            }
            $from_val = ''; $to_val = ''; $product = '';
            if(isset($params['from_value'])){
                $from_val = (string)$params['from_value'];
            }
            if(isset($params['to_value'])){
                $to_val = (string)$params['to_value'];
            }
            if(isset($params['product'])){
                $product = (string)$params['product'];
            }

            return Mage::getModel('simisalestrackingapi/orders')
                ->searchOrder($params['from_date'], $params['to_date'], $product, $params['order_status'], $from_val, $to_val);
        }
    }


    protected function getOrderTab($tab, $filter = ''){
        switch ($tab){
            case 'today':
                $collection = Mage::getModel('simisalestrackingapi/orders')->getToday($filter);
                break;
            case 'yesterday':
                $collection = Mage::getModel('simisalestrackingapi/orders')->getYesterday($filter);
                break;
            case 'this_week':
                $collection = Mage::getModel('simisalestrackingapi/orders')->getThisWeek($filter);
                break;
            case 'last_week':
                $collection = Mage::getModel('simisalestrackingapi/orders')->getLastWeek($filter);
                break;
            case 'this_month':
                $collection = Mage::getModel('simisalestrackingapi/orders')->getThisMonth($filter);
                break;
            case 'last_month':
                $collection = Mage::getModel('simisalestrackingapi/orders')->getLastMonth($filter);
                break;
            case '2_months_ago':
                $collection = Mage::getModel('simisalestrackingapi/orders')->get2MonthsAgo($filter);
                break;
            case '3_months_ago':
                $collection = Mage::getModel('simisalestrackingapi/orders')->get3MonthsAgo('','',$filter);
                break;
            case 'all':
                $collection = Mage::getModel('simisalestrackingapi/orders')->getOrders($filter);
                break;
            default:
                $collection = false;
                break;
        }
        return $collection;
    }
}

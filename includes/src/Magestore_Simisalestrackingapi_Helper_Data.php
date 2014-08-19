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
 * Simisalestrackingapi Helper
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_limit_items = '';
    protected $_page = '';
    protected $_bestseller_date_range_code = ''; //convert to int
    protected $_order_status = array();
    protected $_num_bestsellers = 0;

    /**
     * check to show bestsellers on dashboard
     * @return boolean
     */
    public function isShowBestsellers(){
        $settings = Mage::getModel('simisalestrackingapi/settings');
        $show = $settings->getSetting(Magestore_Simisalestrackingapi_Model_Settings::_SHOW_BESTSELLERS);
        if($show == 'on'){
            return true;
        }else{
            return false;
        }
    }


    /**
     * get from date of time bestsellers
     * @return date Zend_Date
     */
    public function timeBestsellers(){
        $time = $this->getBestsellersDateRangeCode();
        if(is_null($time)) {
            $time = '30d';
            $settings = Mage::getModel('simisalestrackingapi/settings');
            $settings->saveSetting($time, Magestore_Simisalestrackingapi_Model_Settings::_TIME_BESTSELLERS);
        }
        $currentDate = Mage::app()->getLocale()->date();
        switch ($time){
            case '1d':
                $date = $currentDate->subDay(1);
                break;
            case '7d':
                $date = $currentDate->subDay(7);
                break;
            case '15d':
                $date = $currentDate->subDay(15);
                break;
            case '30d':
                $date = $currentDate->subDay(30);
                break;
            case '3m':
                $date = $currentDate->subMonth(3);
                break;
            case '6m':
                $date = $currentDate->subMonth(6);
                break;
            case '1y':
                $date = $currentDate->subYear(1);
                break;
            case '2y':
                $date = $currentDate->subYear(2);
                break;
            case 'lt':
                $date = $currentDate->setYear(1974)->setMonth(1)->setDate(1);
                break;
            default :
                $date = $currentDate;
        }
        
        return $date;
    }
    
    /**
     * get title bestsellers show
     * @return string
     */
    public function getBestsellersTitleTime(){
        switch ($this->getBestsellersDateRangeCode()){
            case '1d':
                $time = 'LAST 1 DAY';
                break;
            case '7d':
                $time = 'LAST 7 DAYS';
                break;
            case '15d':
                $time = 'LAST 15 DAYS';
                break;
            case '30d':
                $time = 'LAST 30 DAYS';
                break;
            case '3m':
                $time = 'LAST 3 MONTH';
                break;
            case '6m':
                $time = 'LAST 6 MONTH';
                break;
            case '1y':
                $time = 'LAST 1 Year';
                break;
            case '2y':
                $time = 'LAST 2 Years';
                break;
            case 'lt':
                $time = 'Lifetime';
                break;
            default :
                $time = 'Lifetime';
        }
        return $time;
    }
    
    public function setBestsellersDateRangeCode($code){
        $this->_bestseller_date_range_code = $code;
        return $this;
    }
    
    public function getBestsellersDateRangeCode(){
        if($this->_bestseller_date_range_code){
            return $this->_bestseller_date_range_code;
        }else{
            return Mage::getSingleton('simisalestrackingapi/settings')
                ->getSetting(Magestore_Simisalestrackingapi_Model_Settings::_TIME_BESTSELLERS);
        }
    }


    /**
     * get for bestsellers list
     * @return collection
     */
    public function getBestsellersCollection(){
        $storeId  =  $this->currentStoreId();
        $from = $this->timeBestsellers(); //Zend_Date //->toString('yyyy-MM-dd HH:mm:ss');
        $to = Mage::app()->getLocale()->date(); //Zend_Date //->toString('yyyy-MM-dd HH:mm:ss');
        $bestsellers = Mage::getSingleton('simisalestrackingapi/bestsellers')
            ->setStoreId($storeId)
            ->setDateRange($from, $to)
            ->setStatus($this->getOrderStatus())
            ->setOrderBy(array('qty','sales'));
        
        $collection = $bestsellers->getCollection();
        $this->_num_bestsellers = $bestsellers->getSize();
        //print_r($collection->getSelectSql(true)); die;
        return $collection;
    }
    
    /**
     * get bestsellers number
     * @return int
     */
    public function getNumBestsellers(){
        return $this->_num_bestsellers;
    }


    /**
     * get best sales of product by product id
     * return int
     */
    public function loadSalesByProduct($product_id){
        $from = $this->timeBestsellers(); //zend_date by time bestsellers
        $sales = 0;
        if(isset($product_id)){
            $orders = Mage::getModel('simisalestrackingapi/orders')->getOrderByProduct($product_id,$from);
            $select = clone $orders->getSelect();
            $group = $select->getPart(Zend_Db_Select::GROUP);
            unset($group[0]);
            $select->setPart(Zend_Db_Select::GROUP, array('order_item.product_id'));
            $select->setPart(Zend_Db_Select::COLUMNS, array(array('order_item','product_id','product_id')));
            $select->columns(array('sales'=>'SUM(order_item.base_row_total)'));
            //print_r((string)$select->__toString()); die;
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $rows = $readConnection->fetchAll($select);
            $sales = $rows[0]['sales'];
        }
        return $sales;
    }
    
    /**
     * set limmit size for show list of collection
     * @param int $number
     */
    public function setLimit($number){
        $this->_limit_items = $number;
    }
    /**
     * get limmit size for show list of collection
     * @param int $number
     */
    public function getLimit(){
        if($this->_limit_items)
            return $this->_limit_items;
        else
            return 10;//Mage::getModel('simisalestrackingapi/settings')->getSetting('limit');
    }
    public function setPage($number){
        $this->_page = $number;
    }
    public function getPage($number){
        $this->_page = $number;
    }
    
    /**
     * get current store id
     * return int store_id
     */
    public function currentStoreId(){
        $storeId  =  0;
        if(Mage::getSingleton('adminhtml/session')->getMobileStoreId() != ''){
            $storeId = Mage::getSingleton('adminhtml/session')->getMobileStoreId();
        }
        return $storeId;
    }
    /**
     * get current store object
     * @return Object
     */
    public function currentStore(){
        $storeId = $this->currentStoreId();
        return Mage::app()->getStore($storeId);
    }
    
    /**
     * get logdate login admin
     * @return datetime
     */
    public function getAdminLoginTime(){
        $admin_session = Mage::getSingleton('admin/session');
        $admin_user = $admin_session['user'];
        $admin_id = $admin_user->getData('user_id');
        $logdate = Mage::getModel('admin/user')->load($admin_id)->getData('logdate');
        return $logdate;
    }
    
    /**
     * get last time login of admin - this function use all time
     * last time login is saved in session
     * @return string datetime GMT = 0 login admin
     */
    public function getLastLogin(){
        $last = Mage::getSingleton('admin/session')->getLastLogin(); //datetime GMT0
        if($last == ''){
            $last = Mage::getModel('simisalestrackingapi/settings')->getLastLogin();
            Mage::getSingleton('admin/session')->setLastLogin($last);
        }
        return $last;
    }
    
    /**
     * get time calc new orders
     * @return datetime
     */
    public function getTimeNewOrders(){
        $time = Mage::getSingleton('admin/session')->getTimeNewOrders(); //datetime GMT0
        if($time == ''){
            $time = Mage::getModel('simisalestrackingapi/settings')->getTimeNewOrders();
            $zend_neworders = new Zend_Date();
            $zend_neworders->set($time, Zend_Date::ISO_8601);
            $time_lastlogin = $this->getLastLogin();
            $zend_date_login = new Zend_Date();
            $zend_date_login->set($time_lastlogin, Zend_Date::ISO_8601); //set datetime for zend_date
            if($zend_date_login->isLater($zend_neworders)){
                $time = $time_lastlogin;
            }
            Mage::getSingleton('admin/session')->setTimeNewOrders($time); //set to session
        }
        
        return $time;
    }
    /**
     * reset time of new orders
     */
    public function resetTimeNewOrders(){
        $date = Mage::app()->getLocale()->date();
        $datetime = gmdate("Y-m-d H:i:s", $date->getTimestamp());
        Mage::getSingleton('admin/session')->setTimeNewOrders($datetime);
        Mage::getModel('simisalestrackingapi/settings')->saveTimeNewOrders($datetime);
    }
    
    /**
     * get time calc new customers
     * @return datetime
     */
    public function getTimeNewCustomers(){
        $time = Mage::getSingleton('admin/session')->getTimeNewCustomers(); //datetime GMT0
        if($time == ''){
            $time = Mage::getModel('simisalestrackingapi/settings')->getTimeNewCustomers();
            $zend_new = Mage::app()->getLocale()->date();
            $zend_new->set($time, Zend_Date::ISO_8601);
            $time_lastlogin = $this->getLastLogin();
            $zend_login = new Zend_Date();
            $zend_login->set($time_lastlogin, Zend_Date::ISO_8601);
            if($zend_login->isLater($zend_new)){
                $time = $time_lastlogin;
            }
            Mage::getSingleton('admin/session')->setTimeNewCustomers($time); //set to session
        }
        return $time;
    }
    /**
     * reset time of new customers
     */
    public function resetTimeNewCustomers(){
        $date = Mage::app()->getLocale()->date();
        $datetime = gmdate("Y-m-d H:i:s", $date->getTimestamp());
        Mage::getSingleton('admin/session')->setTimeNewCustomers($datetime);
        Mage::getModel('simisalestrackingapi/settings')->saveTimeNewCustomers($datetime);
    }


    /**
     * get last time logged out of admin
     * @return string date time GMT = 0 logged out admin
     */
    public function getLastLoggedOut(){
        return Mage::getModel('simisalestrackingapi/settings')->getLastLogout();
    }


    /**
     * get order collection filter by store
     * @return order collection
     */
    public function getSalesCollections(){
        $storeId  =  $this->currentStoreId();
        $collection = Mage::getResourceModel('sales/order_collection');
        if($storeId != 0){
            $collection->addAttributeToFilter('store_id', $storeId); //fill store
        }
        return $collection;
    }
    
    /**
     * prepare datas new orders list
     * @param string $filter
     * @return type
     */
    public function getNewOrder($filter = '') {
        $collection = $this->getNewOrderCollection();
        if($filter != ''){
            $filter = '%'.$filter.'%';
            $collection->getSelect()
                ->where(
                    "increment_id like '$filter' 
                    OR customer_email like '$filter' 
                    OR customer_firstname like '$filter' 
                    OR customer_lastname like '$filter'"
                );
        }
        return $collection;
    }
    
    /**
     * get new order collection
     * @return type
     */
    public function getNewOrderCollection(){
        $lastTime = Mage::helper('simisalestrackingapi')->getTimeNewOrders();
        $collection = Mage::getResourceModel('sales/order_collection');
        //sort list
        //$collection->setOrder('main_table.created_at', 'desc');
        $collection->getSelect()->order("{$this->getMainTable()}.updated_at", 'desc');
        //filter new orders
        $collection->addAttributeToFilter('created_at', array('from' => $lastTime));
        //filter store
        $storeId = Mage::helper('simisalestrackingapi')->currentStoreId();
        if($storeId != 0){
            $collection->addAttributeToFilter('store_id', $storeId);
        }
        // filted by status
        /*
        $status_text = Mage::getModel('simisalestrackingapi/settings')->getSetting(Magestore_Simisalestrackingapi_Model_Settings::_STATUS_ORDERS);
        $status =  explode(";", $status_text);
        if(!empty($status) && $status_text != ''){
            $collection->addAttributeToFilter('status', array('in'=>$status));
        }
         * 
         */
        return $collection;
        
    }
    /**
     * cut short text
     */
    public function shorter($text, $length) {
        $length = abs((int) $length);
        if (strlen($text) > $length) {
            $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
        }
        return($text);
    }
    
    public function getColorStatusOrder($status){
        $color = '';
        switch ($status) {
            case 'processing':
                $color = '#0090da' ;
                break;
             case 'pending':
                $color = '#ff8400';
                break;
             case 'complete':
                $color = '#5d9200';
                break;
             case 'canceled':
                $color = '#ff0000';
                break;
             case 'closed':
                $color = '#b7b7b7';
                break;
             case 'fraud':
                $color = '#ff0000';
                break;
             case 'holded':
                $color = '#ff8400';
                break;
            case 'payment_review':
                $color = '#0090da';
                break;
            case 'pending_payment':
                $color = '#ff8400';
                break;
            case 'pending_paypal':
                $color = '#ff8400';
                break;
            default:
                $color = '#000000';
                break;
        }
        return $color;
    }
    
    /**
     * read new order action and change it to unread
     * @param type $id order id
     * @return boolean
     */
    public function readNewOrder($id){
        $newUnreads = Mage::getSingleton('adminhtml/session')->getNewOrderUnreads();
        if(isset($newUnreads[$id])){
            if($newUnreads[$id] == '1'){
                unset($newUnreads[$id]);
                Mage::getSingleton('adminhtml/session')->setNewOrderUnreads($newUnreads);
                return true;
            }
        }
        return false;
    }
    
    /**
     * read new order action and change it to unread
     * @param type $id order id
     * @return boolean
     */
    public function readNewCustomer($id){
        $newUnreads = Mage::getSingleton('adminhtml/session')->getNewCustomerUnreads();
        if(isset($newUnreads[$id])){
            if($newUnreads[$id] == '1'){
                unset($newUnreads[$id]);
                Mage::getSingleton('adminhtml/session')->setNewCustomerUnreads($newUnreads);
                return true;
            }
        }
        return false;
    }
    
    /**
     * set array Order's status
     */
    public function setOrderStatus($status = array()){
        $this->_order_status = $status;
        return $this;
    }
    
    /**
     * get array Order's status
     */
    public function getOrderStatus(){
        return $this->_order_status;
    }
    
    /**
     * check version magento
     * 
     * @param type $group
     * @return boolean
     */
    public function version($group = 0){
        $gVersion = array(
            1=>array('1.4.1.0','1.4.1.1','1.4.2.0','1.5.1.0','1.6.0.0','1.8.0.0',
                '1.9.0.0','1.9.1.1','1.10.0.1','1.10.1.1','1.11.0.0','1.12.0.0','1.13.0.0'),
            2=>array('1.4.0.1','1.4.0.0')
        );
        if($group == '0'){
            return true;
        }
        if(in_array(Mage::getVersion(), $gVersion[$group])){
            return true;
        }
        return  false;
    }
    
    /**
     * get main table alias magento version
     */
    public function getMainTable(){
        $gVersion = array(
            1=>array('1.4.1.0','1.4.1.1','1.4.2.0','1.5.1.0','1.6.0.0','1.8.0.0',
                '1.9.0.0','1.9.1.1','1.10.0.1','1.10.1.1','1.11.0.0','1.12.0.0','1.13.0.0'),
            2=>array('1.4.0.1','1.4.0.0')
        );
        if(in_array(Mage::getVersion(), $gVersion[2])){
            return 'e';
        }
        return  'main_table';
    }
}
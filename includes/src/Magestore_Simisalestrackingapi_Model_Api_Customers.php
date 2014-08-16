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
class Magestore_Simisalestrackingapi_Model_Api_Customers extends Magestore_Simisalestrackingapi_Model_Api_Abstract
{
    protected $_newCustomers = array(); //array('id'=>'1');
    protected $_collection = '';
    /**
     * api page Dashboard
     *
     * ?call=<customers>
     * [& params={
     *      "filter":"abc",
     *      "page":1|2|3|...,
     *      "limit":10,
     *      "group":0
     * }]
     * 
     * @param type $params
     * @return json array
     */
    public function apiIndex($params){
        $filter = '';
        if(isset($params['filter'])){
            $filter = $params['filter'];
        }
        //get top or down list
        if(isset($params['top']) && isset($params['slice'])){
            if(empty($params['top']) || empty($params['slice'])){
                //throw new Exception($this->_helper->__('No params top and slice'),130);
            }
            $params['top'] = (int)$params['top'];
        }
        $customers_data = Mage::getModel('simisalestrackingapi/customers')->getCustomers($filter, $params['page'], $params['limit'], $params['top'], $params['slice']);
        $customers_collection = Mage::getModel('simisalestrackingapi/customers')->getCustomerCollection($filter);
        $num_customers = $customers_collection->getSize();
        //$this->_bindNewCustomers();
        Mage::helper('simisalestrackingapi')->resetTimeNewCustomers();
        //get all customer ids
        //$_select = clone $customers_collection->getSelect();
        //$_select->reset(Zend_Db_Select::COLUMNS)->columns('entity_id');
        //$_select->order('e.created_at DESC');
        //$_select->limit(1000);
        //$db = Mage::getSingleton('core/resource')->getConnection('core_read');
        //$all_cus_ids = $db->fetchCol($_select); // array date to clear index
        //if param group
        if(!isset($params['group'])){
            $params['group'] = '0';
        }
        if($params['group'] != '0' && $params['group'] != '1'){
            throw new Exception($this->_helper->__('group: 0 or 1'),14);
        }
        $data = $this->bindCustomerOrderNumber($customers_data, $params['group']);
        $result = array(
            'customer_number'     => $num_customers,
            'data'          => $data,
            'list_ids'      => $this->_list_ids
        );
        return $result;
    }
    
    
    /**
    * order detail by order id
    * ? <call=customers.view>
     * <params={
     *      "id":"1,2,3,4,..."
     * }>
    */
    public function apiView($params){
        if(!isset($params['id']) || $params['id'] == ''){
            throw new Exception($this->_helper->__('Not defined param name "id" or value is null'), 21);
        }
        $customer = Mage::getModel('customer/customer')->load($params['id']);
        if($customer->getId() == ''){
            throw new Exception($this->_helper->__('No customer'), 22);
        }
        
        //get next and preview customer ids
        //get next
        $collection = Mage::getModel('customer/customer')->getCollection();
        $next_ids = $pre_ids = array('','');
        if(isset($params['filter'])){
            if($params['filter'] != ''){
                $collection = Mage::getModel('simisalestrackingapi/customers')->getCustomerFilter($params['filter']);
            }
        }
        $collection->getSelect()
            ->where('e.entity_id < ?', $params['id'])
            ->limit(2)
            ->order('e.created_at DESC');
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->columns('entity_id');
        $temp = $collection->getColumnValues('entity_id');
        $next_ids[0] = isset($temp[0])?$temp[0]:'';
        $next_ids[1] = isset($temp[1])?$temp[1]:'';
        //get preview
        $collection = Mage::getModel('customer/customer')->getCollection();
        if(isset($params['filter'])){
            if($params['filter'] != ''){
                $collection = Mage::getModel('simisalestrackingapi/customers')->getCustomerFilter($params['filter']);
            }
        }
        $collection = Mage::getModel('simisalestrackingapi/customers')->getCustomerFilter($params['filter']);
        $collection->getSelect()
            ->where('e.entity_id > ?', $params['id'])
            ->limit(2)
            ->order('e.created_at ASC');
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->columns('entity_id');
        $temp = $collection->getColumnValues('entity_id');
        $pre_ids[0] = isset($temp[0])?$temp[0]:'';
        $pre_ids[1] = isset($temp[1])?$temp[1]:'';
        //zend_debug::dump($pre_ids);
        //zend_debug::dump($next_ids);die;
        $orders = Mage::getModel('simisalestrackingapi/customers')->getOrders($params['id']); //get orders list
        
        $b_address = array('telephone'=>'', 'street'=>'', 'city'=>'',
                            'region'=>'', 'post_code' =>'', 'country' =>'');
        if(($builling_address = $customer->getDefaultBillingAddress())){
            $b_address = array(
                'telephone' => (string)$builling_address->getTelephone(),
                'street'    => (string)$builling_address->getStreetFull(),
                'city'      => (string)$builling_address->getCity(),
                'region'    => (string)$builling_address->getRegion(),
                'post_code' => (string)$builling_address->getPostcode(),
                'country'   => (string)$builling_address->getCountryModel()->getName()
            );
        }
        
        $s_address = array('telephone'=>'', 'street'=>'', 'city'=>'',
                            'region'=>'', 'post_code' =>'', 'country' =>'');
        if(($shipping_address = $customer->getDefaultShippingAddress())){
            $s_address = array(
                'telephone' => (string)$shipping_address->getTelephone(),
                'street'    => (string)$shipping_address->getStreetFull(),
                'city'      => (string)$shipping_address->getCity(),
                'region'    => (string)$shipping_address->getRegion(),
                'post_code' => (string)$shipping_address->getPostcode(),
                'country'   => (string)$shipping_address->getCountryModel()->getName()
            );
        }
        //zend_debug::dump($orders); die;
        $order_item = array();
        foreach($orders as $order){
            $order_item[] = array(
                'date'      =>  Mage::helper('core')->formatDate($order['updated_at'], 'medium', true),
                'increment_id' =>  $order['increment_id'],
                'status'    =>  $order['status'],
                'value'     =>  Mage::helper('core')->currency($order['grand_total'], true, false)
            );
        }
        $customer_model = Mage::getModel('simisalestrackingapi/customers');
        $order_sales = array(
            'lifetime_sales' => Mage::helper('core')->currency($customer_model->getLifetimeSales($customer->getId()), true, false),
            'orders'    =>  $order_item,
            'total'     =>  Mage::helper('core')->currency($this->getTotalValue($orders), true, false)
        );
        
        $data = array(
            'id'            =>  (int)$customer->getId(),
            'next_ids'      =>  $next_ids,
            'prev_ids'       =>  $pre_ids,
            'customer_name' =>  $customer->getName(),
            'customer_email'=>  $customer->getEmail(),
            'created_at'    =>  Mage::helper('core')->formatDate($customer->getCreatedAt(), 'medium', true),
            'builling_adress'   => $b_address,
            'shipping_adress'   =>  $s_address,
            'order_items'   =>  $order_sales
        );
        //$this->_helper->readNewCustomer($params['id']); //set read customer
        return $data;
   }
   
    /**
     * bind number order of customer
     */
    public function bindCustomerOrderNumber($data, $group = 0){
        $temp = array();
        if($group){
            $group = array();
            $pre_date = '';
            $ids_temp = array();
            foreach ($data as $c){
                $ids_temp[] = $c['id'];
                $zdate = Mage::app()->getLocale()->date(strtotime($c['created_at']), null, null, true);
                if($pre_date != ''){
                    if($zdate->compareDay($pre_date) !== 0 ){
                        $temp[] = array(
                            'group_date'    =>  $pre_date->toString(Zend_Date::DATE_MEDIUM),
                            'items'         =>  $group
                        );
                        $group = array();
                    }
                }
                $c['created_at'] = Mage::helper('core')->formatDate($c['created_at'],'medium',true);
                $c['order_number'] = $this->getNumberOrderByCustomerId($c['id']);
                $c['is_new']    =   0;//$this->isNew($c['id']);
                $c['is_unread'] =   0;//$this->isUnread($c['id']);
                $group[] = $c;
                $pre_date = clone $zdate;
            }
            if(count($group)>0){
                $temp[] = array(
                    'group_date'    =>  $pre_date->toString(Zend_Date::DATE_MEDIUM),
                    'items'         =>  $group
                );
            }
            $this->_list_ids = $ids_temp;
        }else{
            $ids_temp = array();
            foreach ($data as $c){
                $ids_temp[] = $c['id'];
                $c['created_at'] = Mage::helper('core')->formatDate($c['created_at'],'medium',true);
                $c['order_number'] = $this->getNumberOrderByCustomerId($c['id']);
                $c['is_new']    =   0;//$this->isNew($c['id']);
                $c['is_unread'] =   0;//$this->isUnread($c['id']);
                $temp[] = $c;
            }
            $this->_list_ids = $ids_temp;
        }
        return $temp;
    }
    
    
    /*
     * get number of orders follow customer_id
     * @parameter customer_id
     * return number order
     */
    public function getNumberOrderByCustomerId($customerId){
        $collection = Mage::helper('simisalestrackingapi')->getSalesCollections();
        $numOrder = 0;
        if($customerId != 'NULL'){
            $collection->addAttributeToSelect('entity_id')
                    ->addFieldToFilter('customer_id',$customerId);
            $numOrder = $collection->getSize();
        }
        return $numOrder;
    }
    
    public function _bindNewCustomers(){
        $newcustomers = Mage::getModel('simisalestrackingapi/customers')->getNewCustomer();
        $newUnreads = Mage::getSingleton('adminhtml/session')->getNewCustomerUnreads();
        foreach ($newcustomers as $cus){
            $this->_newCustomers[$cus->getId()] = '1';
            $newUnreads[$cus->getId()] = '1';
        }
        Mage::getSingleton('adminhtml/session')->setNewCustomerUnreads($newUnreads);
        //reset time after bind new
        Mage::helper('simisalestrackingapi')->resetTimeNewCustomers();
    }

    /**
     * check order is new or not
     * @return type
     */
    public function isNew($id){
        if(isset($this->_newCustomers[$id])){
            if($this->_newCustomers[$id] == '1'){
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
        $newUnreads = Mage::getSingleton('adminhtml/session')->getNewCustomerUnreads();
        if(isset($newUnreads[$id])){
            if($newUnreads[$id] == '1'){
                return true;
            }
        }
        return false;
    }
    
    
    /**
     * total value of order list
     */
    public function getTotalValue($orders){
        $total = 0;
        if($orders){
            foreach ($orders as $order){
                $total += $order['grand_total'];
            }
        }
        return $total;
    }
}

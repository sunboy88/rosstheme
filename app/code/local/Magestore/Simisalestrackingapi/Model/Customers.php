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
 * Simisalestrackingapi Customers Model
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Customers extends Magestore_Simisalestrackingapi_Model_Api_Abstract
{
    /*
     * get customer list
     */
    public function getCustomers($filter = '', $page = 1, $limit = 10, $top = '', $slice = ''){
        Mage::helper('simisalestrackingapi')->setLimit($limit);//reset limit
        $collection = $this->getCustomerFilter($filter);
        //get up or down list
        if(!empty($top) && !empty($slice)){
            if($slice == 'up'){
                $collection->getSelect()->where("e.entity_id > {$top}");
            }else if($slice == 'down'){
                $collection->getSelect()->where("e.entity_id <= {$top}");
            }
        }
        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                    'id' => 'e.entity_id',
                    'name' => "CONCAT( `customer_firstname_table`.`value`,' ',`customer_lastname_table`.`value`)",
                    'email' => 'e.email',
                    //'increment_id' => 'e.increment_id',
                    'created_at' => 'e.created_at',//"DATE_FORMAT(e.created_at,'%b %d %Y %h:%i %p')",
                ))
            ->limit(Mage::helper('simisalestrackingapi')->getLimit(),
                $page * Mage::helper('simisalestrackingapi')->getLimit())//limit page
            ->order('e.created_at desc');
        $data = $collection->getData();
        //get extra info
        $i = 0;
        foreach($collection as $customer){
            $customer = $customer->load($data[$i]['id']);
            $address = $customer->getPrimaryAddress('default_billing');
            if($address){
                $data[$i]['telephone'] = $address->getTelephone();
                $country_name = Mage::getModel('directory/country')->load($address->getCountry())->getName();
                $data[$i]['country'] = $country_name;
            }else{
                $data[$i]['telephone'] = 'No phone';
                $data[$i]['country'] = 'Unknown';
            }
            // Correct Data
            if (is_null($data[$i]['telephone'])) {
            	$data[$i]['telephone'] = 'No phone';
            }
            if (is_null($data[$i]['name'])) {
            	$data[$i]['name'] = ' ';
            }
            if (is_null($data[$i]['email'])) {
                $data[$i]['email'] = ' ';
            }
            $i++;
        }
        
        return $data;
    }
    
    /**
     * get collection of customer list, copy of public function getCustomers($filter = '')
     * @param type $filter
     * @return collection
     */
    public function getCustomerCollection($filter = ''){
        $collection = $this->getCustomerFilter($filter);
        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                    'id' => 'e.entity_id',
                    'name' => "CONCAT( `customer_firstname_table`.`value`,' ',`customer_lastname_table`.`value`)",
                    'email' => 'e.email',
                    //'increment_id' => 'e.increment_id',
                    'created_at' => 'e.created_at',//"DATE_FORMAT(e.created_at,'%b %d %Y %h:%i %p')",
                ));
        return $collection;
    }

    public function getCustomerFilter($filter = ''){
        $firstname = Mage::getResourceSingleton('customer/customer')->getAttribute('firstname');     
        $lastname  = Mage::getResourceSingleton('customer/customer')->getAttribute('lastname'); 
        $collection = Mage::getModel('customer/customer')->getCollection();  
        $collection->getSelect()
                ->joinLeft(
                        array('customer_lastname_table' => $lastname->getBackend()->getTable()),
                        'customer_lastname_table.entity_id = e.entity_id
                         AND customer_lastname_table.attribute_id = '.(int) $lastname->getAttributeId() . '
                         ',
                        array('lastname'=>'value')
                 )
                 ->joinLeft(
                        array('customer_firstname_table' =>$firstname->getBackend()->getTable()),
                        'customer_firstname_table.entity_id = e.entity_id
                         AND customer_firstname_table.attribute_id = '.(int) $firstname->getAttributeId() . '
                         ',
                        array('firstname'=>'value')
                 );
		
        if($filter != ''){
            $collection->getSelect()
                ->where(
                    "CONCAT(`customer_firstname_table`.`value`,' ',`customer_lastname_table`.`value`) like '%".$filter."%' 
                    OR `e`.`email` like '%".$filter."%'"
                );
        }
        return $collection;
    }

    /**
     * get order by Customer Id
     * @param type $customer_id
     * @return array order datas
     */
    public function getOrders($customer_id){
        $collection = Mage::getResourceModel('sales/order_collection');
        //fill by store
        $storeId = Mage::helper('simisalestrackingapi')->currentStoreId();
        if($storeId != 0){
            if($this->_helper->version(2)){
                $collection->addAttributeToFilter('store_id', $storeId);
            }else{
                $collection->addAttributeToFilter("{$this->mainTable}.store_id", $storeId);
            }
        }
        //fill by date in settings bestsellers
//        $fromDate = Mage::helper('simisalestrackingapi')->timeBestsellers();
//        $fromDate->setTime('00:00:00');
//        $toDate = Mage::app()->getLocale()->date();
//        $collection->addAttributeToFilter('main_table.updated_at',
//                array( 'from' => gmdate("Y-m-d H:i:s", $fromDate->getTimestamp()),
//                    'to' => gmdate("Y-m-d H:i:s", $toDate->getTimestamp()) 
//                ));
        //fill by customer id
        if($this->_helper->version(2)){
            $collection->addAttributeToFilter('customer_id', $customer_id);
        }else{
            $collection->addAttributeToFilter("{$this->mainTable}.customer_id", $customer_id);
        }
        
        //sort newest orders by date
        $collection->getSelect()->order("{$this->mainTable}.updated_at DESC");
        //fetch data
        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                    'id' => 'entity_id',
                    'status' => 'status',
                    'increment_id' => 'increment_id',
                    'updated_at' => "updated_at",
                    'grand_total' => 'base_grand_total'
            ));
            // ->limit(10);
        return $collection->getData();
    }
    
    /**
     * get life time sales value of customer info
     */
    public function getLifetimeSales($customer_id){
        $collection = Mage::getResourceModel('sales/order_collection');
        //fill by store
        $storeId = Mage::helper('simisalestrackingapi')->currentStoreId();
        if($storeId != 0){
            if($this->_helper->version(2)){
                $collection->addAttributeToFilter('store_id', $storeId);
            }else{
                $collection->addAttributeToFilter("{$this->mainTable}.store_id", $storeId);
            }
        }
        //fill by customer id
        if($this->_helper->version(2)){
            $collection->addAttributeToFilter('customer_id', $customer_id);
        }else{
            $collection->addAttributeToFilter("{$this->mainTable}.customer_id", $customer_id);
        }
        //fetch data
        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                    'total' => 'SUM(base_grand_total)'
            ))
            ->group("{$this->mainTable}.customer_id");
        $first_item = $collection->getFirstItem();
        return $first_item->getData('total');
    }
    
    public function getNewCustomer(){
        $lastTime = Mage::helper('simisalestrackingapi')->getTimeNewCustomers();
        $collection = Mage::getModel('customer/customer')->getCollection();
        //collec new customer
        $collection->getSelect()
            ->where('e.created_at >= ?', $lastTime);
        //prepare select to show
        $collection->getSelect()->order('e.created_at desc');
        return $collection;
    }
    
    public function getCountNew(){
        return $this->getNewCustomer()->getSize();
    }
}

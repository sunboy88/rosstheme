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
class Magestore_Simisalestrackingapi_Model_Newcustomers extends Mage_Core_Model_Abstract {

     public function getNewCustomer($filter = null){
        $lastTime = Mage::helper('simisalestrackingapi')->getTimeNewCustomers();
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
        //collec new customer
        $collection->getSelect()
            ->where('e.created_at >= ?', $lastTime);
        //get filter
        if($filter != ''){
            $collection->getSelect()
                ->where(
                    "CONCAT(`customer_firstname_table`.`value`,' ',`customer_lastname_table`.`value`) like '%".$filter."%' 
                    OR `e`.`email` like '%".$filter."%'"
                );
        }
        //prepare select to show
        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                    'id' => 'e.entity_id',
                    'name' => "CONCAT( `customer_firstname_table`.`value`,' ',`customer_lastname_table`.`value`)",
                    'email' => 'e.email',
                    'increment_id' => 'e.increment_id',
                    'created_at' => 'e.created_at',//"DATE_FORMAT(e.created_at,'%b %d %Y %h:%i %p')",
                ))
            //->limit(Mage::helper('simisalestrackingapi')->getLimit()) // not limit new customers
            ->order('e.created_at desc');
        
        $data = $collection->getData();
        //get extra info - add telephone and country
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
            $i++;
        }
        
        return $data;
    }
}


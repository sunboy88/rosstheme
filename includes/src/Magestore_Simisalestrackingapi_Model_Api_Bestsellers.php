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
 * SimiSalestracking Api Dashboard Server Model
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Api_Bestsellers extends Magestore_Simisalestrackingapi_Model_Api_Abstract
{
    /**
     * api page Dashboard
     * 
     * ?call=bestsellers 
     * & params = {
     *      "page":"1",
     *      "limit":"10",
     *      "store":"1",
     *      "order_status":"string|array()",
     *      "date_range":"1d|7d|15d|30d|3m|6m|1y|2y|lt"
     * }
     */
    public function apiIndex($params){
        
        //if filter by order status
        if(isset($params['order_status'])){
            if(!is_array($params['order_status'])){
                $params['order_status'] = array($params['order_status']);
            }
            $this->_helper->setOrderStatus($params['order_status']);
        }
        //if filter by date range
        if(isset($params['date_range'])){
            $this->_helper->setBestsellersDateRangeCode($params['date_range']);
        }
        
        $block_bestsellers = Mage::getBlockSingleton('simisalestrackingapi/bestsellers');
        $rows = array();
        $number = 0; //number of bestsellers filtered
        if(($collection = $this->_helper->getBestsellersCollection())){
            $collection->getSelect()
                ->limit($params['limit'],$params['page']*$params['limit']);
            $data = $collection->getData();
            $number = $this->_helper->getNumBestsellers();
            foreach ($data as $r){
                $rows[] = array(
                    'product_id'    =>  (int)$r['product_id'],
                    'sku'           =>  $r['sku'],
                    'name'          =>  $r['product_name'],
                    'qty'           =>  (int)$r['qty'],
                    'sales'         =>  Mage::helper('core')->currency($r['sales'], true, false)
                );
            }
        }
        return array(
            'title'        =>  $block_bestsellers->getTitleTime(),
            'updated_at'   =>  $block_bestsellers->getUpdatedTime(),
            'is_old'       =>  $block_bestsellers->isOld(),
            'num_bestsellers' => $number,
            'data'         =>  $rows
        );
    }
    
    /**
     * get order list by product id
     * 
     * ?call=bestsellers 
     * <&params={
     *      "product_id":123,
     *      "order_status":string|["complete, ..."],
     *      "store":"0|1|2|...",
     *      "page":1,
     *      "limit":10
     * }>
     * 
     * @param type $params
     * @return json array
     * @throws Exception
     */
    public function apiView($params){
        if(!isset($params['product_id']) || $params['product_id'] == ''){
            throw new Exception($this->_helper->__('No product_id'), 13);
        }
        //if filter by order status
        if(isset($params['order_status'])){
            if(!is_array($params['order_status'])){
                $params['order_status'] = array($params['order_status']);
            }
            $this->_helper->setOrderStatus($params['order_status']);
        }
        //if filter by date range
        if(isset($params['date_range'])){
            $this->_helper->setBestsellersDateRangeCode($params['date_range']);
        }
        
        //filter by from to date time
        $from = $this->_helper->timeBestsellers(); //zend_date by time bestsellers
        $orders_collection = Mage::getModel('simisalestrackingapi/orders')->getOrderByProduct($params['product_id'],$from);
        $product = Mage::getModel('catalog/product')->load($params['product_id']);
        // Mage::register('orders_search', $orders_collection);
        //$block_search = Mage::getBlockSingleton('simisalestrackingapi/orders_search');
        //$block_search->setCollection($orders_collection);
        
        $pro_name  = $product->getName();
        //$num_order = $block_search->numOrder();
        //$total_sales = $block_search->getTotalSales();
        //get all order ids
        //$all_order_ids = $this->getAllOrderIds($orders_collection);
        //get top or down list
        if(isset($params['top']) && isset($params['slice'])){
            if(!empty($params['top']) && !empty($params['slice'])){
                $params['top'] = (int)$params['top'];
                if($params['slice'] == 'up'){
                    $orders_collection->getSelect()->where("{$this->mainTable}.entity_id > {$params['top']}");
                }else if($params['slice'] == 'down'){
                    $orders_collection->getSelect()->where("{$this->mainTable}.entity_id <= {$params['top']}");
                }
                //throw new Exception($this->_helper->__('No params top and slice'),130);
            }
        }
        //limit page
        $orders_collection->getSelect()->limit($params['limit'],$params['page']*$params['limit']);//limit page
        //if param group
        if(!isset($params['group'])){
            $params['group'] = '0';
        }
        if($params['group'] != '0' && $params['group'] != '1'){
            throw new Exception($this->_helper->__('group: 0 or 1'),14);
        }
        $data = $this->convertOrderData($orders_collection, $params['group']);
        $result = array(
            'product_name'  => $pro_name,
            'data'          => $data,
            'list_ids'      => $this->_list_ids
        );
        return $result;
    }
    
    /**
     * ? call=bestsellers.refresh & params={"for"="all|new"}
     */
    public function apiRefresh($params){
        if(isset($params['for'])){
            switch ($params['for']){
                case 'all':
                    if(!Mage::getModel('simisalestrackingapi/bestsellers')->refresh(true)){ //all
                        throw new Exception($this->_helper->__('Refresh all failed'), 15);
                    }
                    break;
                case 'new':
                    if(!Mage::getModel('simisalestrackingapi/bestsellers')->refresh()){
                        throw new Exception($this->_helper->__('Update failed'), 16);
                    }
                    break;
                default :
                    throw new Exception($this->_helper->__('Unknown refresh type'), 17);
                    break;
            }
            return $this->_helper->__('Refresh completed');
        }
        throw new Exception($this->_helper->__('Param name "for" is not defined'), 18);
    }
}

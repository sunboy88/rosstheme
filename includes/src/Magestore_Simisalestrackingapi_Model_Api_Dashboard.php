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
class Magestore_Simisalestrackingapi_Model_Api_Dashboard extends Magestore_Simisalestrackingapi_Model_Api_Abstract
{
    /**
     * api page Dashboard
     * ?call=dashboard
     * &params={
     *  "showbestsellers":"0|1",
     *  "order_status"=[],
     *  "store":"0"
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
        $block_dashboard = Mage::getBlockSingleton('simisalestrackingapi/dashboard');
        //get bestsellers
        $bestsellers = array();
        if(isset($params['showbestsellers']) && $params['showbestsellers'] == '1'){
            //if filter by date range
            if(isset($params['date_range'])){
                $this->_helper->setBestsellersDateRangeCode($params['date_range']);
            }
            if(($collection = $this->_helper->getBestsellersCollection())){
                $collection->setPage(0, 5);
                $data = $collection->getData();
                foreach ($data as $r){
                    $bestsellers[] = array(
                        'product_id'    => (int)$r['product_id'],
                        'sku'   =>  $r['sku'],
                        'name'  =>  $r['product_name'],
                        'qty'   =>  (int)$r['qty'],
                        'sales' =>  Mage::helper('core')->currency($r['sales'], true, false)
                    );
                }
            }
        }
        return array(
            'new'       =>  array(
                'customers' => Mage::getModel('simisalestrackingapi/customers')->getCountNew(), //int
                'sales'     => Mage::getSingleton('simisalestrackingapi/orders')->getNewSales(),
                'orders'    => Mage::getSingleton('simisalestrackingapi/orders')->getCountNew(),
            ),
            'sales'     =>  array(
                'today'     =>  $block_dashboard->getTodaySales(),
                'yesterday' =>  $block_dashboard->getYesterdaySales(),
                'accumulation'  =>  $block_dashboard->getThisMonthSoFar(),
                'avg'       =>  $block_dashboard->getThisMonthDayAvg(),
                'forecast'  =>  $block_dashboard->getThisMonthForecast(),
                'month_list'=>  Mage::getBlockSingleton('simisalestrackingapi/dashboard_totalsales')->getTotalsalesPages() 
            ),
            'bestsellers'   =>  $bestsellers,
            'order'    =>  array(
                'today'     =>  $block_dashboard->getNumTodayOrder(),
                'yesterday' =>  $block_dashboard->getNumYesterdayOrder(),
                'max_today' =>  $block_dashboard->getMaxTodayOrder(),
                'min_today' =>  $block_dashboard->getMinTodayOrder(),
                'avg_today' =>  $block_dashboard->getAvgTodayOrder()
            ),
            'customer' =>  array(
                'today'     =>  $block_dashboard->getTodayCustomer(),
                'yesterday' =>  $block_dashboard->getYesterdayCustomer(),
                'online'    =>  $block_dashboard->getOnlineCustomer()
            )
        );
    }
    
    /**
     * get total sales page
     * 
     * ?call=dashboard.sales_month
     * &params = {
     *      "id":1
     * }
     * 
     */
    public function apiTotalsales($params){
        if(!isset($params['month']) || $params['month'] == ''){
            throw new Exception($this->_helper->__('Not defined param name "month" or value is null'), 10);
        }
        //if filter by order status
        if(isset($params['order_status'])){
            if(!is_array($params['order_status'])){
                $params['order_status'] = array($params['order_status']);
            }
            $this->_helper->setOrderStatus($params['order_status']);
        }
        $data_month = Mage::getModel('simisalestrackingapi/totalsales')->getSalesOfMonth($params['month']);
        if($data_month['data']->getSize() <= 0){
            //throw new Exception($this->_helper->__('No have this month'), 11);
        }
        Mage::register('simisalestrackingapi_totalsales_page_data', $data_month['data']);
        Mage::register('simisalestrackingapi_totalsales_page_id', $params['month']);
        $block = Mage::getBlockSingleton('simisalestrackingapi/dashboard_totalsales');
        $page_key = $block->getTotalsalesPages();
        if(!is_array($page_key)){
            throw new Exception($this->_helper->__('No data'), 12);
        }
        $data = $block->getCurrentPageDatas();
        
        $z_date = Mage::app()->getLocale()->date();
        $z_date->set($params['month'], 'YYYY-MM');
        //$z_date->set($data_month['data']->getFirstItem()->getUpdatedAt(), Zend_Date::ISO_8601);
        //$z_date->setTimezone('Etc/UTC');
        $title = $z_date->toString('MMM YYYY');
        return array(
            'title' => $title,
            'total' => Mage::helper('core')->currency($data_month['totals'], true, false),
            'data'  => $data
        );
    }
    
    public function apiTotalsales_view($params){
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
        $collection = Mage::getModel('simisalestrackingapi/orders')->getOrdersByDate($date, $filter);
        //$all_order_ids = $this->getAllOrderIds($collection);
        if(isset($params['top']) && isset($params['slice'])){
            if(!empty($params['top']) && !empty($params['slice'])){
                $params['top'] = (int)$params['top'];
                if($params['slice'] == 'up'){
                    $collection->getSelect()->where($this->mainTable.".entity_id > {$params['top']}");
                }else if($params['slice'] == 'down'){
                    $collection->getSelect()->where($this->mainTable.".entity_id <= {$params['top']}");
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
}

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
 * Login Block
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Block_Dashboard extends Mage_Core_Block_Template {
    /**
     * get total sales of today
     * @return string totals price sales with currency
     */
    public function getTodaySales() {
        $date = Mage::app()->getLocale()->date();
        $time_from_today = $date->setTime('00:00:00');
        $total_sales = $this->getTotalSale($time_from_today);
        return Mage::helper('core')->currency($total_sales, true, false);
    }
    
    /**
     * get totals sales of yesterday
     * @return string price with currency
     */
    public function getYesterdaySales() {
        $from = Mage::app()->getLocale()->date();
        $from->subDay(1);
        $to_time = clone $from;
        $from->setTime('00:00:00');
        $to_time->setTime('23:59:59');
        $total_sales = $this->getTotalSale($from, $to_time);
        return Mage::helper('core')->currency($total_sales, true, false);
    }
    
    /**
     * get total sales values of sum base_grand_total by amount of time
     */
    protected function getTotalSale($time, $to_time = '') {
        $collection = Mage::helper('simisalestrackingapi')->getSalesCollections(); //filted by store
        // filted by status
        $status = Mage::helper('simisalestrackingapi')->getOrderStatus();
        if(!empty($status)){
            $collection->addAttributeToFilter('status', array('in'=>$status));
        }else{
            $collection->addAttributeToFilter('status', array('in'=>array('')));
        }
        // filter complete
        $collection->addAttributeToFilter('updated_at', array('from'=>gmdate("Y-m-d H:i:s", $time->getTimestamp())))
            ->addAttributeToSelect('base_grand_total')
            ->addAttributeToSelect('base_total_refunded')
            ->addAttributeToSelect('base_total_paid')
            ->addAttributeToSelect('status');
        if($to_time != ''){
            $collection->addAttributeToFilter('updated_at', array('to'=>gmdate("Y-m-d H:i:s", $to_time->getTimestamp())));
        }
        //$grand_total = 0;
        //foreach ($collection as $item) {
        //    $grand_total += $item->getBaseGrandTotal();
        //}
        $rows = $collection->getData();
        $total = 0;
        foreach($rows as $r){
            if($r['status'] == 'complete'){
                if($r['base_total_refunded'] == ''){
                    $total += (float)$r['base_total_paid'];
                }else{
                    $total += (float)$r['base_total_paid'] - (float)$r['base_total_refunded'];
                }
            }else{
                $total += (float)$r['base_grand_total'];
            }
        }
        return $total;
    }

    /**
     * get total sales of this month so far (from start day of month to current time)
     * @return type
     */
    public function getThisMonthSoFar() {
        $first_day_of_month = Mage::app()->getLocale()->date();
        $first_day_of_month->setDay(1)->setTime('00:00:00');
        return Mage::helper('core')->currency($this->getTotalSale($first_day_of_month), true, false);
    }

    /**
     * get average per day of this month
     * @return string
     */
    public function getThisMonthDayAvg() {
        $day_avg = $this->getAvgSalesDay();
        return  Mage::helper('core')->currency($day_avg, true, false);
    }
    /**
     * get sales of this month in forecast
     * @return total sales with currency
     */
    public function getThisMonthForecast() {
        $date = Mage::app()->getLocale()->date();
        $month = $date->toString('M', 'iso');
        $year = $date->toString('Y', 'iso');
        $days_of_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthforecast = $this->getAvgSalesDay(true) * $days_of_month;
        return Mage::helper('core')->currency($monthforecast, true, false);
    }
    
    /**
     * get average total sales in per day of month
     * @return float is average by step hour
     */
    public function getAvgSalesDay($forecast = false){
        $collection = Mage::helper('simisalestrackingapi')->getSalesCollections();
        // filted by status
        $status = Mage::helper('simisalestrackingapi')->getOrderStatus();
        if(!empty($status)){
            $collection->addAttributeToFilter('status', array('in'=>$status));
        }else{
            $collection->addAttributeToFilter('status', array('in'=>array('')));
        }
        //fill by this current month
        $date = Mage::app()->getLocale()->date();
        $first_day_of_month = clone $date;
        $first_day_of_month->setDay(1)->setTime('00:00:00');
        $lastMonth = 0;
        if ($forecast && date('j') < 15) {
        	$lastMonth = 15 - date('j');
        	$first_day_of_month->subDay($lastMonth);
        }
        $collection->addAttributeToFilter('updated_at', array('from'=>gmdate("Y-m-d H:i:s", $first_day_of_month->getTimestamp())));
        //calculate sum grand total and count
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                ->columns(array('sum_total' => "SUM(IF(`status` = 'complete', IF(`base_total_refunded` IS NULL, `base_total_paid`, `base_total_paid`-`base_total_refunded`), `base_grand_total`))"))//'SUM(base_grand_total)'))
                ->group('YEAR(updated_at)');
        $grand_total_sum = 0;
        foreach($collection as $item){
            $grand_total_sum += $item->getSumTotal();
        }
        // calc hours number
        $day = (int)$date->toString('d', 'iso') - 1;
        $hour = (int)$date->toString('H', 'iso');
        $h = $day*24 + $hour;
        $h += $lastMonth*24;
        if($h){
            $avg_hour = $grand_total_sum / $h;
        }else{
            $avg_hour = 0;
        }
        return $avg_hour*24;
    }
    
    

    public function getNumTodayOrder() {
        $date = Mage::app()->getLocale()->date();
        $date->setTime('00:00:00');
        $num_order = $this->getNumberOrder($date);
        return $num_order;
    }

    public function getNumYesterdayOrder() {
        $from = Mage::app()->getLocale()->date();
        $from->subDay(1);
        $to = clone $from;
        $from->setTime('00:00:00');
        $to->setTime('23:59:59');
        $num_order = $this->getNumberOrder($from, $to);
        return $num_order;
    }

    /**
     * get number order by date time
     * @param type $time
     * @return int
     */
    protected function getNumberOrder($from, $to = '') {
        if($to == ''){
            $to = Mage::app()->getLocale()->date();
        }
        $collections = $this->getOrderCollection($from, $to)
                ->addAttributeToSelect('entity_id');
        if (isset($collections)) {
        	return $collections->getSize();
        }
        return 0;
    }
    /**
     * get max value of today order
     * @return string
     */
    public function getMaxTodayOrder(){
        $date = Mage::app()->getLocale()->date();
        $date->setTime('00:00:00');
        $collections = $this->getOrderCollection($date)
                ->addAttributeToSelect('base_grand_total')
                ->setOrder('base_grand_total','DESC');
        $collections->getSelect()->limit(1);
        $grand_total = $collections->getFirstItem()->getData('base_grand_total');
        return Mage::helper('core')->currency($grand_total, true, false);
    }
    /**
     * get min value of today order
     * @return string
     */
    public function getMinTodayOrder(){
        $date = Mage::app()->getLocale()->date();
        $date->setTime('00:00:00');
        $collections = $this->getOrderCollection($date)
                ->addAttributeToSelect('base_grand_total')
                ->setOrder('base_grand_total','ASC');
        $collections->getSelect()->limit(1);
        $grand_total = $collections->getFirstItem()->getData('base_grand_total');
        return Mage::helper('core')->currency($grand_total, true, false);
        
    }
    /**
     * get average value of today order
     * @return string
     */
    public function getAvgTodayOrder(){
        $date = Mage::app()->getLocale()->date();
        $date->setTime('00:00:00');
        $collections = $this->getOrderCollection($date)
                ->addAttributeToSelect('base_grand_total');
        $num_order = count($collections);
        $grand_total = 0;
        foreach ($collections as $item){
            $grand_total += $item->getBaseGrandTotal();
        }
        $avg = 0;
        if($num_order != 0){
            $avg = $grand_total/$num_order;
        }
        return Mage::helper('core')->currency($avg, true, false);
    }
    /**
     * get order collection by from date to date
     * @param type $from Zend_Date
     * @param type $to Zend_Date
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function getOrderCollection($from, $to = '') {
        $storeId  =  Mage::helper('simisalestrackingapi')->currentStoreId();
        $collections = Mage::getModel('sales/order')->getCollection();
        if($storeId != 0){
            $collections->addFieldToFilter('store_id',$storeId);
        }
        // filted by status
        $status = Mage::helper('simisalestrackingapi')->getOrderStatus();
        if(!empty($status)){
            $collections->addAttributeToFilter('status', array('in'=>$status));
        }else{
            $collections->addAttributeToFilter('status', array('in'=>array('')));
        }
        if($to == ''){
            $to = Mage::app()->getLocale()->date();
        }
        $collections->addFieldToFilter('updated_at', 
            array(
                'from'=>gmdate("Y-m-d H:i:s", $from->getTimestamp()),
                'to'=>gmdate("Y-m-d H:i:s", $to->getTimestamp())
            ));
        return $collections;
    }
    
    /**
     * get number today customer has created account
     * @return int
     */
    public function getTodayCustomer(){
        $date = Mage::app()->getLocale()->date();
        $date->setTime('00:00:00');
        $collection  = Mage::getModel('customer/customer')->getCollection();
        $collection->addAttributeToSelect('updated_at')
            ->addAttributeToFilter('created_at',
                array('from' => gmdate("Y-m-d H:i:s", $date->getTimestamp())));
        return $collection->getSize();
    }
    /**
     * get number yesterday customer has created account
     * @return int
     */
    public function getYesterdayCustomer(){
        $from = Mage::app()->getLocale()->date();
        $from->subDay(1);
        $to = clone $from;
        $from->setTime('00:00:00');
        $to->setTime('23:59:59');
        $collection  = Mage::getModel('customer/customer')->getCollection();
        $collection->addAttributeToSelect('created_at')
            ->addAttributeToFilter('created_at',
            array(
                'from' => gmdate("Y-m-d H:i:s", $from->getTimestamp()),
                'to' =>   gmdate("Y-m-d H:i:s", $to->getTimestamp())
                ));
        return $collection->getSize();
    }
    /**
     * get number customer online
     * @return int
     */
    public function getOnlineCustomer(){
        $collection = Mage::getModel('log/visitor_online')
                ->prepare()
                ->getCollection();
        $collection->addFieldToFilter('visitor_type', array('eq'=>Mage_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER))->addCustomerData();
        return count($collection);
    }

}


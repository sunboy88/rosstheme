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
 * Dashboard Totalsales Block
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Block_Dashboard_Totalsales extends Mage_Core_Block_Template{
    
    /**
     * get the page render befor load
     * this is container pages (a page is a month)
     */
    public function getTotalsalesPages(){
        $minmonth = Mage::getModel('simisalestrackingapi/totalsales')->getMinMonth();
        $aMonth = explode('-', $minmonth);
        $curMonth = Mage::app()->getLocale()->date();
        $fromMonth = clone $curMonth;
        if($minmonth){
            $fromMonth->setYear($aMonth[0]);
            $fromMonth->setMonth($aMonth[1]);
        }
        $data = array();
        while ($curMonth->compareDate($fromMonth) != -1){
            $title = $curMonth->toString('MMM YYYY');
            $month = $curMonth->toString('YYYY-MM');
            $data[] = array('title'=>$title,'month'=>$month);
            $curMonth->subMonth(1);
        }
        Mage::getSingleton('adminhtml/session')->setTotalsalesMinmonth($minmonth);
        Mage::getSingleton('adminhtml/session')->setTotalsalesPages($data);
        return $data;
    }
    
    /**
     * get page of month rendered by controller
     * @return collection of the page
     */
    public function getCurrentPageDatas(){
        $collection = Mage::registry('simisalestrackingapi_totalsales_page_data');
        $curMonth = Mage::registry('simisalestrackingapi_totalsales_page_id'); //get current month Y-m
        $arrMonth = explode('-', $curMonth);
        $days_of_month = cal_days_in_month(CAL_GREGORIAN, $arrMonth[1], $arrMonth[0]);
        $data_collection = $collection->getData();
        $date = '';
        $data = array();
        $_minmonth = Mage::getSingleton('adminhtml/session')->getTotalsalesMinmonth(); //min month Y-m
        $cur_day = Mage::app()->getLocale()->date(Mage::getSingleton('core/date')->gmtTimestamp(), null, null);
        // merge data date
        for ($i=$days_of_month;$i>=1;$i--){
            if(isset($data_collection[0]['date'])){
                $dates = explode('/', $data_collection[0]['date']);
                $date = $dates[0];
            }
            if((int)$date === $i){
                //$obj = new Varien_Object();
                //$obj->setData($data_collection[0]);
                unset($data_collection[0]['updated_at']);
                $data_collection[0]['total_sales'] = Mage::helper('core')->currency($data_collection[0]['total_sales'], true, false);
                $data_collection[0]['percent'] = $data_collection[0]['percent'].'%';
                $data[] = $data_collection[0];
                array_shift($data_collection);
            }else{
                $data[] = array(
                    'date'=>sprintf("%02d", $i).'/'.$arrMonth[1].'/'.$arrMonth[0],
                    'total_sales'=>Mage::helper('core')->currency(0, true, false),
                    'percent'=>'0%');
            }
            // check breack merge end of min month
            if($_minmonth == $curMonth){
                    if(count($data_collection) <= 0) break;
            }
        }
        //remerge
        $temp = array();
        for($i=$days_of_month-1;$i>=0;$i--){
        	if (!$data[$i]) {
        		continue;
        	}
            $temp[] = $data[$i];
            //break today of month
            $today = $cur_day->toString('dd/MM/yyyy');
            if($today == $data[$i]['date']){
                break;
            }
        }
        //revert array
        return array_reverse($temp);
    }
    
    /**
     * get title (it is month)
     */
    public function getTitle(){
        $title = Mage::registry('simisalestrackingapi_totalsales_page_title');
        $date = Mage::getModel('core/date')->gmtDate('M Y');
        if($title === $date){
            $title = $this->__('THIS MONTH');
        }
        return $title;
    }
    /**
     * get money total sales of month
     * @return type
     */
    public function getTotalSales(){
        return Mage::registry('simisalestrackingapi_totalsales_page_total');
    }
    /**
     * get id of the page
     * @return type
     */
    public function getPageId(){
        return Mage::registry('simisalestrackingapi_totalsales_page_id');
    }
    
}



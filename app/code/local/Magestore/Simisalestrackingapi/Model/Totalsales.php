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
 * Simisalestrackingapi Totalsales Model
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Totalsales extends Mage_Core_Model_Abstract
{
    protected $mainTable = 'main_table';
    /**
     * construct
     */
    public function __construct() {
        $this->mainTable = Mage::helper('simisalestrackingapi')->getMainTable();
    }
    /**
     * this function get total date of month
     * this collection will group by date
     * @param array $month to calc sales with timezone
     * @return mixed
     */
    public function getSalesOfMonth($month){
        // set date range
        //$from = clone $month;
        //$to = clone $month;
        //$to->addMonth(1)->subDay(1)->setTime('23:59:59');
        $collection = Mage::getResourceModel('sales/order_collection');
        $storeId = Mage::helper('simisalestrackingapi')->currentStoreId();
        if($storeId != 0){
            $collection->addAttributeToFilter('store_id', $storeId); //fill store
        }
        // calculate offset and get offset format +00:00
        $offset = -Mage::app()->getLocale()->date()->getGmtOffset(); // Offset in seconds to UTC 
        $offsetHours = round(abs($offset)/3600); 
        $offsetMinutes = round((abs($offset) - $offsetHours * 3600) / 60); 
        $offsetString = ($offset < 0 ? '-' : '+')
                    . ($offsetHours < 10 ? '0' : '') . $offsetHours . ':'
                    . ($offsetMinutes < 10 ? '0' : '') . $offsetMinutes;
        //calculate totals
        $view = clone $collection;
        // filted by status
        $order_status = Mage::helper('simisalestrackingapi')->getOrderStatus(); //get array
        if(!empty($order_status)){
            $view->addAttributeToFilter('status', array('in'=>$order_status));
        }else{
            $view->addAttributeToFilter('status', array('in'=>array(''))); //filter no
        }
        $view->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                'month' => "DATE_FORMAT(CONVERT_TZ(".$this->mainTable.".updated_at,'+00:00','".$offsetString."'), '%Y-%m')",
                'totals' => "SUM(IF(`status` = 'complete', IF(`base_total_refunded` IS NULL, `base_total_paid`, `base_total_paid`-`base_total_refunded`), `base_grand_total`))"//"SUM(`base_grand_total`)"//
            ))
            //->where("status = 'complete'")
            ->where("DATE_FORMAT(CONVERT_TZ(".$this->mainTable.".updated_at,'+00:00','".$offsetString."'), '%Y-%m') = ?", $month) //fill by month
            ->group("month");
        $totals = $view->getFirstItem()->getTotals();
        if($totals == '') $totals = 0;
        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->reset(Zend_Db_Select::WHERE)
            ->reset(Zend_Db_Select::GROUP)
            ->columns(array(
                'updated_at' => $this->mainTable.'.updated_at',
                'date' => "DATE_FORMAT(CONVERT_TZ(".$this->mainTable.".updated_at,'+00:00','".$offsetString."'), '%d/%m/%Y')",
                'total_sales' => "SUM(IF(`status` = 'complete', IF(`base_total_refunded` IS NULL, `base_total_paid`, `base_total_paid`-`base_total_refunded`), `base_grand_total`))",//"SUM(`base_grand_total`)",//
                'percent' => "IF({$totals}>0,ROUND(SUM(IF(`status` = 'complete', IF(`base_total_refunded` IS NULL, `base_total_paid`, `base_total_paid`-`base_total_refunded`), `base_grand_total`))/'{$totals}'*100, 2),0)"
            ))
            //->where("status = 'complete'")
            ->where("DATE_FORMAT(CONVERT_TZ(".$this->mainTable.".updated_at,'+00:00','".$offsetString."'), '%Y-%m') = ?", $month) //fill by month
            ->group("DATE_FORMAT(CONVERT_TZ(".$this->mainTable.".updated_at,'+00:00','".$offsetString."'), '%d/%m/%Y')")
            ->order($this->mainTable.'.updated_at DESC');
        // filted by status again
        if(!empty($order_status)){
            $view->addAttributeToFilter('status', array('in'=>$order_status));
        }else{
            $view->addAttributeToFilter('status', array('in'=>array(''))); //filter no
        }
        return array("totals"=>$totals, "data"=>$collection);
    }
    
    /**
     * this function return totals months and group month-year
     * @return array as groups (each group is an element of the array)
     */
    public function getMinMonth(){
        $collection = Mage::getResourceModel('sales/order_collection');
        $storeId = Mage::helper('simisalestrackingapi')->currentStoreId();
        if($storeId != 0){
            $collection->addAttributeToFilter('store_id', $storeId); //fill store
        }
        // calculate offset and get offset format +00:00
        $offset = Mage::getModel('core/date')->getGmtOffset(); // Offset in seconds to UTC 
        $offsetHours = round(abs($offset)/3600); 
        $offsetMinutes = round((abs($offset) - $offsetHours * 3600) / 60); 
        $offsetString = ($offset < 0 ? '-' : '+')
                    . ($offsetHours < 10 ? '0' : '') . $offsetHours . ':'
                    . ($offsetMinutes < 10 ? '0' : '') . $offsetMinutes;
        // filted by status
        $order_status = Mage::helper('simisalestrackingapi')->getOrderStatus(); //get array
        if(!empty($order_status)){
            $collection->addAttributeToFilter('status', array('in'=>$order_status));
        }else{
            $collection->addAttributeToFilter('status', array('in'=>array(''))); //filter no
        }
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                'month_year' => "DATE_FORMAT(CONVERT_TZ(".$this->mainTable.".updated_at,'+00:00','".$offsetString."'), '%Y-%m')"
            ))
            //->where("status = 'complete'")
            ->order('month_year ASC')
            ->limit(1);
        return $collection->getFirstItem()->getData('month_year');
    }
    
}

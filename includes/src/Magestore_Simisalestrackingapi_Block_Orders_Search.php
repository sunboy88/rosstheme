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
 * Orders Search Block
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Block_Orders_Search extends Magestore_Simisalestrackingapi_Block_Orders {
    
    protected $_sql = ''; //sql
    
    /**
     * get number of orders
     */
    public function numOrder(){
        $collection = Mage::registry('orders_search');
        return $collection->getSize();
    }
    
    public function getBestProductName(){
        $pro_id = Mage::app()->getRequest()->getPost('pro_id');
        if(Mage::getSingleton('adminhtml/session')->getCurrentTab()=='bestsellers' || $pro_id){
            return Mage::getSingleton('adminhtml/session')->getBestProductName();
        }else{
            return '';
        }
    }
}

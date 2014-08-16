<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Collpur_Block_Customer_Deals extends AW_Collpur_Block_Customer_Basedeal {

    
    public function _construct() {
        
        parent::_construct();
        $this->setTemplate('aw_collpur/customer/deals.phtml');
        $this->setAvailableDealsScope($this->getCustomerPurchases());
    }

    protected function _toHtml() {
        
        return parent::_toHtml();
    }

    public function getCustomerPurchases() {

        if (isset($this->_data['available_deals_scope'])) { return $this->getAvailableDealsScope(); }
        return Mage::getModel('collpur/dealpurchases')->getCollection()->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomer()->getId());
    }

    public function getDealByPurchase($id) {
 
        if(!$this->getCustomerDealsCollection()) {
            $dealCollection = Mage::getModel('collpur/deal')->getCollection()->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomer()->getId());
            $this->setCustomerDealsCollection($dealCollection);
        }

        return $this->getCustomerDealsCollection()->getItemById($id);
    }   

    protected function _prepareLayout() {

        $pager = $this->getLayout()->createBlock('page/html_pager', 'awcp_customer_deals_pager');
        //$pager->setAvailableLimit(array("10" => "10", "15" => "15", "25" => "25"));
        $pager->setLimitVarName('custlimit');
        $pager->setPageVarName('castvarname');
        $pager->setCollection($this->getCustomerPurchases());
        $this->setChild('awcp_customer_deals_pager', $pager);
    }

    public function getPagerHtml() {

        return $this->getChildHtml('awcp_customer_deals_pager');
    }

    public function isEnabled() {

        return true;
    }

     
}
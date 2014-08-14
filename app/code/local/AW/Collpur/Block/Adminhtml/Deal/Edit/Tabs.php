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


class AW_Collpur_Block_Adminhtml_Deal_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('collpur_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('collpur')->__('Deal'));
    }

    protected function _beforeToHtml() {
        $this->addTab('general_section', array(
            'label' => Mage::helper('collpur')->__('General Section'),
            'title' => Mage::helper('collpur')->__('General Section'),
            'content' => $this->getLayout()->createBlock('collpur/adminhtml_deal_edit_tab_general')->toHtml(),
            'active' => Mage::registry('collpur_deal')->getId() ? false : true
        ));

        $this->addTab('details_section', array(
            'label' => Mage::helper('collpur')->__('Details'),
            'title' => Mage::helper('collpur')->__('Details'),
            'content' => $this->getLayout()->createBlock('collpur/adminhtml_deal_edit_tab_details')->toHtml(),
        ));

        if ($deal = Mage::registry('collpur_deal')) {
            if ($deal->getId()) {
                $this->addTab('coupons_section', array(
                    'label' => Mage::helper('collpur')->__('Coupons'),
                    'title' => Mage::helper('collpur')->__('Coupons'),
                    'content' => $this->getLayout()->createBlock('collpur/adminhtml_deal_edit_tab_coupons')->toHtml(),
                ));
            } else {
                $this->addTab('coupons_section', array(
                    'label' => Mage::helper('collpur')->__('Coupons'),
                    'title' => Mage::helper('collpur')->__('Coupons'),
                    'content' => $this->getLayout()->createBlock('collpur/adminhtml_deal_edit_tab_couponsNotActive')->toHtml(),
                ));
            }
        }

        $this->addTab('orders_section', array(
            'label' => Mage::helper('collpur')->__('Orders'),
            'title' => Mage::helper('collpur')->__('Orders'),
            'content' => $this->getLayout()->createBlock('collpur/adminhtml_deal_edit_tab_orders')->toHtml(),
        ));

        $this->addTab('info_section', array(
            'label' => Mage::helper('collpur')->__('Information'),
            'title' => Mage::helper('collpur')->__('Information'),
            'content' => $this->getLayout()->createBlock('collpur/adminhtml_deal_edit_tab_info')->toHtml(),
        ));

        $this->_updateActiveTab();

        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab() {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if ($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }

}

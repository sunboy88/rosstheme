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


class AW_Collpur_CustomerController extends Mage_Core_Controller_Front_Action {

    public function preDispatch() {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initPage('awcp.deals.list');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Purchased deals'));
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        $this->_initPage('awcp.deals.view');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Purchased deals'));
        $this->renderLayout();
    }

    private function _initPage($id)
    {
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock)
            $navigationBlock->setActive('deals/customer/index');

        $block = $this->getLayout()->getBlock($id);
        if ($block)
            $block->setRefererUrl($this->_getRefererUrl());
    }

    public function printcouponAction()
    {
        $this->loadLayout('print');
        $this->renderLayout();
    }
}
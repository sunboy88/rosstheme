<?php

class Magestore_Affiliatepluslevel_IndexController extends Mage_Core_Controller_Front_Action {

    protected function _getAccountHelper() {
        return Mage::helper('affiliateplus/account');
    }

    public function listTierTransactionAction() {
        /* hainh edit 25-04-2014 */
        if (!Mage::helper('affiliatepluslevel')->isPluginEnabled()) {
            return;
        }
        if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }
        if ($this->_getAccountHelper()->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Commissions'));
        $this->renderLayout();
    }

    public function listTierAction() {
        /* hainh edit 25-04-2014 */
        if (!Mage::helper('affiliatepluslevel')->isPluginEnabled()) {
            return;
        }
        if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }
        if ($this->_getAccountHelper()->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Tier Affiliates'));
        $this->renderLayout();
    }

}

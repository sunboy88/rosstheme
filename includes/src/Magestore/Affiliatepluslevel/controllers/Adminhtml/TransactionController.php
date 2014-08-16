<?php

class Magestore_Affiliatepluslevel_Adminhtml_TransactionController extends Mage_Adminhtml_Controller_Action {

    public function tierAction() {
        /* hainh edit 25-04-2014 */
        if (!Mage::helper('affiliatepluslevel')->isPluginEnabled()) {
            return;
        }
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function tierGridAction() {
        /* hainh edit 25-04-2014 */
        if (!Mage::helper('affiliatepluslevel')->isPluginEnabled()) {
            return;
        }
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

}

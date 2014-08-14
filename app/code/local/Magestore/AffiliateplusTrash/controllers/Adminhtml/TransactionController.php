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
 * @package     Magestore_AffiliateplusTrash
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplus transaction trash Controller
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusTrash
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusTrash_Adminhtml_TransactionController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliateplus/transaction')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Transactions Manager'), Mage::helper('adminhtml')->__('Transaction Manager'));
		return $this;
	}
    
    public function deleteAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $transactionId = $this->getRequest()->getParam('id');
        if ($transactionId > 0) {
            $model = Mage::getModel('affiliateplus/transaction');
            try {
                $model->load($transactionId)
                    ->deleteTransaction();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Transaction was moved to trash successfully'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return $this->_redirect('affiliateplusadmin/*/view', array('id' => $transactionId));
            }
        }
        $this->_redirect('affiliateplusadmin/*/index');
    }
    
    public function massDeleteAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $ids = $this->getRequest()->getParam('transaction');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select transaction(s)'));
        } else {
            $collection = Mage::getResourceModel('affiliateplus/transaction_collection');
            $collection->addFieldToFilter('transaction_id', array('in' => $ids));
            $successed = 0;
            foreach ($collection as $model) {
                try {
                    $model->deleteTransaction();
                    $successed++;
                } catch (Exception $e) {
                    
                }
            }
            if ($successed) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total %s of %s transactions were moved to trash successfully', $successed, count($ids))
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('No transaction is moved to trash')
                );
            }
        }
        $this->_redirect('affiliateplusadmin/*/index');
    }
    
    public function deletedAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
		$this->_title($this->__('Affiliateplus'))->_title($this->__('Manage Deleted Transactions'));
		$this->_initAction()
			->renderLayout();
    }
    
    public function deletedGridAction()
    {
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function restoreAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $transactionId = $this->getRequest()->getParam('id');
        if ($transactionId > 0) {
            $model = Mage::getModel('affiliateplus/transaction');
            try {
                $model->load($transactionId)
                    ->restoreTransaction();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Transaction was restored successfully'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return $this->_redirect('affiliateplusadmin/*/view', array('id' => $transactionId));
            }
        }
        $this->_redirect('*/*/deleted');
    }
    
    public function massRestoreAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $ids = $this->getRequest()->getParam('transaction');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select transaction(s)'));
        } else {
            $collection = Mage::getResourceModel('affiliateplus/transaction_collection');
            $collection->setShowDeleted()->addFieldToFilter('transaction_is_deleted', 1)
                ->addFieldToFilter('transaction_id', array('in' => $ids));
            $successed = 0;
            foreach ($collection as $model) {
                try {
                    $model->restoreTransaction();
                    $successed++;
                } catch (Exception $e) {
                    
                }
            }
            if ($successed) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total %s of %s transactions were restored successfully', $successed, count($ids))
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('No transaction is restored')
                );
            }
        }
        $this->_redirect('*/*/deleted');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('affiliateplus');
    }
}

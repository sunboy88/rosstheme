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
 * Affiliateplus payment trash Controller
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusTrash
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusTrash_Adminhtml_PaymentController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliateplus/payment')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Payments Manager'), Mage::helper('adminhtml')->__('Payment Manager'));
		return $this;
	}
    
    public function deleteAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $paymentId = $this->getRequest()->getParam('id');
        if ($paymentId > 0) {
            $model = Mage::getModel('affiliateplus/payment');
            try {
                $model->load($paymentId)
                    ->deletePayment();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Withdrawal was moved to trash successfully'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return $this->_redirect('affiliateplusadmin/*/view', array('id' => $paymentId));
            }
        }
        $this->_redirect('affiliateplusadmin/*/index');
    }
    
    public function massDeleteAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $ids = $this->getRequest()->getParam('payment');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select withdrawal(s)'));
        } else {
            $collection = Mage::getResourceModel('affiliateplus/payment_collection');
            $collection->addFieldToFilter('payment_id', array('in' => $ids));
            $successed = 0;
            foreach ($collection as $model) {
                try {
                    $model->deletePayment();
                    $successed++;
                } catch (Exception $e) {
                    
                }
            }
            if ($successed) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total %s of %s withdrawals were moved to trash successfully', $successed, count($ids))
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('No withdrawal is moved to trash')
                );
            }
        }
        $this->_redirect('affiliateplusadmin/*/index');
    }
    
    public function deletedAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
		$this->_title($this->__('Affiliateplus'))->_title($this->__('Manage Deleted Withdrawals'));
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
        $paymentId = $this->getRequest()->getParam('id');
        if ($paymentId > 0) {
            $model = Mage::getModel('affiliateplus/payment');
            try {
                $model->load($paymentId)
                    ->restorePayment();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Withdrawal was restored successfully'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return $this->_redirect('affiliateplusadmin/*/view', array('id' => $paymentId));
            }
        }
        $this->_redirect('*/*/deleted');
    }
    
    public function massRestoreAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $ids = $this->getRequest()->getParam('payment');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select withdrawal(s)'));
        } else {
            $collection = Mage::getResourceModel('affiliateplus/payment_collection');
            $collection->setShowDeleted()->addFieldToFilter('payment_is_deleted', 1)
                ->addFieldToFilter('payment_id', array('in' => $ids));
            $successed = 0;
            foreach ($collection as $model) {
                try {
                    $model->restorePayment();
                    $successed++;
                } catch (Exception $e) {
                    
                }
            }
            if ($successed) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total %s of %s withdrawals were restored successfully', $successed, count($ids))
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('No withdrawal is restored')
                );
            }
        }
        $this->_redirect('*/*/deleted');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('affiliateplus');
    }

    public function editAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $id = $this->getRequest()->getParam('id');
        $payment = Mage::getModel('affiliateplus/payment')->load($id);

        $this->_title($this->__('Affiliateplus'))->_title($this->__('Manage Withdrawals'));

        if ($payment && $payment->getId()) {
            $this->_title($this->__($payment->getAccountName()));
        }

        if ($payment->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $payment->addData($data);
            }

            if ($payment->getId())
                $payment->addPaymentInfo();

            Mage::register('payment_data', $payment);

            $this->loadLayout();
            $this->_setActiveMenu('affiliateplus/payment');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Payment Manager'), Mage::helper('adminhtml')->__('Withdrawals Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Payment News'), Mage::helper('adminhtml')->__('Withdrawal News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('affiliateplustrash/adminhtml_payment_edit'))
                    ->_addLeft($this->getLayout()->createBlock('affiliateplustrash/adminhtml_payment_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliateplus')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
}

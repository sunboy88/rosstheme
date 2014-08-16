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


class AW_Collpur_Adminhtml_DealController extends Mage_Adminhtml_Controller_Action {

    protected function displayTitle($data = null,$root = 'Group Deals') {
        if (!Mage::helper('collpur')->magentoLess14()) {
            if ($data) {
                if(!is_array($data)) { $data = array($data); }
                foreach ($data as $title) {
                    $this->_title($this->__($title));
                }
                $this->_title($this->__($root));
            } else {
                $this->_title($this->__('Group Deals'))->_title($root);
            }
        }
        return $this;
    }

    public function indexAction() {

        $filter = $this->getRequest()->getParam('deal_filter');
        if(!$filter) $filter = 'All';
        $filter = ucwords($filter).' Deals';

        $this
                ->displayTitle($filter)
                ->loadLayout()
                ->_setActiveMenu('collpur')
                ->renderLayout();
    }

    public function newAction() {

        $this
                ->displayTitle('Select product')
                ->loadLayout()
                ->renderLayout();
    }

    public function editAction() {

        $deal = Mage::getModel('collpur/deal')->load($this->getRequest()->getParam('id'));
  
        Mage::register('collpur_deal', $deal);
        if ($deal->getId()) {
                /* Check if related product is not deleted */
                if(!Mage::getModel('catalog/product')->load($deal->getProductId())->getId()) {
                      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('collpur')->__('Error: associated product has been deleted'));
                      return $this->_redirect('*/*/');
                }
            $breadcrumbTitle = $breadcrumbLabel = Mage::helper('collpur')->__('Edit Deal');
            $this->displayTitle('Edit Deal');
        } else {
            $breadcrumbTitle = $breadcrumbLabel = Mage::helper('collpur')->__('New Deal');
            $this->displayTitle('New Deal');
        }


        $this
               // ->displayTitle()
                ->loadLayout()
                ->_setActiveMenu('collpur')
                ->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle)
                ->_addContent($this->getLayout()->createBlock('collpur/adminhtml_deal_edit'))
                ->_addLeft($this->getLayout()->createBlock('collpur/adminhtml_deal_edit_tabs'))
                ->renderLayout();
    }

    public function saveAction() {
        try {
            $request = $this->getRequest();
            if ($data = $request->getPost()) {


                $deal = Mage::getModel('collpur/deal');
                if ($request->getParam('id'))
                    $deal->load($request->getParam('id'));

                if ($deal->getId()) {
                    if (isset($data['maximum_allowed_purchases']) && $data['maximum_allowed_purchases'] && $data['maximum_allowed_purchases'] < $deal->getPurchasesCount()) {
                        throw new AW_Core_Exception($this->__('Current purchases count is more then maximum allowed purchases'));
                    }
                }

                if (!$deal->getId()) {
                    if ($request->getParam('product_id') && $request->getParam('product_visibility')) {
                        Mage::getModel('catalog/product')
                                ->load($request->getParam('product_id'))
                                ->setData('visibility', $request->getParam('product_visibility'))
                                ->save();
                    }                

                }              

                $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                if (isset($data['available_from']) && $data['available_from']) {
                    $dateFrom = Mage::app()->getLocale()->date($data['available_from'], $format);
                    $data['available_from'] = Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp());
                } else {
                    $data['available_from'] = null;
                }
                if (isset($data['available_to']) && $data['available_to']) {
                    $dateTo = Mage::app()->getLocale()->date($data['available_to'], $format);
                    $data['available_to'] = Mage::getModel('core/date')->gmtDate(null, $dateTo->getTimestamp());
                } else {
                    $data['available_to'] = null;
                }



                $imgName = '';
                if (isset($_FILES['deal_image']['name']) && $_FILES['deal_image']['name'] != '' && @$data['deal_image']['delete'] != 1) {
                    $uploader = new Varien_File_Uploader('deal_image');
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::getBaseDir('media') . DS . 'aw_collpur' . DS . 'deals' . DS;
                    $imgName = preg_replace("#[^a-zA-Z0-9_.-]#is","",$_FILES['deal_image']['name']); 
                    $uploader->save($path, $imgName);
                }

                $deal->setData('close_state', AW_Collpur_Model_Deal::STATE_OPEN)->addData($data);
 
                if ($deal->getId()) { $deal->checkPurchasesCount()->checkSuccess()->checkAutoClose(); }
                else { $deal->setPurchasesLeft($deal->getQtyToReachDeal()); }


                if ($imgName) {
                    $deal->setData('deal_image', $imgName);
                } elseif (@$data['deal_image']['delete'] == 1) {
                    $deal->setData('deal_image', '');
                } else {
                    $deal->unsDealImage();
                }

                $deal->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Successfully saved'));
                if ($this->getRequest()->getParam('back'))
                    return $this->_redirect('*/*/edit', array('id' => $deal->getId(), 'tab' => $this->getRequest()->getParam('tab')));
            }
        } catch (AW_Core_Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
            return $this->_redirect('*/*/edit', array('id' => $request->getParam('id')));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('collpur')->__('Unable to find item to save'));
            return $this->_redirect('*/*/');
        }
        return $this->_redirect('*/*/');
    }

    public function productGridAction() {
        $this->getResponse()->setBody($this->getLayout()->createBlock('collpur/adminhtml_deal_product_grid')->toHtml());
    }

    public function couponsGridAction() {
        $this->getResponse()->setBody($this->getLayout()->createBlock('collpur/adminhtml_deal_coupon_grid')->toHtml());
    }

    public function ordersGridAction() {
        $this->getResponse()->setBody($this->getLayout()->createBlock('collpur/adminhtml_deal_orders_grid')->toHtml());
    }

    public function generateCouponsAction() {
        $couponsQty = $this->getRequest()->getParam('qty');
        $dealId = $this->getRequest()->getParam('deal');
        try {
            if ($couponsQty > 0) {
                $deal = Mage::getModel('collpur/deal')->load($dealId);
                AW_Collpur_Model_Coupon::generateCoupons($deal, $couponsQty);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('collpur')->__('Coupons were successfully generated'));
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('collpur')->__('Incorrect coupons number'));
            }
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('collpur')->__('Error while coupons generating'));
        }

        return $this->_redirect('*/*/edit', array('id' => $deal->getId(), 'tab' => $this->getRequest()->getParam('tab')));
    }

    public function addsecondstepAction() {
        $this
                ->displayTitle('Select linked product visibility')
                ->loadLayout()
                ->_addContent($this->getLayout()->createBlock('collpur/adminhtml_deal_edit_addsecondstep'))
                ->renderLayout();
    }

    public function closeAsFailedAction() {
        try {
            $request = $this->getRequest();
            if (!$request->getParam('id'))
                throw new Mage_Core_Exception(Mage::helper('collpur')->__('Incorrect deal'));
            Mage::getModel('collpur/deal')
                    ->load($request->getParam('id'))
                    ->closeAsFailed();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('collpur')->__('Deal successfully closed, refunds completed'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }
        return $this->_redirect('*/*/edit', array('id' => $request->getParam('id')));
    }

    public function closeAsSuccessAction() {
        try {
            $request = $this->getRequest();
            if (!$request->getParam('id'))
                throw new Mage_Core_Exception(Mage::helper('collpur')->__('Incorrect deal'));
            Mage::getModel('collpur/deal')
                    ->load($request->getParam('id'))
                    ->closeAsSuccess();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('collpur')->__('Deal successfully closed'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }
        return $this->_redirect('*/*/edit', array('id' => $request->getParam('id')));
    }

    public function archiveAction() {
        try {
            $request = $this->getRequest();
            if (!$request->getParam('id'))
                throw new Mage_Core_Exception(Mage::helper('collpur')->__('Incorrect deal'));
            Mage::getModel('collpur/deal')
                    ->load($request->getParam('id'))
                    ->archive();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('collpur')->__('Deal successfully archived'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }
        return $this->_redirect('*/*/edit', array('id' => $request->getParam('id')));
    }

    public function deleteAction() {
        try {
            $request = $this->getRequest();
            if (!$request->getParam('id'))
                throw new Mage_Core_Exception(Mage::helper('collpur')->__('Incorrect deal'));
            Mage::getModel('collpur/deal')
                    ->load($request->getParam('id'))
                    ->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('collpur')->__('Deal successfully deleted'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }
        return $this->_redirect('*/*/index');
    }

    public function reopenAction() {
        try {
            $request = $this->getRequest();
            if (!$request->getParam('id'))
                throw new Mage_Core_Exception(Mage::helper('collpur')->__('Incorrect deal'));
            Mage::getModel('collpur/deal')
                    ->load($request->getParam('id'))
                    ->reopen();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('collpur')->__('Deal successfully reopened'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }
        return $this->_redirect('*/*/edit', array('id' => $request->getParam('id')));
    }

    public function massDeleteAction() {
        try {
            $dealIds = $this->getRequest()->getParam('deal');
            $dealModel = Mage::getModel('collpur/deal');
            if ($dealIds) {
                foreach ($dealIds as $dealId) {
                    $deal = Mage::getModel('collpur/deal')->load($dealId);
                    if($deal->isClosed() || $deal->isArchived() || !$dealModel->dealProductExists($deal)) {
                        $deal->delete();
                    }
                    else {
                        Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('collpur')->__('Only closed deals can be deleted. Deal #%d is not closed',$deal->getId()));
                        //return $this->_redirect('*/*/index');
                    }
                }
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('collpur')->__('Successfully deleted'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('collpur')->__('Unable to find items to delete'));
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed() {

        return Mage::getSingleton('admin/session')->isAllowed('collpur/deals');

    }

}
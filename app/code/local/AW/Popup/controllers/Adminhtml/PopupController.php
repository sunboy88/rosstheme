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
 * @package    AW_Popup
 * @version    1.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Popup_Adminhtml_PopupController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        $aclSectionMap = array(
            'index' => 'list_popups',
            'edit'  => 'add_new',
            'new'   => 'add_new',
            'save'  => 'add_new',
        );

        $actionName = $this->getRequest()->getActionName();
        if (array_key_exists($actionName, $aclSectionMap) && isset($aclSectionMap[$actionName])) {
            return Mage::getSingleton('admin/session')->isAllowed('cms/popup/' . $aclSectionMap[$actionName]);
        }
        return true;
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/popup')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager')
            );
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        return $this;
    }

    protected function _setTitle($title)
    {
        if (method_exists($this, '_title')) {
            $this->_title($title);
        }
        return $this;
    }

    public function indexAction()
    {
        $this->_setTitle(Mage::helper('adminhtml')->__('List Popups'));
        $this->_initAction()
            ->renderLayout();
    }

    public function editAction()
    {
        $this->_setTitle(Mage::helper('adminhtml')->__('Item Manager'));
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('popup/popup')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('popup_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('cms/popup');

            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('popup/adminhtml_popup_edit'))
                ->_addLeft($this->getLayout()->createBlock('popup/adminhtml_popup_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('popup')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            if ($data['sort_order'] == '') {
                $data['sort_order'] = Mage::helper('popup')->getPopupCount() + 1;
            }

            $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $localeCode = Mage::app()->getLocale()->getLocaleCode();
            if (empty($data['date_from'])) {
                $from = new Zend_Date();
            } else {
                $from = new Zend_Date($data['date_from'], $dateFormatIso, $localeCode);
            }
            $data['date_from'] = $from->setTimezone('utc')->toString(Varien_Date::DATE_INTERNAL_FORMAT);

            if (empty($data['date_to'])) {
                $to = new Zend_Date();
                $to->addMonth(1);
            } else {
                $to = new Zend_Date($data['date_to'], $dateFormatIso, $localeCode);
            }
            $data['date_to'] = $to->setTimezone('utc')->toString(Varien_Date::DATE_INTERNAL_FORMAT);

            if (!isset($data['store_view'])) {
                $data['store_view'][] = '0';
            }
            if ($data['store_view'][0] == '0') {
                $stores = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
                foreach ($stores as $store) {
                    if (is_array($store['value'])) {
                        foreach ($store['value'] as $value) {
                            $data['store_view'][] = $value['value'];
                        }
                    }
                }
            }

            $model = Mage::getModel('popup/popup');
            $model
                ->setData($data)
                ->setId($this->getRequest()->getParam('id'))
            ;

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('popup')->__('Item was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return $this;
                }
                $this->_redirect('*/*/');
                return $this;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return $this;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('popup')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('popup/popup');

                $model->setId($this->getRequest()->getParam('id'))->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $popupIds = $this->getRequest()->getParam('popup');
        if (!is_array($popupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($popupIds as $popupId) {
                    $popup = Mage::getModel('popup/popup')->load($popupId);
                    $popup->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($popupIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $popupIds = $this->getRequest()->getParam('popup');
        if (!is_array($popupIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($popupIds as $popupId) {
                    $popup = Mage::getSingleton('popup/popup')
                        ->load($popupId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($popupIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/**********************************************
 *        MAGENTO EDITION USAGE NOTICE        *
 **********************************************/
/* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/**********************************************
 *        DISCLAIMER                          *
 **********************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 **********************************************
 * @category   Belvg
 * @package    Belvg_Referralreward
 * @copyright  Copyright (c) 2010 - 2014 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_Adminhtml_PointsController extends Mage_Adminhtml_Controller_action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('promo/points')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Referral Bonus and Reward System - Points'), Mage::helper('adminhtml')->__('Referral Bonus and Reward System - Points'));

        return $this;
    }   
 
    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    public function editAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }
 
    public function saveAction()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        $form       = $this->getRequest()->getParam('change');
        $points     = (int) $form['points'];
        if ($customerId && $points) {
            try {
                $vector = $form['vector'];
                $params = array(
                    'points'      => $points,
                    'customer_id' => $customerId,
                );

                if ($vector == '1') {
                    Mage::getModel('referralreward/points_log_admin')->supplementPoints($customerId, $params);
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('referralreward')->__('successfully supplemented'));
                } else if ($vector == '-1') {
                    $params = new Varien_object($params);
                    Mage::getModel('referralreward/points_log_admin')->withdrawPoints($params);
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('referralreward')->__('successfully withdrawed'));
                }

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $customerId));

                    return;
                }

                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $customerId));

                return;
            }
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('referralreward')->__('Points are not changed'));
        $this->_redirect('*/*/edit', array('id' => $customerId));

        return;
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit'/*, array('id' => $this->getRequest()->getParam('id'))*/);
            }
        }

        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        /*$referralrewardIds = $this->getRequest()->getParam('referralreward');
        if (!is_array($referralrewardIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($referralrewardIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }*/

        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'referralreward.csv';
        $content  = $this->getLayout()->createBlock('referralreward/adminhtml_points_grid')->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'referralreward.xml';
        $content  = $this->getLayout()->createBlock('referralreward/adminhtml_points_grid')->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', TRUE);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', TRUE);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    public function logGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('referralreward/adminhtml_points_edit_tab_log')->toHtml()
        );
    }
}
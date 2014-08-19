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
 * @package     Magestore_AffiliateplusBanner
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusbanner Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_AffiliateplusBanner_Adminhtml_BannerController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('affiliateplus/banner')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Banners Manager'),
                Mage::helper('adminhtml')->__('Banner Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $this->_title($this->__('Affiliateplus'))->_title($this->__('Manage Banners'));
        $this->_initAction()
            ->renderLayout();
    }
    
    public function gridAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('affiliateplusbanner/adminhtml_banner_grid')->toHtml()
        );
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $bannerId   = $this->getRequest()->getParam('id');
        $storeId    = $this->getRequest()->getParam('store');
        
        $banner = Mage::getModel('affiliateplus/banner')
                ->setStoreId($storeId)
                ->load($bannerId);
        
        $this->_title($this->__('Affiliateplus'))->_title($this->__('Manage Banners'));
    
        if ($banner && $banner->getId()) {
            $this->_title($this->__($banner->getTitle()));
        } else {
            $this->_title($this->__('New Banner'));
        }
        
        if ($banner->getId() || $bannerId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $banner->setData($data);
            }
            Mage::register('banner_data', $banner);

            $this->loadLayout();
            $this->_setActiveMenu('affiliateplus/banner');
            
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Banner Manager'),
                    Mage::helper('adminhtml')->__('Banner Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Banner News'),
                    Mage::helper('adminhtml')->__('Banner News'));
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('affiliateplusbanner/adminhtml_banner_edit'))
                ->_addLeft($this->getLayout()->createBlock('affiliateplusbanner/adminhtml_banner_edit_tabs'));
            
            $this->renderLayout();
            
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('affiliateplus')->__('Banner does not exist'));
            $this->_redirect('*/*/', array('store' => $storeId));
        }
    }
    
    public function bannerAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('tab.banner')
                ->setBanners($this->getRequest()->getPost('obanner', null));
        $this->renderLayout();
    }
    
    public function bannerGridAction()
    {
        $this->bannerAction();
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Upload banner file to server
     * 
     * @param string $fieldName
     * @return string
     */
    protected function _uploadAffiliateBannerFile($fieldName)
    {
        if (isset($_FILES[$fieldName]['name']) && $_FILES[$fieldName]['name'] != '') {
            try {
                $uploader = new Varien_File_Uploader($fieldName);
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','swf'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'affiliateplus' . DS . 'banner' . DS;
                $result = $uploader->save($path, $_FILES[$fieldName]['name']);
                
                return $result['file'];
            } catch (Exception $e) {
                return $_FILES[$fieldName]['name'];
            }
        }
        return '';
    }
    
    /**
     * save item action
     */
    public function saveAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        if ($data = $this->getRequest()->getPost()) {
            if ($sourceFile = $this->_uploadAffiliateBannerFile('source_file')) {
                $data['source_file'] = $sourceFile;
            }
            // Peel large file uploading
            if ($peelImage = $this->_uploadAffiliateBannerFile('peel_image')) {
                $data['peel_image'] = $peelImage;
            }
            
            $bannerId = $this->getRequest()->getParam('id');
            $storeId  = $this->getRequest()->getParam('store');
            $banner = Mage::getModel('affiliateplus/banner');
            $banner->setStoreId($storeId)
                    ->load($bannerId)
                    ->addData($data)
                    ->setId($bannerId);
            
            // Prepare image size
            if (($sourceFile = $banner->getSourceFile())
                && $banner->getTypeId() != Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_FLASH
                && (!$banner->getWidth() || !$banner->getHeight())
            ) {
                try {
                    $image = new Varien_Image(Mage::getBaseDir('media') . DS
                            . 'affiliateplus' . DS . 'banner' . DS . $sourceFile);
                    if (!$banner->getWidth()) {
                        $banner->setWidth($image->getOriginalWidth());
                    }
                    if (!$banner->getHeight()) {
                        $banner->setHeight($image->getOriginalHeight());
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
            // Prepare image size for Peel banner
            if (($sourceFile = $banner->getPeelImage())
                && $banner->getTypeId() == Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_PEEL
                && (!$banner->getPeelWidth() || !$banner->getPeelHeight())
            ) {
                try {
                    $image = new Varien_Image(Mage::getBaseDir('media') . DS
                            . 'affiliateplus' . DS . 'banner' . DS . $sourceFile);
                    if (!$banner->getPeelWidth()) {
                        $banner->setPeelWidth($image->getOriginalWidth());
                    }
                    if (!$banner->getPeelHeight()) {
                        $banner->setPeelHeight($image->getOriginalHeight());
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
            
            // Try to save banner
            try {
                $banner->save();
                
                if ($banner->getTypeId() == Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_ROTATOR
                    && isset($data['banners'])
                ) {
                    $childBanners = array();
                    if ($banner->getData('banners')) {
                        parse_str($banner->getData('banners'), $childBanners);
                    }
                    Mage::getSingleton('affiliateplusbanner/rotator')
                            ->setData('parent_id', $banner->getId())
                            ->saveChildBanner($childBanners);
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('affiliateplusbanner')->__('Banner was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array(
                        'id'    => $banner->getId(),
                        'store' => $storeId
                    ));
                    return;
                }
                $this->_redirect('*/*/', array('store' => $storeId));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                    'id'    => $this->getRequest()->getParam('id'),
                    'store' => $storeId
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('affiliateplusbanner')->__('Unable to find banner to save')
        );
        $this->_redirect('*/*/', array('store' => $storeId));
    }
 
    /**
     * delete item action
     */
    public function deleteAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $bannerId = $this->getRequest()->getParam('id');
        $storeId = $this->getRequest()->getParam('store');
        
        if ($bannerId > 0) {
            try {
                $banner = Mage::getModel('affiliateplus/banner');
                $banner->setId($bannerId)
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Banner was successfully deleted'));
                $this->_redirect('*/*/', array('store' => $storeId));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $bannerId));
            }
        }
        $this->_redirect('*/*/', array('store' => $storeId));
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $bannerIds = $this->getRequest()->getParam('banner');
        $storeId = $this->getRequest()->getParam('store');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select banner(s)'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $banner = Mage::getModel('affiliateplus/banner')->load($bannerId);
                    $banner->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($bannerIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index', array('store' => $storeId));
    }
    
    /**
     * mass change status for item(s) action
     */
    public function massStatusAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $bannerIds = $this->getRequest()->getParam('banner');
        $storeId = $this->getRequest()->getParam('store');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select banner(s)'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    Mage::getSingleton('affiliateplus/banner')
                        ->setStoreId($storeId)
                        ->load($bannerId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($bannerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index', array('store' => $storeId));
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('affiliateplus/banner');
    }
}
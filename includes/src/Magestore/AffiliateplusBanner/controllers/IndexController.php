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
 * AffiliateplusBanner Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action/ show form for custom banner link
     */
    public function indexAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }
        $block = Mage::getBlockSingleton('affiliateplusbanner/form');
        $block->setBannerId($this->getRequest()->getParam('id'));
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function customLinkAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }
        $bannerId = $this->getRequest()->getParam('id');
        $customLink = $this->getRequest()->getParam('custom_link');
        
        $banner = Mage::getModel('affiliateplus/banner')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($bannerId);
        $banner->setLink($customLink);
        
        $renderBlock = Mage::getBlockSingleton('affiliateplusbanner/view');
        $renderBlock->setRenderType('code')
                ->setBanner($banner);
        
        $this->getResponse()->setBody(Zend_Json::encode(array(
            'bannerid'  => $bannerId,
            'code'      => trim($renderBlock->toHtml()),
            'link'      => $renderBlock->getBannerUrl()
        )));
    }
}
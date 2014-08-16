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
 * AffiliateplusBanner Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Model_Observer
{
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_AffiliateplusBanner_Model_Observer
     */
    public function affiliateplusBannerImage($observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        if ($action->getRequest()->getParam('type') == 'javascript') {
            // $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            
            $store  = Mage::app()->getStore($action->getRequest()->getParam('store_id'));
            $banner = Mage::getModel('affiliateplus/banner')
                    ->setStoreId($store->getId())
                    ->load($action->getRequest()->getParam('id'));
            if (!$banner->getId()) {
                return ;
            }
            if ($customLink = $action->getRequest()->getParam('link')) {
                $banner->setLink(Mage::helper('core')->urlDecode($customLink));
            }
            $account = Mage::getModel('affiliateplus/account')
                    ->setStoreId($store->getId())
                    ->load($action->getRequest()->getParam('account_id'));
            if (!$account->getId()) {
                return ;
            }
            
            $renderBlock = Mage::getBlockSingleton('affiliateplusbanner/view');
            $renderBlock->setRenderType('javascript')
                    ->setAffiliateAccount($account)
                    ->setBanner($banner);
            $action->getResponse()->setBody($renderBlock->toHtml());
        }
        return ;
    }
}
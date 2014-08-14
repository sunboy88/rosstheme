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
/******************************************
 *      MAGENTO EDITION USAGE NOTICE      *
 ******************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/******************************************
 *      DISCLAIMER                        *
 ******************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 ******************************************
 * @category   Belvg
 * @package    Belvg_Referralreward
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_Model_Observer_Other
{
    const BLOCK_ONEPAGE_PAYMENT_POINTS  = 'referralreward.checkout.onepage.form';

    /**
     *  Creating database entry for a current user to store points.
     *  If this entry does not exist.
     *  <events>
     *      <controller_action_layout_load_before>
     */
    public function checkCustomer(Varien_Event_Observer $observer)
    {
        $helper     = Mage::helper('referralreward');
        $customerId = (int) Mage::getSingleton('customer/session')->getId();
        if ($helper->isEnabled() && $customerId) {
            $helper->createNewPointsObject($customerId);
            Mage::getModel('referralreward/points_log')->checkPointsEndAt();
        }
    }

    public function beforeCart(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('referralreward');
        if (!$helper->isEnabled()) {
            $helper->removeCouponsForReferral();
        }
    }

    protected function _getLayout()
    {
        return Mage::app()->getLayout();
    }

    protected function _loadLayoutBlock()
    {
        return $this->_getLayout()->getBlock(self::BLOCK_ONEPAGE_PAYMENT_POINTS);
    }

    public function pointsPaymentAdd(Varien_Event_Observer $observer)
    {
        $helper   = Mage::helper('referralreward');
        $settings = $helper->getSettings();
        $block    = $observer->getEvent()->getBlock();
        if ($helper->isEnabled() && $settings['cart_or_onepage'] == Belvg_Referralreward_Model_Source_Cartonepage::POINTS_ONEPAGE && $block instanceof Mage_Checkout_Block_Onepage_Payment) {
            $transport = $observer->getEvent()->getTransport();

            $block     = $this->_loadLayoutBlock();
            $html      = $transport->getHtml();
            $blockHtml = '';
            if ($block instanceof Mage_Core_Block_Template) {
                $blockHtml = $block->toHtml();
            }

            $html = str_replace('</form>', $blockHtml . '</form>', $html);
            //$html = str_replace('</fieldset>', $blockHtml . '</fieldset>', $html);

            $transport->setHtml($html);
        }
    }
}
 
 
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
 * @copyright  Copyright (c) 2010 - 2014 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_Block_Checkout_Onepage extends Belvg_Referralreward_Block_Checkout_Cart
{
    public function isFormShow()
    {
        $settings    = Mage::helper('referralreward')->getSettings();
        $items       = $this->getQuote()->getAllVisibleItems();

        if ($settings['cart_or_onepage'] == Belvg_Referralreward_Model_Source_Cartonepage::POINTS_ONEPAGE && $items && $this->getCustomerPoints()) {
            if ($settings['use_coupon']) {
                $pointsCouponCode = $this->getItem()->getCouponCode();
                $quoteCouponCode  = $this->getQuote()->getCouponCode();
                if ($pointsCouponCode == $quoteCouponCode) {
                    return TRUE;
                }
            } else {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function isQuotePointsMessageShow()
    {
        $settings = Mage::helper('referralreward')->getSettings();

        return ($this->getQuotePoints() && !$this->isFormShow() && $settings['cart_or_onepage'] == Belvg_Referralreward_Model_Source_Cartonepage::POINTS_ONEPAGE) ? TRUE : FALSE;
    }

    public function isCustomerPointsMessageShow()
    {
        $settings = Mage::helper('referralreward')->getSettings();

        return (Mage::getSingleton('customer/session')->isLoggedIn() && $this->getCustomerPoints() && $settings['cart_or_onepage'] == Belvg_Referralreward_Model_Source_Cartonepage::POINTS_ONEPAGE) ? TRUE : FALSE;
    }

    public function isCustomerCouponCodeShow()
    {
        $settings = Mage::helper('referralreward')->getSettings();

        return ($settings['use_coupon'] && $settings['cart_or_onepage'] == Belvg_Referralreward_Model_Source_Cartonepage::POINTS_ONEPAGE) ? TRUE : FALSE;
    }

    public function getSliderPointsSettings()
    {
        $settings      = Mage::helper('referralreward')->getSettings();
        $quoteSubtotal = $this->getQuote()->getSubtotal();

        return array(
            'cost'     => $settings['pointCost'],
            'discard'  => $settings['discardPoints'],
            'subtotal' => $quoteSubtotal,
            'my'       => $this->getCustomerPoints(),
            'quote'    => $this->getQuotePoints(),
            'minValue' => 0,
        );
    }
}
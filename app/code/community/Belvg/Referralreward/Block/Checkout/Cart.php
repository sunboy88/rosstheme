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

class Belvg_Referralreward_Block_Checkout_Cart extends Belvg_Referralreward_Block_Invite
{
    public function _construct()
    {
        $helper           = Mage::helper('referralreward');
        $settings         = $helper->getSettings();
        $pointsCouponCode = $this->getItem()->getCouponCode();
        $quoteCouponCode  = $this->getQuote()->getCouponCode();

        if ($settings['use_coupon']) {
            Mage::getSingleton('core/session')->unsPointsDiscount();
            $helper->createCouponForReferral();
            if ($this->getQuoteCouponCode() == $this->getCustomerCouponCode()) {
                Mage::getSingleton('core/session')->setPoints('used');
            } else {
                Mage::getSingleton('core/session')->unsPoints();
            }
        } else {
            if ($quoteCouponCode == $pointsCouponCode) {
                $this->getQuote()->setCouponCode('')
                    ->collectTotals()
                    ->save();
            }
        }

        parent::_construct();
    }

    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function getQuotePoints()
    {
        $quotePoints = (int) Mage::helper('referralreward')->getQuotePoints();

        return $quotePoints;
    }

    public function getQuoteCredit()
    {
        $settings    = Mage::helper('referralreward')->getSettings();
        $quoteCredit = $this->getQuotePoints() * $settings['pointCost'];

        return Mage::helper('checkout')->formatPrice($quoteCredit);
    }

    public function getQuoteCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }

    public function getCustomerPoints()
    {
        return (int) $this->getItem()->getPoints();
    }

    public function getCustomerCredit()
    {
        $settings       = Mage::helper('referralreward')->getSettings();
        $customerCredit = $this->getCustomerPoints() * $settings['pointCost'];

        return Mage::helper('checkout')->formatPrice($customerCredit);
    }

    public function getCustomerCouponCode()
    {
        return $this->getItem()->getCouponCode();
    }

    public function isFormShow()
    {
        $settings    = Mage::helper('referralreward')->getSettings();
        $items       = $this->getQuote()->getAllVisibleItems();

        if ($settings['cart_or_onepage'] == Belvg_Referralreward_Model_Source_Cartonepage::POINTS_CART && $items && $this->getCustomerPoints()) {
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
        $settings    = Mage::helper('referralreward')->getSettings();

        return ($this->getQuotePoints() && !$this->isFormShow() && $settings['cart_or_onepage'] == Belvg_Referralreward_Model_Source_Cartonepage::POINTS_CART) ? TRUE : FALSE;
    }

    public function isCustomerPointsMessageShow()
    {
        return (Mage::getSingleton('customer/session')->isLoggedIn() && $this->getCustomerPoints() && $settings['cart_or_onepage'] == Belvg_Referralreward_Model_Source_Cartonepage::POINTS_CART) ? TRUE : FALSE;
    }

    public function isCustomerCouponCodeShow()
    {
        $settings = Mage::helper('referralreward')->getSettings();

        return ($settings['use_coupon'] && $settings['cart_or_onepage'] == Belvg_Referralreward_Model_Source_Cartonepage::POINTS_CART) ? TRUE : FALSE;
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
            'minValue' => 1,
        );
    }

}
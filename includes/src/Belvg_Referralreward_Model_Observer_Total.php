<?php

class Belvg_Referralreward_Model_Observer_Total
{
	public function invoiceSaveAfter(Varien_Event_Observer $observer)
	{
		$invoice = $observer->getEvent()->getInvoice();
		if ($invoice->getBaseReferralrewardAmount()) {
			$order = $invoice->getOrder();
			$order->setReferralrewardAmountInvoiced($order->getReferralrewardAmountInvoiced() + $invoice->getReferralrewardAmount());
			$order->setBaseReferralrewardAmountInvoiced($order->getBaseReferralrewardAmountInvoiced() + $invoice->getBaseReferralrewardAmount());
		}

		return $this;
	}

	public function creditmemoSaveAfter(Varien_Event_Observer $observer)
	{
		/* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
		$creditmemo = $observer->getEvent()->getCreditmemo();
		if ($creditmemo->getReferralrewardAmount()) {
			$order = $creditmemo->getOrder();
			$order->setReferralrewardAmountRefunded($order->getReferralrewardAmountRefunded() + $creditmemo->getReferralrewardAmount());
			$order->setBaseReferralrewardAmountRefunded($order->getBaseReferralrewardAmountRefunded() + $creditmemo->getBaseReferralrewardAmount());
		}

		return $this;
	}

	public function updatePaypalTotal($evt)
    {
		$cart = $evt->getPaypalCart();
		$cart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL, $cart->getSalesEntity()->getReferralrewardAmount());
	}

    public function savePayment(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('referralreward');
        if ($helper->isEnabled()) {
            $payment = Mage::app()->getRequest()->getParam('payment');
            //print_r($payment); die;
            if ($payment && isset($payment['use_points'])) {
                $discount = (int) $payment['use_points'];
                if ($discount) {
                    $settings   = $helper->getSettings();
                    $customerId = (int) Mage::getSingleton('customer/session')->getId();
                    $pointsItem = Mage::getModel('referralreward/points')->getItem($customerId);
                    if ($discount > $pointsItem->getPoints()) {
                        $discount = $pointsItem->getPoints();
                    }

                    if ($settings['use_coupon']) {
                        Mage::getSingleton('core/session')->setPoints('used');
                        Mage::getSingleton('core/session')->unsPointsDiscount();
                        Mage::helper('referralreward')->createCouponForReferral($discount);
                    } else {
                        Mage::getSingleton('core/session')->setPoints('used');
                        Mage::getSingleton('core/session')->setPointsDiscount($discount);
                    }

                    return $this;
                }
            }
        }

        Mage::getSingleton('core/session')->unsPoints();
        Mage::getSingleton('core/session')->unsPointsDiscount();
    }
}
<?php

class Belvg_Referralreward_Model_Total_Invoice extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		$order = $invoice->getOrder();
		$referralrewardAmountLeft     = $order->getReferralrewardAmount() - $order->getReferralrewardAmountInvoiced();
		$baseReferralrewardAmountLeft = $order->getBaseReferralrewardAmount() - $order->getBaseReferralrewardAmountInvoiced();
		if (abs($baseReferralrewardAmountLeft) < $invoice->getBaseGrandTotal()) {
			$invoice->setGrandTotal($invoice->getGrandTotal() + $referralrewardAmountLeft);
			$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseReferralrewardAmountLeft);
		} else {
			$referralrewardAmountLeft     = $invoice->getGrandTotal() * -1;
			$baseReferralrewardAmountLeft = $invoice->getBaseGrandTotal() * -1;

			$invoice->setGrandTotal(0);
			$invoice->setBaseGrandTotal(0);
		}
			
		$invoice->setReferralrewardAmount($referralrewardAmountLeft);
		$invoice->setBaseReferralrewardAmount($baseReferralrewardAmountLeft);

		return $this;
	}
}
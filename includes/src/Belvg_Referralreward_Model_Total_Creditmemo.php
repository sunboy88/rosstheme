<?php

class Belvg_Referralreward_Model_Total_Creditmemo extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
		$order = $creditmemo->getOrder();
		$referralrewardAmountLeft     = $order->getReferralrewardAmountInvoiced() - $order->getReferralrewardAmountRefunded();
		$baseReferralrewardAmountLeft = $order->getBaseReferralrewardAmountInvoiced() - $order->getBaseReferralrewardAmountRefunded();
		if ($baseReferralrewardAmountLeft > 0) {
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $referralrewardAmountLeft);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseReferralrewardAmountLeft);
			$creditmemo->setReferralrewardAmount($referralrewardAmountLeft);
			$creditmemo->setBaseReferralrewardAmount($baseReferralrewardAmountLeft);
		}

		return $this;
    }
}
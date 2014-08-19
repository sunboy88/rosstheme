<?php
class Magestore_Onestepcheckout_Model_Sales_Order_Invoice_Total_Discount extends Mage_Sales_Model_Order_Invoice_Total_Abstract {
	
	public function collect(Mage_Sales_Model_Order_Invoice $invoice) {
		$invoice->setOnestepcheckoutDiscountAmount(0);        
        $orderOnestepcheckoutDiscount = $invoice->getOrder()->getOnestepcheckoutDiscountAmount();		
        if ($orderOnestepcheckoutDiscount) {
            $invoice->setOnestepcheckoutDiscountAmount($orderOnestepcheckoutDiscount);           
            $invoice->setGrandTotal($invoice->getGrandTotal()-$orderOnestepcheckoutDiscount);
			$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()-$orderOnestepcheckoutDiscount);			
        }
        return $this;
	}
}
<?php
class Magestore_Onestepcheckout_Model_Sales_Quote_Address_Total_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract {
	public function collect(Mage_Sales_Model_Quote_Address $address) {
        $session = Mage::getSingleton('checkout/session');
		$discount = $session->getData('onestepcheckout_admin_discount');
        if(!$discount){
            return $this;
        }
		
		$items = $address->getAllItems();
		if (!count($items)) {
			return $this;
		}
		$session->setData('onestepcheckout_admin_discount',$discount);
		$address->setOnestepcheckoutDiscountAmount($discount);		
		$address->setData('onestepcheckout_discount_amount',$discount);
		$address->setGrandTotal($address->getGrandTotal() - $address->getOnestepcheckoutDiscountAmount());
		$address->setBaseGrandTotal($address->getBaseGrandTotal() - $address->getOnestepcheckoutDiscountAmount());	
		return $this;
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address) 
	{
		$amount = $address->getOnestepcheckoutDiscountAmount();		
		$title = Mage::helper('sales')->__('Discount(Admin)');
		if ($amount!=0) {
			$address->addTotal(array(
					'code'=>$this->getCode(),
					'title'=>$title,
					'value'=>'-'.$amount
			));
		}
		return $this;
	}
}

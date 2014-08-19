<?php
class Magestore_OneStepCheckout_Model_Source_Reloadpayment {

	public function toOptionArray() 
	{
		$options = array();		
		$options[] = array('label' => 'When all required fields are filled', 'value' => '1');
		$options[] = array('label' => 'When any triggering fields change', 'value' => '2');
		return $options;
	}
}
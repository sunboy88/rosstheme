<?php

class Ebluestore_Bulkprice_Block_Bulkprice extends Mage_Core_Block_Template{
	
	function getCurrentProduct(){
		$_product = Mage::registry('current_product');
		return $_product;
	}
}
?>
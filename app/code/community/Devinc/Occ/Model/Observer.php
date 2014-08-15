<?php
class Devinc_Occ_Model_Observer
{
    public function updateBlocksBefore($observer)
    {       
        $block = $observer->getEvent()->getBlock();
        if ($block->getNameInLayout() == 'head' && Mage::getStoreConfig('occ/configuration/enabled') && Mage::helper('occ')->isMagentoEnterprise()) { 
            $block->setIsEnterprise(true);      
        }       
    }   

    public function updateBlocksAfter($observer)
	{		
        $block = $observer->getEvent()->getBlock();
        $_transportObject = $observer->getEvent()->getTransport();
        $html = $_transportObject->getHtml();
        
        if (Mage::getSingleton('customer/session')->getOccBlocks()) {
			$occBlocks = unserialize(Mage::getSingleton('customer/session')->getOccBlocks());
    	} else {
			$occBlocks = array();
		}
		
        if ($block->getType() == 'checkout/cart_sidebar') {
        	if (!isset($occBlocks['checkout/cart_sidebar'])) {
	        	$occBlocks['checkout/cart_sidebar'] = array();
        	}
        	if (!in_array($block->getNameInLayout(), $occBlocks['checkout/cart_sidebar'], true)) {
	        	$i = count($occBlocks['checkout/cart_sidebar']);
	        	$occBlocks['checkout/cart_sidebar'][$i] = $block->getNameInLayout();      
	        } else {
		        $i = array_search($block->getNameInLayout(), $occBlocks['checkout/cart_sidebar']);
	        }
	        
	        if (!Mage::getSingleton('customer/session')->getOccRequest()) {
	        	$html = '<div class="occ-cart-sidebar" id="occ-cart-sidebar'.$i.'">'.$html.'</div>';
				$_transportObject->setHtml($html);
	        }
        }
		
		Mage::getSingleton('customer/session')->setOccBlocks(serialize($occBlocks));
	}
}

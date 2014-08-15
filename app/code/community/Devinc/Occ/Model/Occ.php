<?php
class Devinc_Occ_Model_Occ extends Mage_Core_Model_Abstract
{    
	//returns the billing or shipping dropdowns
    public function getAddressesHtmlSelect($type, $addressId = null)
    {
    	$customerSession = Mage::getSingleton('customer/session');
    	$customer = $customerSession->getCustomer();
        if ($customerSession->isLoggedIn()) {
            $options = array();
            foreach ($customer->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }

            //$addressId = $this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $customer->getPrimaryBillingAddress();
                } else {
                    $address = $customer->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = Mage::getModel('core/layout')->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select required-entry')
                ->setExtraParams('onchange="occAddress.newAddress(this.value, \''.$type.'\')"')
                ->setValue($addressId)
                ->setOptions($options);

            
            if (count($options)==0) {
            	$select->addOption('', '');
            }
            $select->addOption('addAddress', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }
    
	//returns the billing or shipping dropdowns
    public function getDefaultAddressId($type)
    {
    	$customerSession = Mage::getSingleton('customer/session');
    	$customer = $customerSession->getCustomer();
    	
    	if ($type=='billing' && $customer->getPrimaryBillingAddress()) {
	    	return $customer->getPrimaryBillingAddress()->getId();
	    } else if ($type=='shipping' && $customer->getPrimaryShippingAddress()) {
	    	return $customer->getPrimaryShippingAddress()->getId();	    	
	    } else {
	    	return null;
	    }
    }
    
    //check to see if quote has only virtual items
    public function hasNonVirtualItems()
    {
    	$quote = Mage::getSingleton('checkout/type_onepage')->getQuote();
        foreach ($quote->getItemsCollection() as $_item) {
            //if ($_item->getParentItemId()) {
                //continue;
            //}
            if (!$_item->getProduct()->isVirtual()) {
                return true;
            }
        }
        return false;
    }
    
	//add cart layout messages to occ
    public function loadLayoutMessages()
    {
        $messages = array();
		$cart = Mage::getSingleton('checkout/cart');
        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                $messages[] = $message;
            }
        }
        $cart->getCheckoutSession()->addUniqueMessages($messages);	
    }
}
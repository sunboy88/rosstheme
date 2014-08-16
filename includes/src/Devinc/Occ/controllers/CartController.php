<?php
require_once Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'CartController.php';
class Devinc_Occ_CartController extends Mage_Checkout_CartController
{
	protected function _goBack()
    {
        $this->_getSession()->setCartWasUpdated(false);        
        $result = array();

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {	
            Mage::getSingleton('catalog/session')->addError($this->__('Your session has expired. Please log back in.'));
			$result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');
        	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;   
        }
        
        $messages = $this->_getErrorMessages();
        foreach ($messages as $message) {
            $result['error'] = -1;
            $result['message'][] = $message->getText();
        }
        
        if (!Mage::getSingleton('checkout/type_onepage')->getQuote()->hasItems() && !isset($result['error'])) {
        	$result['close_popup'] = true;
        }     
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    private function _getErrorMessages()
    {
        $allMessages = array_merge(
            $this->_getErrorMessagesFromSession(Mage::getSingleton('checkout/session')),
            $this->_getErrorMessagesFromSession(Mage::getSingleton('wishlist/session')),
            $this->_getErrorMessagesFromSession(Mage::getSingleton('catalog/session'))
        );
        return $allMessages;
    }

    private function _getErrorMessagesFromSession($session)
    {
        $messages = $session->getMessages(true);
        $sessionMessages = array_merge(
            $messages->getItems(Mage_Core_Model_Message::ERROR),
            $messages->getItems(Mage_Core_Model_Message::WARNING),
            $messages->getItems(Mage_Core_Model_Message::NOTICE)
        );
        return $sessionMessages;
    }
	
    protected function _getCartPageHtml()
    {
		Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('occ_index_cart_page');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        
		return $output;
    }	
    
}
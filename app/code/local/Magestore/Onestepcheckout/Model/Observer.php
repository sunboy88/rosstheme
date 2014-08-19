<?php

class Magestore_Onestepcheckout_Model_Observer extends Mage_Core_Controller_Varien_Action {

    public function __construct() {
        
    }

    /*
     * init checkout 
     * if one step checkout is enabled, redirect checkout page to onestepcheckout
     * otherwise, redirect to checkout/onepage
     */

    public function initController($observer) {
        if (Mage::helper('onestepcheckout')->enabledOnestepcheckout()) {
            $observer->getControllerAction()->_redirect('onestepcheckout/index', array('_secure' => true));
        }
    }

    public function initCartController($observer) {
        if (Mage::helper('onestepcheckout')->enabledOnestepcheckout()) {
            if (Mage::getStoreConfig('onestepcheckout/general/redirect_to_checkout', Mage::app()->getStore()->getStoreId()) && Mage::getStoreConfig('onestepcheckout/general/active', Mage::app()->getStore()->getStoreId())) {
                $message = Mage::helper('onestepcheckout')->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($observer->getProduct()->getName()));
                Mage::getSingleton('checkout/session')->addSuccess($message);
                $redirect = Mage::getUrl('onestepcheckout/index', array('_secure' => true));
                Header('Location: ' . $redirect);
                exit();
            }
        }
    }

    public function controllerActionPredispatchCheckoutCartIndex($observer) {
        if (Mage::helper('onestepcheckout')->enabledOnestepcheckout()) {
            if (Mage::getModel('checkout/session')->getData('redirectOnestepcheckout')) {
                $observer->getControllerAction()->_redirect('onestepcheckout/index', array('_secure' => true));
                Mage::getModel('checkout/session')->setData('redirectOnestepcheckout', false);
                $smessages = Mage::getSingleton('checkout/session')->getMessages()->getItems();
                if (count($smessages)) {
                    $output = array();
                    foreach ($smessages as $smessage) {
                        $output[] = $smessage->getText();
                    }
                    Mage::getModel('checkout/session')->setData('paymentMessages', serialize($output));
                }
            }
        }
    }

    public function controllerActionPredispatchIcepayProcessingResult($observer) {
        if (Mage::helper('onestepcheckout')->enabledOnestepcheckout()) {
            $status = $observer->getControllerAction()->getRequest()->getParam('Status');
            if ($status == 'ERR') {
                Mage::getModel('checkout/session')->setData('redirectOnestepcheckout', true);
            } else {
                Mage::getSingleton('core/session')->setData('ic_quoteid', null);
            }
        }
    }

    public function controllerActionPredispatchWirecardCheckoutPageProcessingCheckresponse($observer) {
        if (Mage::helper('onestepcheckout')->enabledOnestepcheckout()) {
            $session = Mage::getSingleton('checkout/session');
            $order = Mage::getModel('sales/order');
            $order->load($session->getLastOrderId());
            if ($order->isCanceled()) {
                Mage::getModel('checkout/session')->setData('redirectOnestepcheckout', true);
            }
        }
    }

    public function controllerActionPredispatchOnestepcheckoutIndex($observer) {
        if (Mage::helper('onestepcheckout')->enabledOnestepcheckout()) {
            $smessages = unserialize(Mage::getModel('checkout/session')->getData('paymentMessages'));
            if (is_array($smessages)) {
                foreach ($smessages as $smessage) {
                    Mage::getSingleton('checkout/session')->addError($smessage);
                }
            }
            Mage::getModel('checkout/session')->setData('paymentMessages', null);
        }
    }

    /**
     * 
     * Field Position change event
     */
    public function changeFieldPosition($observer) {
        if ($observer->getEvent()->getStore()) {
            $scope = 'stores';
            $scopeId = (int) Mage::getConfig()->getNode('stores/' . $observer->getEvent()->getStore() . '/system/store/id');
        } elseif ($observer->getEvent()->getWebsite()) {
            $scope = 'websites';
            $scopeId = (int) Mage::getConfig()->getNode('websites/' . $observer->getEvent()->getWebsite() . '/system/website/id');
        } else {
            $scope = 'default';
            $scopeId = 0;
        }

        $groups = Mage::app()->getRequest()->getPost('groups');
        $fieldPositions = $groups['field_position_management']['fields'];

        $deleteTransaction = Mage::getModel('core/resource_transaction');
        /* @var $deleteTransaction Mage_Core_Model_Resource_Transaction */
        $saveTransaction = Mage::getModel('core/resource_transaction');
        /* @var $saveTransaction Mage_Core_Model_Resource_Transaction */
        $deleteCustomTransaction = Mage::getModel('core/resource_transaction');
        /* @var $deleteTransaction Mage_Core_Model_Resource_Transaction */
        $saveCustomTransaction = Mage::getModel('core/resource_transaction');
        /* @var $saveTransaction Mage_Core_Model_Resource_Transaction */
        foreach ($fieldPositions as $row => $data) {
            if ($data['value'] != null) {
                $value = $data['value'];
            } else {
                $value = null;
            }
            $dataObject = Mage::getModel('onestepcheckout/config');
            $positionPath = 'onestepcheckout/field_position_management/' . $row;
            $dataObject
                    ->setScope($scope)
                    ->setScopeId($scopeId)
                    ->setPath($positionPath)
                    ->setValue($value);
            $oldPath = Mage::getModel('onestepcheckout/config')->getCollection()
                    ->addFieldToFilter('scope', $scope)
                    ->addFieldToFilter('scope_id', $scopeId)
                    ->addFieldToFilter('path', $positionPath)
                    ->getFirstItem();
            if ($oldPath) {
                $dataObject->setConfigId($oldPath->getConfigId());
            }
            $inherit = !empty($data['inherit']);
            if (!$inherit) {
                $saveTransaction->addObject($dataObject);
            } else {
                $deleteTransaction->addObject($dataObject);
            }
        }

        //save style 
        $fieldStyles = $groups['style_management']['fields'];
//        var_dump($fieldStyles);die();
//        foreach($fieldStyles as $style){
//            zend_debug::dump($style);
//        }
//        foreach ($fieldStyles as $style){
//            zend_debug::dump($style);
//        }
//        zend_debug::dump($fieldStyles);die();
        $style = $fieldStyles['style'];        
//        foreach ($fieldStyles as $style) {
            $styleValue = $style['value'];
            $styleDataObject = Mage::getModel('onestepcheckout/config');
            $stylePath = 'onestepcheckout/style_management/style';
            $oldStylePath = Mage::getModel('onestepcheckout/config')->getCollection()
                    ->addFieldToFilter('scope', $scope)
                    ->addFieldToFilter('scope_id', $scopeId)
                    ->addFieldToFilter('path', $stylePath)
                    ->getFirstItem();
            $styleDataObject
                    ->setScope($scope)
                    ->setScopeId($scopeId)
                    ->setPath($stylePath)
                    ->setValue($styleValue);

            if ($oldStylePath) {
                $styleDataObject->setConfigId($oldStylePath->getConfigId());
            }
            $styleInherit = !empty($style['inherit']);            
            if($style['value']=='custom' || $style['custom'] || $style['inherit']){
                $customValue = $style['custom'];
                $customDataObject = Mage::getModel('onestepcheckout/config');
                $customPath = 'onestepcheckout/style_management/custom';
                $oldCustomPath = Mage::getModel('onestepcheckout/config')->getCollection()
                    ->addFieldToFilter('scope', $scope)
                    ->addFieldToFilter('scope_id', $scopeId)
                    ->addFieldToFilter('path', $customPath)
                    ->getFirstItem();
                $customDataObject
                        ->setScope($scope)
                        ->setScopeId($scopeId)
                        ->setPath($customPath)
                        ->setValue($customValue);
                if($oldCustomPath){
                    $customDataObject->setConfigId($oldCustomPath->getConfigId());
                }
                if (!$styleInherit) {                    
                    $saveCustomTransaction->addObject($customDataObject);
                } else {                    
                    $deleteCustomTransaction->addObject($customDataObject);
                }
            }
            if (!$styleInherit) {
                $saveTransaction->addObject($styleDataObject);
            } else {
                $deleteTransaction->addObject($styleDataObject);
            }
//        }
        $deleteTransaction->delete();
        $saveTransaction->save();
        $deleteCustomTransaction->delete();
        $saveCustomTransaction->save();
        
        /*button*/
        $deleteButtonTransaction = Mage::getModel('core/resource_transaction');        
        $saveButtonTransaction = Mage::getModel('core/resource_transaction');        
        $deleteCustomButtonTransaction = Mage::getModel('core/resource_transaction');        
        $saveCustomButtonTransaction = Mage::getModel('core/resource_transaction');        
        
        $button = $fieldStyles['button'];          
//        foreach ($fieldStyles as $style) {
            $buttonValue = $button['value'];
            $buttonDataObject = Mage::getModel('onestepcheckout/config');
            $buttonPath = 'onestepcheckout/style_management/button';
            $oldButtonPath = Mage::getModel('onestepcheckout/config')->getCollection()
                    ->addFieldToFilter('scope', $scope)
                    ->addFieldToFilter('scope_id', $scopeId)
                    ->addFieldToFilter('path', $buttonPath)
                    ->getFirstItem();
            $buttonDataObject
                    ->setScope($scope)
                    ->setScopeId($scopeId)
                    ->setPath($buttonPath)
                    ->setValue($buttonValue);

            if ($oldButtonPath) {
                $buttonDataObject->setConfigId($oldButtonPath->getConfigId());
            }
            $buttonInherit = !empty($button['inherit']);            
            if($button['value']=='custombutton' || $button['custombutton'] || $button['inherit']){
                $customButtonValue = $button['custombutton'];
                $customButtonDataObject = Mage::getModel('onestepcheckout/config');
                $customButtonPath = 'onestepcheckout/style_management/custombutton';
                $oldCustomButtonPath = Mage::getModel('onestepcheckout/config')->getCollection()
                    ->addFieldToFilter('scope', $scope)
                    ->addFieldToFilter('scope_id', $scopeId)
                    ->addFieldToFilter('path', $customButtonPath)
                    ->getFirstItem();
                $customButtonDataObject
                        ->setScope($scope)
                        ->setScopeId($scopeId)
                        ->setPath($customButtonPath)
                        ->setValue($customButtonValue);
                if($oldCustomButtonPath){
                    $customButtonDataObject->setConfigId($oldCustomButtonPath->getConfigId());
                }
                if (!$buttonInherit) {                    
                    $saveCustomButtonTransaction->addObject($customButtonDataObject);
                } else {                    
                    $deleteCustomButtonTransaction->addObject($customButtonDataObject);
                }
            }
            if (!$buttonInherit) {
                $saveButtonTransaction->addObject($buttonDataObject);
            } else {
                $deleteButtonTransaction->addObject($buttonDataObject);
            }
//        }
        $deleteButtonTransaction->delete();
        $saveButtonTransaction->save();
        $deleteCustomButtonTransaction->delete();
        $saveCustomButtonTransaction->save();
    }

    /*
     * 	the function is to save customer comment
     */

    public function saveOrderComment($observer) {
        $_order = $observer->getEvent()->getOrder();
        $customerComment = Mage::getSingleton('checkout/session')->getCustomerComment();
        if ($customerComment != "") {
            try {
                $_order->setOnestepcheckoutOrderComment($customerComment)
                        ->addStatusHistoryComment($customerComment, false)
                        ->save();
            } catch (Exception $e) {
                
            }
        }
    }

    /*
     * notify admin after order is placed
     */

    public function notifyAdmin($observer) {
        $helper = Mage::helper('onestepcheckout');
        if ($helper->enableNotifyAdmin()) {
            $_order = $observer->getEvent()->getOrder();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            $paymentBlock = Mage::helper('payment')->getInfoBlock($_order->getPayment())
                    ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($_order->getStore()->getId());
            $mailTemplate = Mage::getModel('core/email_template');
            $template = Mage::getStoreConfig('onestepcheckout/order_notification/template', $_order->getStoreId());
            $sendTo = array();
            $email_array = $helper->getEmailArray();
            if (!empty($email_array)) {
                foreach ($email_array as $email) {
                    $sendTo[] = array('email' => trim($email),
                        'name' => '');
                }
            }
            foreach ($sendTo as $recipient) {
                $result = $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $_order->getStoreId()))
                        ->sendTransactional(
                        $template, Mage::getStoreConfig('sales_email/order/identity', $_order->getStoreId()), $recipient['email'], $recipient['name'], array(
                    'order' => $_order,
                    'billing' => $_order->getBillingAddress(),
                    'payment_html' => $paymentBlock->toHtml(),
                        )
                );
            }
            $translate->setTranslateInline(true);
        }
    }

    public function controller_action_predispatch_adminhtml($observer) {
        $controller = $observer->getControllerAction();
        if ($controller->getRequest()->getControllerName() != 'system_config' || $controller->getRequest()->getActionName() != 'edit')
            return;
        $section = $controller->getRequest()->getParam('section');
        if ($section != 'onestepcheckout')
            return;
        $magenotificationHelper = Mage::helper('magenotification');
        if (!$magenotificationHelper->checkLicenseKey('Onestepcheckout')) {
            $message = $magenotificationHelper->getInvalidKeyNotice();
            echo $message;
            die();
        } elseif ((int) $magenotificationHelper->getCookieLicenseType() == Magestore_Magenotification_Model_Keygen::TRIAL_VERSION) {
            Mage::getSingleton('adminhtml/session')->addNotice($magenotificationHelper->__('You are using a trial version of One Step Checkout extension. It will be expired on %s.', $magenotificationHelper->getCookieData('expired_time')
            ));
        }
    }

    public function orderPlaceAfter($observers) {
        $session = Mage::getSingleton('checkout/session');
        $giftwrap = $session->getData('onestepcheckout_giftwrap');
        $giftwrapAmount = $session->getData('onestepcheckout_giftwrap_amount');
        if ($giftwrap || $giftwrapAmount) {
            $session->unsetData('onestepcheckout_giftwrap');
            $session->unsetData('onestepcheckout_giftwrap_amount');
        }
        //Save Comment                
        $order = $observers->getEvent()->getOrder();
        $customerComment = $session->getData('customer_comment');
        if ($customerComment != "") {
            try {
                $order->setOnestepcheckoutOrderComment($customerComment)
                        ->addStatusHistoryComment($customerComment, false)
                        ->save();
            } catch (Exception $e) {
                
            }
        }
        //Save survey				
        $orderId = $order->getId();
        $surveyQuestion = $session->getData('survey_question');
        $surveyAnswer = $session->getData('survey_answer');
        $survey = Mage::getModel('onestepcheckout/survey');
        if ($surveyAnswer) {
            try {
                $survey->setData('question', $surveyQuestion)
                        ->setData('answer', $surveyAnswer)
                        ->setData('order_id', $orderId)
                        ->save();
            } catch (Exception $e) {
                
            }
            $session->unsetData('survey_question');
            $session->unsetData('survey_answer');
        }

        $delivery_date_time = $session->getData('delivery_date_time');
        $delivery = Mage::getModel('onestepcheckout/delivery');
        if ($delivery_date_time) {
            try {
                $delivery->setData('delivery_time_date', $delivery_date_time)
                        ->setData('order_id', $orderId)
                        ->save();
            } catch (Exception $e) {
                
            }
            $session->unsetData('delivery_date_time');
        }
    }

}

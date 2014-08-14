<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simisalestrackingapi Adminhtml Action
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Controllers_Adminhtml_Action extends Mage_Adminhtml_Controller_Action
{
    /**
     * Controller predispatch method for check login all action
     * @return Magestore_Simisalestrackingapi_Controller_Adminhtml_Action
     */
    public function preDispatch()
    {
        // override admin store design settings via stores section
        Mage::getDesign()
            ->setArea($this->_currentArea)
            ->setPackageName((string)Mage::getConfig()->getNode('stores/admin/design/package/name'))
            ->setTheme((string)Mage::getConfig()->getNode('stores/admin/design/theme/default'))
        ;
        foreach (array('layout', 'template', 'skin', 'locale') as $type) {
            if ($value = (string)Mage::getConfig()->getNode("stores/admin/design/theme/{$type}")) {
                Mage::getDesign()->setTheme($type, $value);
            }
        }
        $this->getLayout()->setArea('adminhtml');
        //check login
        $session = Mage::getSingleton('admin/session');
        if (!$session->isLoggedIn()) {
            $request = Mage::app()->getRequest();
            $request->initForward()
                ->setModuleName('simisalestrackingapi')
                ->setControllerName('login')
                ->setActionName('index')
                ->setDispatched(false);
        }else if(!$session->isAllowed('simisalestrackingapi/'.$this->getRequest()->getControllerName())
            && $this->getRequest()->getControllerName() !== 'changestore'){
			Mage::getSingleton('admin/session', array('name' => 'adminhtml'))
                ->getCookie()->delete(
                    Mage::getSingleton('admin/session', array('name' => 'adminhtml'))
                        ->getSessionName());
			Mage::getSingleton('admin/session', array('name' => 'adminhtml'))->unsetAll();
            Mage::getSingleton('adminhtml/session')->unsetAll();
            Mage::getSingleton('core/session')->setLoginMessage(Mage::helper('simisalestrackingapi')->__('Access denied.'));
            $request = Mage::app()->getRequest();
            $request->initForward()
                ->setModuleName('simisalestrackingapi')
                ->setControllerName('login')
                ->setActionName('index')
                ->setDispatched(false);
        }
        
        return $this;
    }
}
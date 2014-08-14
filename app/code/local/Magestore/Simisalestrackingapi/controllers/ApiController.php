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
 * Simisalestrackingapi Admin Controller
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_ApiController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Api
     */
    public function indexAction()
    {
        error_reporting(0);
        @ini_set('display_errors', 0);
        
        $result = array('success' => 1);
        $data = array();
        $data['call'] = $this->getRequest()->getParam('call');
        $data['params'] = $this->getRequest()->getPost('params');
        
        //Mage::setIsDeveloperMode(true);
        //Mage::log($data,1,'simisalestrackingapi.log');
        
        if (isset($data['call'])) {
            try {
                $result['data'] = Mage::getModel('simisalestrackingapi/api')->run($data);
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error'] = $e->getCode();
                $result['data'] = $e->getMessage();
            }
        }
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        
        return;
    }
    
    
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
        if (($value = (string)Mage::getConfig()->getNode("stores/admin/design/theme/layout"))) {
            Mage::getDesign()->setTheme('layout', $value);
        }
        $this->getLayout()->setArea('adminhtml');
        return $this;
    }
    
//    
//    public function logAction(){
//        $is_clear = $this->getRequest()->getParam('clear');
//        if(isset($is_clear)){
//                file_put_contents(Mage::getBaseDir('var').DS.'log'.DS.'simisalestrackingapi.log', 'clear ___________');
//        }
//        zend_debug::dump(file_get_contents(Mage::getBaseDir('var').DS.'log'.DS.'simisalestrackingapi.log'));die;
//    }
//    
    
}

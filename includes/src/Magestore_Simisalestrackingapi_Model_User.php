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
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiSalestracking User Model
 * 
 * @category    Magestore
 * @package     Magestore_Simialestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_User extends Mage_Core_Model_Abstract
{
    /**
     * @var Magestore_Simisalestrackingapi_Helper_Data
     */
    protected $_helper;
    
    public function __construct() {
        $this->_helper = Mage::helper('simisalestrackingapi');
    }
    
    
    /**
     * login API
     */
    public function login($username, $password)
    {
        $session = Mage::getSingleton('admin/session');
        if( $this->authenticate($username, $password)){
            if (empty($username) || empty($password)) {
                return;
            }
            /** @var $user Mage_Admin_Model_User */
            $user = Mage::getModel('admin/user');
            $user->login($username, $password);
            if ($user->getId()) {
                if (method_exists($session, 'renewSession')) {
                    $session->renewSession();
                }
                $session->setIsFirstPageAfterLogin(true);
                $session->setUser($user);
                $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
                
                return true;
            }
        }
        return false;
    }
    
    
    /**
     * logout API
     */
    public function logout(){
        //set time before logout
        if(Mage::getSingleton('admin/session', array('name' => 'adminhtml'))->isLoggedIn()){
            Mage::getModel('simisalestrackingapi/settings')->saveLogout();
        }
        //clear all datas
        Mage::getSingleton('admin/session', array('name' => 'adminhtml'))
            ->getCookie()->delete(
                Mage::getSingleton('admin/session', array('name' => 'adminhtml'))
                    ->getSessionName());
        Mage::getSingleton('admin/session', array('name' => 'adminhtml'))->unsetAll();
        Mage::getSingleton('adminhtml/session')->unsetAll();
    }
    
    /**
     * API authenticate
     * @param type $username
     * @param type $password
     * @return boolean
     * @throws Mage_Core_Exception
     */
    protected function authenticate($username,$password){
        $config = Mage::getStoreConfigFlag('admin/security/use_case_sensitive_login');
        $result = false;
        $user = Mage::getModel('admin/user')->loadByUsername($username);
        try {
            $sensitive = ($config) ? $username==$user->getUsername() : true;
            if ($sensitive && $user->getId() && Mage::helper('core')->validateHash($password, $user->getPassword())) {

                if ($user->getIsActive() != '1') {
                    Mage::throwException(Mage::helper('simisalestrackingapi')->__('This account is inactive.'));
                }
                if (!$user->hasAssigned2Role($user->getId())) {
                    $result = false;
                }else{
                    $result = true;
                }
            }
        }catch (Mage_Core_Exception $e) {
            $user->unsetData();
            throw $e;
        }
        return $result;
    }
    
    
}

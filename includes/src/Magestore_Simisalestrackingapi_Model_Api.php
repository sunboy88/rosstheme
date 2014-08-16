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
 * SimiSalestracking Api Server Model
 * 
 * @category    Magestore
 * @package     Magestore_Simialestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Api
{
    /**
     * @var Magestore_Simisalestrackingapi_Helper_Data
     */
    protected $_helper;
    
    public function __construct() {
        $this->_helper = Mage::helper('simisalestrackingapi');
    }
    
    /**
     * Run API Call
     * 
     * @param array $data
     * @return mixed
     */
    public function run($data)
    {
        if (empty($data['call'])) {
            throw new Exception($this->_helper->__('No method is specified'), 0);
        }
        // Check param input
        $params = isset($data['params']) ? Mage::helper('core')->jsonDecode($data['params']) : array();
        if (!is_array($params)) {
            $params = array($params);
        }
        $method = $data['call'];
        if ($method == 'login') {
            // Login is method that do not need session id
            if (empty($params['username']) || empty($params['password'])) {
                throw new Exception($this->_helper->__('Miss username or password to login'), 2);
            }
            return $this->login($params['username'], $params['password']);
        }
        
        if($this->checkLogin())
        {
            if(!$this->checkRole()){// if not role
                throw new Exception($this->_helper->__('Access Denied.'), 3);
            }
            
            // change store id - fill by store id
            if(isset($params['store'])){
                Mage::getSingleton('adminhtml/session')->setMobileStoreId($params['store']);
            }
            //reset limit to all list
            Mage::helper('simisalestrackingapi')->setLimit(9999);
            $limit = 10;
            if(isset($params['limit']) && $params['limit'] != '' && is_numeric($params['limit'])){
                if($params['limit'] > 0){
                    $limit = $params['limit'];
                }else{
                    $limit = 0;
                }
            }
            $params['limit'] = $limit;
            $page = 0;
            if(isset($params['page']) && $params['page'] != '' && is_numeric($params['page'])){
                if($params['page'] >= 1){
                    $page = $params['page'] - 1;
                }
            }
            $params['page'] = $page;
            
            
            // Is current method of this model
            if (method_exists($this, $method)) {
                return call_user_func_array(array($this, $method), $params); //($param1, $param2)
            }
            // Is an API model
            $list = explode('.', $method);
            if(count($list) == 1) $list[1] = '';
            list($resourceName, $methodName) = $list;
            if (empty($resourceName) || empty($methodName)) {
                if(empty($methodName)){
                    $methodName = 'index';
                }
                if(empty($resourceName)){
                    throw new Exception($this->_helper->__('Invalid method.'), 4);
                }
            }
            $model = Mage::getModel('simisalestrackingapi/api_'.$resourceName);
            if(!$model){
                throw new Exception($this->_helper->__('Invalid method.'), 4);
            }
            $methodName = 'api' . ucfirst($methodName);
            if (is_callable(array(&$model, $methodName))) {
                return call_user_func_array(array(&$model, $methodName), array($params)); //($param1 = array())
            }
            throw new Exception($this->_helper->__('Resource cannot callable.'), 5);
        }
        throw new Exception($this->_helper->__('Not login.'), 1);
    }
    
    public function login($username, $password){
        if(Mage::getModel('simisalestrackingapi/user')->login($username, $password)){
            Mage::getModel('simisalestrackingapi/settings')->saveLogin();
            //clear cache
            Mage::app()->getCacheInstance()->cleanType("config");
            return $username;
        }
        throw new Exception($this->_helper->__('Login failed'), 6);
    }
    
    public function logout(){
        Mage::getModel('simisalestrackingapi/user')->logout();
        return true;
    }
    
    /**
     * check is loged in
     * @return boolean
     */
    public function checkLogin(){
        $session = Mage::getSingleton('admin/session');
        if($session->isLoggedIn()){
            return true;
        }
        return false;
    }
    
    /**
     * check ACL
     */
    protected function checkRole(){
        // bind call name to controller name for check acl
        $session = Mage::getSingleton('admin/session');
        if(!$session->isAllowed('simisalestrackingapi/api')){
            return false;
        }
        return true;
    }
    
    /**
     * api get store list
     */
    public function get_store(){
        $changeStoreBlock = Mage::getBlockSingleton('simisalestrackingapi/changestore');
        $stores = $changeStoreBlock->getStoreList();
        $current = $changeStoreBlock->currentStore();
        $storeList = array();
        foreach ($stores as $st){
            if($st->getCode()==='admin'){
                $name = Mage::helper('simisalestrackingapi')->__('All Store Views');
            }else{
                $name = $st->getName();
            }
            $storeList[] =  array('id'=>(int)$st->getId(), 'name'=>$name);
            
        }
        return array("cur_id"=>(int)$current, "list"=>$storeList);
    }
}

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
 * Simisalestrackingapi Settings Model
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Model_Settings extends Mage_Core_Model_Abstract
{
    const _SETTING_PATH = 'simisalestrackingapi/setting';
    const _SHOW_BESTSELLERS = 'show_bestsellers';
    const _TIME_BESTSELLERS = 'time_bestsellers';
    const _TIME_REFRESH_BESTSELLERS = 'time_refresh_bestsellers';
    const _STATUS_ORDERS = 'status_orders';
    /**
     * save your config value
     * @param type $value is value
     * @param string $code is path or code
     */
    public function saveSetting($value, $code = ''){
        $setting_path = '';
        if($code == ''){
            $setting_path = self::_SETTING_PATH .'/'. $value;
        }else{
            $setting_path = self::_SETTING_PATH .'/'. $code;
        }
        if(is_null($value)) $value = '';
        Mage::getModel('core/config')->saveConfig($setting_path, $value);
    }
    /**
     * this function is alias of saveSetting
     * @param type $value
     * @param type $code
     */
    public function setSetting($value, $code = ''){
        $this->saveSetting($value, $code);
    }
    
    /**
     * get setting
     * @param type $code
     * @return type
     */
    public function getSetting($code){
        $setting_path = self::_SETTING_PATH .'/'. $code;
        return Mage::getStoreConfig($setting_path);
    }
    
    /**
     * save config for earch admin time logged out
     */
    public function saveLogout(){
        $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $date = Mage::getModel('core/date')->gmtDate(); //get GMT date time
        $this->saveSetting($date, 'logout_'.$username); //logout time code is username of admin
    }
    
    /**
     * get logged out date time
     * @return string
     */
    public function getLastLogout(){
        $code = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $date = Mage::app()->getLocale()->date()->subDay(1)->setMinute(0)->setSecond(0); // get time from yestoday when never logout
        $last_yesterday = gmdate("Y-m-d H:i:s", $date->getTimestamp()); //datetime mysql
        if($this->getSetting('logout_'.$code) != ''){
            if (strtotime($last_yesterday) > strtotime($this->getSetting('logout_'.$code))){
                $this->saveSetting($last_yesterday, 'logout_'.$code);
                return $last_yesterday;
            }else{
                return $this->getSetting('logout_'.$code);
            }
        }else{
            $this->saveSetting($last_yesterday, 'logout_'.$code); //logout time code is username of admin
            //clear cache
            Mage::app()->getCacheInstance()->cleanType("config");
            return $last_yesterday;
        }
    }
    
    /**
     * save config for earch admin time login
     */
    public function saveLogin(){
        $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $datetime = Mage::app()->getLocale()->date();
        $datetime->setTimezone('Etc/UTC');
        $last = $this->getLastLogin();
        Mage::getSingleton('admin/session')->setLastLogin($last); // save into session
        $this->saveSetting($datetime->toString(Varien_Date::DATETIME_INTERNAL_FORMAT), 'login_'.$username); //login time code is username of admin
    }
    
    /**
     * get login date time
     * @return string
     */
    public function getLastLogin(){
        $code = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $date = Mage::app()->getLocale()->date()->subDay(1)->setMinute(0)->setSecond(0); // get time from yestoday when never logout
        $last_yesterday = gmdate("Y-m-d H:i:s", $date->getTimestamp()); //datetime mysql
        if(($loginconfig = $this->getSetting('login_'.$code)) != ''){
            return $loginconfig;
        }else{
            return $last_yesterday;
        }
    }
    
    /**
     * save config for earch admin time new customers
     */
    public function saveTimeNewCustomers($time){
        $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $this->saveSetting($time, 'newcustomer_time_'.$username); //login time code is username of admin
    }
    
    /**
     * get date time time new customers
     * @return string
     */
    public function getTimeNewCustomers(){
        $code = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $date = Mage::app()->getLocale()->date()->subDay(1)->setMinute(0)->setSecond(0); // get time from yestoday when never logout
        $last_yesterday = gmdate("Y-m-d H:i:s", $date->getTimestamp()); //datetime mysql
        if(($loginconfig = $this->getSetting('newcustomer_time_'.$code)) != ''){
            return $loginconfig;
        }else{
            return $last_yesterday;
        }
    }
    
    /**
     * save config for earch admin time new orders
     */
    public function saveTimeNewOrders($time){
        $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $this->saveSetting($time, 'neworders_time_'.$username); //login time code is username of admin
    }
    
    /**
     * get date time time new orders
     * @return string
     */
    public function getTimeNewOrders(){
        $code = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $date = Mage::app()->getLocale()->date()->subDay(1)->setMinute(0)->setSecond(0); // get time from yestoday when never logout
        $last_yesterday = gmdate("Y-m-d H:i:s", $date->getTimestamp()); //datetime mysql
        if(($loginconfig = $this->getSetting('neworders_time_'.$code)) != ''){
            return $loginconfig;
        }else{
            return $last_yesterday;
        }
    }
}
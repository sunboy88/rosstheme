<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Collpur_Helper_Notifications extends Mage_Core_Block_Abstract
{
    const ADMIN_EMAIL = 'administrator_email';
    const NOTIFICATIONS_ROOT = 'collpur/notifications/';
    const NOTIFICATIONS_ON = 'collpur/notifications/enable';
    public $emailHelper;

    protected function _construct()
    {
        $this->emailHelper = Mage::getModel('core/email_template');        
    }

    public function dealSucceedTemplateAdmin($data) {
       
         $this->emailHelper->setDesignConfig(array('area' => 'frontend', 'store' => 0))
                ->sendTransactional(
                   Mage::getStoreConfig('collpur/notifications/deal_succeed_template_admin'),
                   $this->getSenderName(),
                   $this->getAdminEmail(),
                   $this->__('Administrator'),
                   $data
                );
    }

    public function dealSucceedTemplateCustomer($data) {
 
         $this->emailHelper->setDesignConfig(array('area' => 'frontend', 'store' => $data['order']->getStoreId()))
                ->sendTransactional(
                   Mage::getStoreConfig('collpur/notifications/deal_succeed_template_customer'),
                   $this->getSenderName($data['order']->getStoreId()),
                   $data['order']->getCustomerEmail(),
                   $data['order']->getCustomerFirstName(),
                   $data,
                   $data['order']->getStoreId()
         );        
    }

    public function dealExpiredTemplateAdmin($data) {
          
          $this->emailHelper->setDesignConfig(array('area' => 'frontend', 'store' => 0))
                ->sendTransactional(
                   Mage::getStoreConfig('collpur/notifications/deal_failed_template_admin'),
                   $this->getSenderName(),
                   $this->getAdminEmail(),
                   $this->__('Administrator'),
                   $data
            );        
    }

     public function dealExpiredTemplateCustomer($data) {
 
         $sender = $this->getSenderName($data['order']->getStoreId());
         $data['deal']->setStoreEmailAddress($sender['email']); 
         $this->emailHelper->setDesignConfig(array('area' => 'frontend', 'store' => $data['order']->getStoreId()))
                ->sendTransactional(
                   Mage::getStoreConfig('collpur/notifications/deal_failed_template_customer'),
                   $sender,
                   $data['order']->getCustomerEmail(),
                   $data['order']->getCustomerFirstName(),
                   $data,
                   $data['order']->getStoreId()
         );
    }


     public function notifyAdminBeforeDealExpired($data) {

          $data['deal']->setExpireAfterDays($data['params']['expire_after_days']);         
          $this->emailHelper->setDesignConfig(array('area' => 'frontend', 'store' => 0))
                ->sendTransactional(
                   Mage::getStoreConfig('collpur/notifications/notify_admin_before_deal_expired_template'),
                   $this->getSenderName(),
                   $this->getAdminEmail(),
                   $this->__('Administrator'),
                   $data
           );
    }

    public function processEmails($function,$params) {
        if(method_exists($this,$function)) {
            $this->{$function}($params);         
        }
    }

    private function getConfig($path, $root = 'collpur/notifications/',$storeId = 0) {
        return Mage::getStoreConfig($root.$path,$storeId);
    }

    private function getAdminEmail($storeId=0) {
        
       $email = @trim((string) $this->getConfig(self::ADMIN_EMAIL,self::NOTIFICATIONS_ROOT,$storeId));       
        if(!$email) {
            return Mage::getStoreConfig("trans_email/ident_general/email", $storeId);
        }
        return $email;
    }


    private function getSenderName($storeId = 0) {
 
        return array(
            'name' => Mage::getStoreConfig("trans_email/ident_{$this->getConfig('email_sender')}/name", $storeId),
            'email'=> Mage::getStoreConfig("trans_email/ident_{$this->getConfig('email_sender')}/email", $storeId)
        );
            
    }

}
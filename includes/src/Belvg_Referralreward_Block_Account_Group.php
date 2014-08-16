<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/******************************************
 *      MAGENTO EDITION USAGE NOTICE      *
 ******************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/******************************************
 *      DISCLAIMER                        *
 ******************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 ******************************************
 * @category   Belvg
 * @package    Belvg_Referralreward
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_Block_Account_Group extends Mage_Core_Block_Template
{
    protected $_moveGroupTo = FALSE;

    public function isEnabled()
    {
        return Mage::helper('referralreward')->storeConfig('move_group/enabled');
    }

    public function getCustomerGroups()
    {
        if (!$this->_moveGroupTo) {
            $this->_moveGroupTo = Mage::helper('referralreward')->decodeStoreConfigMoveGroupTo();
        }

        return $this->_moveGroupTo;
    }

    public function getMaxGroupInvited()
    {
        $moveGroupTo = $this->getCustomerGroups();
        $invited     = 0;
        foreach ($moveGroupTo AS $item) {
            if ($item['invited'] > $invited) {
                $invited = $item['invited'];
            }
        }

        return $invited;
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function getInvitedCount()
    {
        $customerId = (int) $this->_getSession()->getId();
        $status     = Belvg_Referralreward_Model_Friends::FRIEND_INVITED_ACTIVE;
        $collection = Mage::getModel('referralreward/friends')->getFriendsCollection($customerId, $status);
        
        return $collection->count();
    }
}
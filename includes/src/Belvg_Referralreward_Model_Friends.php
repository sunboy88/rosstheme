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

class Belvg_Referralreward_Model_Friends extends Mage_Core_Model_Abstract
{
    /**
     * Statuses of invited friends
     * 0 - friend is already registered and can't bring extra referral points
     * 1 - friend, who is invited (not registered)
     * 2 - friend, registered with a referral link, who may influence points upon purchase
     * 3 - friend, who bring profit
     */
    const FRIEND_NO_BRING       = 0;
    const FRIEND_INVITED        = 1;
    const FRIEND_BRING          = 2;
    const FRIEND_INVITED_ACTIVE = 3;


    public function _construct()
    {
        parent::_construct();
        $this->_init('referralreward/friends');
    }

    /**
     * Checking the email in the customer's database of friends
     *
     * @param int Customer id
     * @param string Email
     * @return int
     */
    public function checkFriendByEmail($customer_id, $email)
    {
        return $this->getCollection()
                ->addFieldToFilter('customer_id', $customer_id)
                ->addFieldToFilter('friend_email', $email)
                ->load()
                ->count();
    }

    /**
     * Checking user status (registered/unregistered), based on the email in the database
     *
     * @param string Email
     * @return int
     */
    public function checkCustomerByEmail($email)
    {
        return Mage::getModel('customer/customer')->getCollection()
                ->addFieldToFilter('email', $email)
                ->load()
                ->count();
    }

    /**
     * Removing email list for a current customer
     *
     * @param int Customer id
     * @param mixed Emails (string/array)
     * @return int
     */
    public function removeFriends($customer_id, $emails)
    {
        if (!is_array($emails) AND $emails != '') {
            $emails = explode(',', $emails);
        }

        $collection = $this->getCollection()
                        ->addFieldToFilter('customer_id', $customer_id)
                        ->addFieldToFilter('friend_email', array("in" => $emails));
        $count      = $collection->count();
        $collection->delete();

        return $count;
    }

    /**
     * Adding freinds to the database for a customer
     * It returns the amount of friends, added to the address book ($count['enable']) and registered on the site ($count['disable'])
     *
     * @param int Customer id
     * @param array Emails
     * @param array Names
     * @return array
     */
    public function saveFriends($customer_id, $emails, $names)
    {
        $count = array(
            'enable'  => 0,
            'disable' => 0
        );
        foreach ($emails AS $i => $email) {
            if ($email && !$this->checkFriendByEmail($customer_id, $email)) {
                $data                   = array();
                $data['friend_name']    = $names[$i];
                $data['friend_email']   = $email;
                $data['customer_id']    = $customer_id;
                if ($this->checkCustomerByEmail($email)) {
                    $data['status']     = self::FRIEND_NO_BRING;
                    $count['disable']++;
                } else {
                    $data['status']     = self::FRIEND_INVITED;
                    $count['enable']++;
                }

                $this->setData($data)->save();
            }
        }

        return $count;
    }

    /**
     * Friend, invited by a current user
     *
     * @param int Customer id
     * @param string Emails
     * @return Belvg_Referralreward_Model_Friends
     */
    public function getItem($customer_id, $email)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('friend_email', $email);

        return $collection->getFirstItem();
    }

    /**
     * Inviting the same friend by different users, except the current one
     *
     * @param int Customer id
     * @param string Emails
     * @return Belvg_Referralreward_Model_Friends_Collection
     */
    public function getOtherItems($customer_id, $email)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('customer_id', array("neq"=>$customer_id))
            ->addFieldToFilter('friend_email', $email);

        return $collection;
    }

    /**
     * All friends with the same status, connected to the currest user
     *
     * @param int Customer id
     * @param array Statuses of invited friends (self::FRIEND_NO_BRING, self::FRIEND_INVITED, self::FRIEND_BRING)
     * @return Belvg_Referralreward_Model_Friends_Collection
     */
    public function getFriendsCollection($customer_id, $statuses)
    {
        if (is_array($statuses)) {
            $condition = array();
            foreach ($statuses AS $status) {
                $condition[] = array('eq' => $status);
            }
        } else {
            $condition = array('eq' => $statuses);
        }

        $collection = $this->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('status', $condition);
        $collection->getSelect()
            ->order('friend_email asc'); //friend_name*/

        //print_r((string)$collection->getSelect()); die;

        return $collection;
    }

    /**
     * Get 'Friend' object of a current customer with FRIEND_BRING status.
     * (the customers is just registered via the referral link)
     *
     * @param int Customer id
     * @return Belvg_Referralreward_Model_Friends
     */
    public function loadCurrentSuccessFriend($customer_id)
    {
        $customer   = Mage::getModel('customer/customer')->load($customer_id);
        $collection = $this->getCollection()
            ->addFieldToFilter('friend_email', $customer->getEmail())
            ->addFieldToFilter('status', self::FRIEND_BRING);

        return $collection->getFirstItem();
    }

    public function loadCurrentSuccessFriends($customer_id)
    {
        $customer   = Mage::getModel('customer/customer')->load($customer_id);
        $collection = $this->getCollection()
            ->addFieldToFilter('friend_email', $customer->getEmail())
            ->load();

        // remove those who have added an email, but never sent him an invitation
        foreach ($collection AS $key => $item) {
            if ($item->getStatus() == self::FRIEND_INVITED && $item->getCountSend() == 0) {
                $collection->removeItemByKey($key);
            }
        }

        return $collection;
    }
}
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

class Belvg_Referralreward_Block_Fetch extends Mage_Core_Block_Template
{
    protected $_providers = FALSE;

    /**
     *
     *
     * @return
     */
    protected function getProviders()
    {
        if (!$this->_providers) {
            $helper = Mage::helper('referralreward');
            $this->_providers = array();

            // GOOGLE
            if ($helper->storeConfig('gmail/enabled')) {
                $scope = 'https://www.google.com/m8/feeds/';
                $this->_providers['gmail'] = array();
                $this->_providers['gmail']['appid']  = $helper->storeConfig('gmail/appid');
                $this->_providers['gmail']['secret'] = $helper->storeConfig('gmail/secret');
                $this->_providers['gmail']['url']    = 'https://accounts.google.com/o/oauth2/auth' .
                                                '?client_id=' . $this->_providers['gmail']['appid'] .
                                                '&redirect_uri=' . $helper->saveFriendsUrl('gmail') .
                                                '&scope=' . $scope .
                                                '&response_type=code';
            }

            // YAHOO
            if ($helper->storeConfig('yahoo/enabled')) {
                $this->_providers['yahoo'] = array();
                $this->_providers['yahoo']['key']    = $helper->storeConfig('yahoo/key');
                $this->_providers['yahoo']['secret'] = $helper->storeConfig('yahoo/secret');
                $this->_providers['yahoo']['appid']  = $helper->storeConfig('yahoo/appid');

                /*include_once 'Yahoo/Yahoo.inc';
                $hasSession   = YahooSession::hasSession($this->_providers['yahoo']['appid'], $this->_providers['yahoo']['secret'], 'wmBW8i7c');
                //$callback   = YahooUtil::current_url()."?in_popup";
                $callback     = $helper->saveFriendsUrl('yahoo')."?in_popup";
                $sessionStore = new NativeSessionStore();
                $this->_providers['yahoo']['url']    = YahooSession::createAuthorizationUrl(
                                                    $this->_providers['yahoo']['appid'],
                                                    $this->_providers['yahoo']['secret'],
                                                    $callback,
                                                    $sessionStore);*/
                $this->_providers['yahoo']['url'] = $helper->saveFriendsUrl('yahoo');
            }

            // HOTMAIL / LIVE
            if ($helper->storeConfig('hotmail/enabled')) {
                $scope = 'wl.signin+wl.basic+wl.contacts_emails';
                $this->_providers['hotmail'] = array();
                $this->_providers['hotmail']['appid']  = $helper->storeConfig('hotmail/appid');
                $this->_providers['hotmail']['secret'] = $helper->storeConfig('hotmail/secret');
                /*$this->_providers['hotmail']['url']  = 'https://consent.live.com/Delegation.aspx' .
                                                  '?RU=' . urlencode($helper->saveFriendsUrl('hotmail')) .
                                                  '&ps=Contacts.View' .
                                                  '&pl=' . urlencode($helper->policyUrl());*/
                $this->_providers['hotmail']['url']    = 'https://login.live.com/oauth20_authorize.srf' .
                                                  '?client_id=' . $this->_providers['hotmail']['appid'] .
                                                  '&scope=' . $scope .
                                                  '&response_type=code' .
                                                  '&redirect_uri=' . urlencode($helper->saveFriendsUrl('hotmail'));
            }
        }

        return $this->_providers;
    }
}
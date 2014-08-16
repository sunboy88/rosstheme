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

class Belvg_Referralreward_Block_Takeout extends Mage_Core_Block_Template{

    /**
     *
     *
     * @return
     */
    protected function getProviders() {
        $helper    = Mage::helper('referralreward');
        $providers = array();

        // GOOGLE
        if ($helper->storeConfig('gmail/enabled')) {
            $scope = 'https://www.google.com/m8/feeds/';
            $providers['gmail']['appid']  = $helper->storeConfig('gmail/appid');
            $providers['gmail']['secret'] = $helper->storeConfig('gmail/secret');
            $providers['gmail']['url']    = 'https://accounts.google.com/o/oauth2/auth' .
                                            '?client_id=' . $providers['gmail']['appid'] .
                                            '&redirect_uri=' . $helper->saveFriendsUrl('gmail') .
                                            '&scope=' . $scope .
                                            '&response_type=code';
        }

        // YAHOO
        if ($helper->storeConfig('yahoo/enabled')) {
            $providers['yahoo']['key']    = $helper->storeConfig('yahoo/key');
            $providers['yahoo']['secret'] = $helper->storeConfig('yahoo/secret');
            $providers['yahoo']['appid']  = $helper->storeConfig('yahoo/appid');

            /*include_once 'Yahoo/Yahoo.inc';
            $hasSession   = YahooSession::hasSession($providers['yahoo']['appid'], $providers['yahoo']['secret'], 'wmBW8i7c');
            //$callback   = YahooUtil::current_url()."?in_popup";
            $callback     = $helper->saveFriendsUrl('yahoo')."?in_popup";
            $sessionStore = new NativeSessionStore();
            $providers['yahoo']['url']    = YahooSession::createAuthorizationUrl(
                                                $providers['yahoo']['appid'],
                                                $providers['yahoo']['secret'],
                                                $callback,
                                                $sessionStore);*/
            $providers['yahoo']['url'] = $helper->saveFriendsUrl('yahoo');
        }

        // HOTMAIL / LIVE
        if ($helper->storeConfig('hotmail/enabled')) {
            $scope = 'wl.signin+wl.basic+wl.contacts_emails';
            $providers['hotmail']['appid']  = $helper->storeConfig('hotmail/appid');
            $providers['hotmail']['secret'] = $helper->storeConfig('hotmail/secret');
            /*$providers['hotmail']['url']  = 'https://consent.live.com/Delegation.aspx' .
											  '?RU=' . urlencode($helper->saveFriendsUrl('hotmail')) .
											  '&ps=Contacts.View' .
											  '&pl=' . urlencode($helper->policyUrl());*/
            $providers['hotmail']['url']    = 'https://login.live.com/oauth20_authorize.srf' .
                                              '?client_id=' . $providers['hotmail']['appid'] .
                                              '&scope=' . $scope .
                                              '&response_type=code' .
                                              '&redirect_uri=' . urlencode($helper->saveFriendsUrl('hotmail'));
        }

        return $providers;
    }

}
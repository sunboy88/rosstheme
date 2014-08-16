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

class Belvg_Referralreward_YahooController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    protected function XmltoArray($xml)
    {
        $array = json_decode(json_encode($xml), TRUE);

        foreach (array_slice($array, 0) AS $key => $value) {
            if (empty($value)) {
                $array[$key] = NULL;
            } elseif (is_array($value)) {
                $array[$key] = $this->XmltoArray($value);
            }
        }

        return $array;
    }

    public function saveAction()
    {
        require 'Yahoo/Yahoo.inc';
        
        $helper = Mage::helper('referralreward');
        //YahooLogger::setDebug(true);
        //YahooLogger::setDebugDestination('LOG');
        define('OAUTH_CONSUMER_KEY', $helper->storeConfig('yahoo/key'));
        define('OAUTH_CONSUMER_SECRET', $helper->storeConfig('yahoo/secret'));
        define('OAUTH_DOMAIN', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
        define('OAUTH_APP_ID', $helper->storeConfig('yahoo/appid'));

        $hasSession = YahooSession::hasSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);

        if ($hasSession == FALSE) {
            $callback     = YahooUtil::current_url() . "?in_popup";
            $sessionStore = new NativeSessionStore();
            $authUrl      = YahooSession::createAuthorizationUrl(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $callback, $sessionStore);
            header("Location: " . $authUrl);
            exit;
        } else {
            $session = YahooSession::requireSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);

            if ($session) {
                $user      = $session->getSessionedUser();
                //$profile = $user->getProfile();
                $contacts  = $user->getContactSync();
                if ($contacts) {
                    $contacts = $this->XmltoArray($contacts);
                    $names    = array();
                    $emails   = array();
                    foreach ($contacts['contactsync']['contacts'] as $key=>$profileContact) {
                        foreach ($profileContact['fields'] as $contact) {
                            $emails[] = $contact['value'];
                            $names[]  = '';
                        }
                    }

                    YahooSession::clearSession();

                    $customer_id = (int)$this->_getSession()->getId();
                    $count       = Mage::getModel('referralreward/friends')->saveFriends($customer_id, $emails, $names);
                    if ($count['enable']) {
                        if ($count['enable'] > 1) {
                            $this->_getSession()->addSuccess($this->__('%s have been added', $count['enable']));
                        } else {
                            $this->_getSession()->addSuccess($this->__('%s has been added', $count['enable']));
                        }
                    }

                    if ($count['disable']) {
                        $this->_getSession()->addSuccess($this->__('%s already registered', $count['disable']));
                    }

                    if ($count['enable'] == 0 && $count['disable'] == 0) {
                        $this->_getSession()->addError($this->__("no emails were added"));
                    }
                } else {
                    $this->_getSession()->addError($this->__("no emails were added"));
                }
            }

            echo"<script>
                    window.opener.location.reload(true);
                    window.close();
                </script>";
        }
    }

}
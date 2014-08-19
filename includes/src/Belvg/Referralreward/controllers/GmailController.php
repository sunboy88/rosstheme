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

class Belvg_Referralreward_GmailController extends Mage_Core_Controller_Front_Action
{
    public function likesAction()
    {
        $likes = Mage::helper('referralreward/google')->getShared();
        $this->getResponse()->setBody($likes);

        return;
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

    public function saveAction()
    {
        //Oauth 2.0: exchange token for session token so multiple calls can be made to api
        if (isset($_REQUEST['code'])) {
            $_SESSION['accessToken'] = $this->getAccessToken($_REQUEST['code']);
            if (isset($_SESSION['accessToken']) AND $_SESSION['accessToken']) {
                $contactsXml = $this->callApi($_SESSION['accessToken']);

                try {
                    $xml = new SimpleXMLElement($contactsXml);
                    $xml->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
                    $addresses = $xml->xpath('//gd:email');
                    $names     = array();
                    $emails    = array();

                    foreach ($addresses AS $email) {
                        $emails[] = $email->attributes()->address;
                        $names[]  = '';
                    }

                    $customerId = (int)$this->_getSession()->getId();
                    $count      = Mage::getModel('referralreward/friends')->saveFriends($customerId, $emails, $names);
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
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__("no emails were added"));
                }
            } else {
                //echo"<p class='errorMessage'>Google Error</p>";
                $this->_getSession()->addError($this->__("no emails were added"));
            }

            echo"<script>
                    window.opener.location.reload(true);
                    window.close();
                 </script>";
        }
    }

    protected function getAccessToken($code)
    {
        $helper = Mage::helper('referralreward');

        $oauth2tokenUrl  = "https://accounts.google.com/o/oauth2/token";
        $clienttokenPost = array(
            "code"          => $code,
            "client_id"     => $helper->storeConfig('gmail/appid'),
            "client_secret" => $helper->storeConfig('gmail/secret'),
            "redirect_uri"  => $helper->saveFriendsUrl('gmail'),
            "grant_type"    => "authorization_code",
        );

        $curl = curl_init($oauth2tokenUrl);

        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $clienttokenPost);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $jsonResponse = curl_exec($curl);
        curl_close($curl);
        $authObj = json_decode($jsonResponse);

        if (isset($authObj->access_token)) {
            $accessToken = $authObj->access_token;
            return $accessToken;
        }

        return FALSE;
    }

    protected function callApi($accessToken)
    {
        $url  = 'https://www.google.com/m8/feeds/contacts/default/full/?max-results=9999999&oauth_token=' . $accessToken;
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        $xmlresponse = curl_exec($curl);
        curl_close($curl);
 
        return $xmlresponse;
    }



}
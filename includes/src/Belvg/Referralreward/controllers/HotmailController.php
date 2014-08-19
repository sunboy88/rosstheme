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

class Belvg_Referralreward_HotmailController extends Mage_Core_Controller_Front_Action
{
    /**
     * Imported contacts
     * 
     * @var array
     * @access private
     */
    private $contacts = array();

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
        if (isset($_REQUEST['code'])) {
            $_SESSION['accessToken'] = $this->getAccessToken($_REQUEST['code']);
            if (isset($_SESSION['accessToken'])) {
                $jsonContacts = $this->callApi($_SESSION['accessToken']);
                $contacts     = json_decode($jsonContacts);

                //print_r($contacts); die;
                $names  = array();
                $emails = array();
                if (is_array($contacts->data)) {
                    foreach ($contacts->data AS $item) {
                        $names[]      = $item->name;
                        $collection   = $item->emails;
                        if (is_array($collection)) {
                            $emails[] = $collection[0]->preferred;
                        } else {
                            $emails[] = $collection->preferred;
                        }
                    }
                }

                //print_r($emails); die;
                $customerId = (int)$this->_getSession()->getId();
                $count      = Mage::getModel('referralreward/friends')->saveFriends($customerId, $emails, $names);
                if ($count['enable']) {
                    if ($count['enable']>1) {
                        $this->_getSession()->addSuccess($this->__('%s have been added', $count['enable']));
                    } else {
                        $this->_getSession()->addSuccess($this->__('%s has been added', $count['enable']));
                    }
                }

                if ($count['disable']) {
                    $this->_getSession()->addSuccess($this->__('%s already registered', $count['disable']));
                }

                if ($count['enable']==0 && $count['disable']==0) {
                    $this->_getSession()->addError($this->__("no emails were added"));
                }
            } else {
                //echo"<p class='errorMessage'>Hotmail Error</p>";
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

        $oauth2token_url  = "https://login.live.com/oauth20_token.srf";
        $clienttoken_post = array(
            "code"          => $code,
            "client_id"     => $helper->storeConfig('hotmail/appid'),
            "client_secret" => $helper->storeConfig('hotmail/secret'),
            "redirect_uri"  => $helper->saveFriendsUrl('hotmail'),
            "grant_type"    => "authorization_code",
        );

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $oauth2token_url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($clienttoken_post));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));

        $jsonResponse = curl_exec($curl);
        curl_close($curl);
        $authObj = json_decode($jsonResponse);

        if (isset($authObj->refresh_token)) {
            global $refreshToken;
            $refreshToken = $authObj->refresh_token;
        }

        $accessToken = $authObj->access_token;

        return $accessToken;
    }

    protected function callApi($accessToken)
    {
        $url  = 'https://apis.live.net/v5.0/me/contacts?access_token=' . $accessToken;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
        $jsonResponse = curl_exec($curl);
        curl_close($curl);

        return $jsonResponse;
    }

}
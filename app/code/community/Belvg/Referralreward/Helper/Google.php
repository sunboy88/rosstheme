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

class Belvg_Referralreward_Helper_Google extends Mage_Core_Helper_Abstract
{
    protected $_google = NULL;
    protected $_uid    = NULL;

    /**
     * The Facebook is enabled/disabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig('referralreward/gmail/enabled', Mage::app()->getStore());
    }

    /**
     * Facebook App ID/API Key
     *
     * @return string
     */
    public function getAppId()
    {
        return Mage::getStoreConfig('referralreward/gmail/appid', Mage::app()->getStore());
    }

    /**
     * Facebook App Secret
     *
     * @return string
     */
    public function getSecret()
    {
        return Mage::getStoreConfig('referralreward/gmail/secret', Mage::app()->getStore());
    }

    public function getShared()
    {
        //$uid  = $this->getUserId();
        $uid  = '105719935823527948817';
        /*$url  = 'http://stylehatch.co/';
        $post = '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]';
        if ($uid) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            $curl_results = curl_exec ($curl);
            curl_close ($curl);
            $json = json_decode($curl_results, true);

            print_r($json);
            return intval( $json[0]['result']['metadata']['globalCounts']['count'] );
        }*/

        //$uid  = 'me';
        $url  = 'http://stylehatch.co/';
        $url  = 'https://plus.google.com/_/plusone/get?oid=' . $uid . '&u=' . $url;
        if ($uid) {
            $data = file_get_contents($url);
            $array = explode(',', $data);

            $links = Array();

            for ($i = 0; $i < count($array); $i++) {
                if (substr($array[$i], 0, 3) == "[\"h") {
                    $t['Link']  = str_replace("[", "", str_replace("\"", "", $array[$i]));
                    $t['Title'] = str_replace("\"", "", $array[$i+1]);
                    $t['Site']  = str_replace("\"", "", $array[$i+3]);

                    $links[] = $t;
                }
            }

            return $links;
            return json_encode($links);
        }

        return FALSE;
    }
}
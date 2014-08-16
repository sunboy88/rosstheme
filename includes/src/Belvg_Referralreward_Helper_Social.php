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

class Belvg_Referralreward_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getFacebookShared()
    {
        $helper   = Mage::helper('referralreward/facebook');
        $facebook = new Facebook_Api(array(
            'appId'  => $helper->getAppId(),
            'secret' => $helper->getSecret(),
            'cookie' => TRUE,
        ));

        $likes = $facebook->api(array(
            "method" => "fql.query",
            "query"  => "select uid from page_fan where uid=" . $uid . " and page_id=" . Mage::getStoreConfig('facebookall/bonus/fbpage_id')
        ));

        return $likes;
    }
}
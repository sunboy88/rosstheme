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

class Belvg_Referralreward_Block_Facebook extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
    }


    /**
     * Return current facebook user id
     *
     * @return string
     */
    public function checkFbUser()
    {
        /*$cookie = $this->get_facebook_cookie($this->getAppId(), $this->getSecret());
        if (isset($cookie['access_token'])) {
            $me     = json_decode($this->getFbData('https://graph.facebook.com/me?access_token='.$cookie['access_token']));
            if (!empty($me->id)) {
                return $me->id;
            }
        }

        return 0;*/
        return Mage::helper('referralreward/facebook')->getUserId();
    }
}
<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Collpur_Helper_Deals extends Mage_Core_Helper_Abstract 
{
    const RUNNING = 'active';
    const NOT_RUNNING = 'future';
    const CLOSED = 'closed';
    const FEATURED = 'featured';
    public static $featuredDealId = NULL;
    
    public static $menus = array();
 
    public function getTimeLeftToBuy($deal=false, $time=false, $now=false, $gmtTo=false) {
        
        if ($now === false && $gmtTo === false && $deal !== false && $time !== false) { 
            $gmtTo = AW_Collpur_Helper_Data::getGmtTimestamp($deal->getData($time));
            $now = AW_Collpur_Helper_Data::getGmtTimestamp(true, true);
        }
        
        $amount = $gmtTo - $now; 
        $days = floor($amount / 86400);
        $amount = $amount % 86400;
        $hours = floor($amount / 3600);
        $amount = $amount % 3600;
        $minutes = floor($amount / 60);
        $amount = $amount % 60;
        $seconds = floor($amount);

        if ($minutes < 10) {
            $minutes = "0{$minutes}";
        }
        if ($seconds < 10) {
            $seconds = "0{$seconds}";
        }

        $daysPHP = $days > 1 || $days == 0 ? Mage::helper('collpur')->__('days') : Mage::helper('collpur')->__('day');
        $hoursPHP = $hours > 1 || $hours == 0 ? Mage::helper('collpur')->__('hours') : Mage::helper('collpur')->__('hours');
        $minutesPHP = $minutes > 1 || $minutes == 0 ? Mage::helper('collpur')->__('minutes') : Mage::helper('collpur')->__('minute');
        $secondsPHP = $seconds > 1 || $seconds == 0 ? Mage::helper('collpur')->__('seconds') : Mage::helper('collpur')->__('second');

        return "{$days} $daysPHP {$hours}:{$minutes}"; //{seconds};
    }

    public static function setActiveMenus($menu) {

            self::$menus[] = $menu;

    }

    public static function getSectionsAssoc() {
        return array(
            self::RUNNING=>'Active deals',
            self::NOT_RUNNING=>'Upcoming deals',
            self::CLOSED=>'Closed deals',
            self::FEATURED=>'Featured deal'
        );        
    }

    public static function getSectionsKeys() {
        return array_keys(self::getSectionsAssoc());
    }

    public static function clearData() {

        self::$menus[] = array();
    }

}
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


class AW_Collpur_Model_Source_Menus {

    public static function getMenusArray($withData = true) { 

        $menu = array (
            "0" => array(
                'name' => Mage::helper('collpur')->__('Featured Deal'),
                'key' => AW_Collpur_Helper_Deals::FEATURED,
                'alias'=>'featured',
                'size'=>self::getMenuSize(AW_Collpur_Helper_Deals::FEATURED),
                'validator'=>'isArchived'),
            "1" => array(
                'name' => Mage::helper('collpur')->__('All Deals'),
                'key' => AW_Collpur_Helper_Deals::RUNNING,
                'alias'=>'all',
                'size'=>self::getMenuSize(AW_Collpur_Helper_Deals::RUNNING),
                'validator'=>'isRunning'),
            "2" => array(
                'name' => Mage::helper('collpur')->__('Upcoming Deals'),
                'key' => AW_Collpur_Helper_Deals::NOT_RUNNING,
                'alias'=>'future',
                'size'=>self::getMenuSize(AW_Collpur_Helper_Deals::NOT_RUNNING),
                'validator'=>'isNotRunning'),
            "3" => array(
                'name' => Mage::helper('collpur')->__('Closed Deals'),
                'key' => AW_Collpur_Helper_Deals::CLOSED,
                'alias'=>'closed',
                'size'=>self::getMenuSize(AW_Collpur_Helper_Deals::CLOSED),
                'validator'=>'isClosed')
        );

        if($withData) {
            self::addDataToMenu($menu);
        }

        $data = new Varien_Object($menu);
        Mage::dispatchEvent('awcp_menu_init_after', array('menu' => $data));
        return $data->getData();
    }

    private static function getMenuSize($section) {
        return Mage::getModel('collpur/deal')->getMenuSize($section);        
    }

    public function toOptionArray() {
        $menus = AW_Collpur_Model_Source_Menus::getMenusArray();
        $options = array();

        for ($i = 0; $i < count($menus); $i++) {
            $options[$i]['label'] = $menus[$i]['name'];
            $options[$i]['value'] = $menus[$i]['key'];
        }
        return $options;
    }

    public static function getFirstAvailable() {
     
        $menus = AW_Collpur_Model_Source_Menus::getMenusArray(true);
      
        foreach ($menus as $menu) {
            if($menu['skip'] == 0 && $menu['size']) { return $menu['key']; }
        }

        return false;

    }

    public static function addDataToMenu(&$menus) {

        for($i=0;$i<count($menus); $i++) { 
               if(!Mage::getStoreConfig("collpur/{$menus[$i]['alias']}/enabled")) {
                   $menus[$i]['skip'] = 1;
               }
               else {
                    $menus[$i]['skip'] = 0;
               }
                if ((int) $order = Mage::getStoreConfig("collpur/{$menus[$i]['alias']}/order")) {
                    $menus[$i]['order'] = $order;
                } else {
                    $menus[$i]['order'] = $i;
                }
                if ((string) $title = Mage::getStoreConfig("collpur/{$menus[$i]['alias']}/title")) {
                    $menus[$i]['title'] = $title;
                } else {
                    $menus[$i]['title'] = $menus[$i]['name'];
                }
          }
            
        usort($menus,array('AW_Collpur_Model_Source_Menus','addSortOrder'));

    }


    public static function addSortOrder($a,$b) {

         return ($a['order'] > $b['order']) ? +1 : -1;
    }

    public static function isNotAllowed($section) {

         $menus = self::getMenusArray();         
         foreach($menus as $menu) {
             if($menu['key'] == $section) {
                 $section = $menu['alias'];
                 break;
             } 
         }

        if(!$section) return false;
        if(!Mage::getStoreConfig("collpur/{$section}/enabled")) return true;
        return false;
    }
 

}
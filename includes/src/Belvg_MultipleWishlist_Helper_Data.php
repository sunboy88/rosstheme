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
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
/*
 * This package designed for Magento COMMUNITY edition BelVG does not guarantee
 * correct work of this extension on any other Magento edition except Magento
 * COMMUNITY edition. BelVG does not provide extension support in case of
 * incorrect edition usage. /*************************************** DISCLAIMER
 * * ***************************************
 */
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
*****************************************************
* @category   Belvg
* @package    Belvg_MultipleWishlist
* @author     Victor Potseluyonok
* @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
* @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
*/

class Belvg_MultipleWishlist_Helper_Data extends Mage_Core_Helper_Abstract
{

    const ISENABLED_XML_PATH = 'multiplewishlist/settings/enabled';

    const NEW_WISHLIST_ITEM_INDEX = 1;

    public function isEnabled ()
    {
        return (bool) Mage::getStoreConfig(self::ISENABLED_XML_PATH);
    }

    public function getDefaultTabs ()
    {
        return array(
                new Varien_Object(
                        array(
                                'id' => 0,
                                'wishlist_name' => $this->__('Main')
                        )),
                new Varien_Object(
                        array(
                                'id' => - 1,
                                'wishlist_name' => $this->__('New Wishlist')
                        ))
        );
    }

    public function prepareTabs ($tabs, $include_new_wishlist = TRUE)
    {
        $_items = $this->getDefaultTabs();

        if (! $include_new_wishlist) {
            unset($_items[self::NEW_WISHLIST_ITEM_INDEX]);
        }

        foreach ($tabs as $item) {
            $_items[] = new Varien_Object(
                    array(
                            'id' => $item->getId(),
                            'wishlist_name' => $item->getWishlistName()
                    ));
        }

        return $this->sortTabs($_items, $include_new_wishlist);
    }

    protected function sortTabs ($tabs, $include_new_wishlist)
    {
        if (! $include_new_wishlist) {
            return $tabs;
        }

        $new_wishlist = $tabs[self::NEW_WISHLIST_ITEM_INDEX];
        unset($tabs[self::NEW_WISHLIST_ITEM_INDEX]);
        $tabs[] = $new_wishlist;

        return $tabs;
    }

    public function getWishlistConfig ()
    {
        return array(
                'moveUrl' => Mage::getUrl('wishlist/index/moveitem'),
                'noItemSelectMess' => $this->__('Please select items.'),
                'deleteTabUrl' => Mage::getUrl('wishlist/index/deletetab'),
                'deleteTabMess' => $this->__(
                        'Do you really want to delete this Wishlist along with all products in it?')
        );
    }
}
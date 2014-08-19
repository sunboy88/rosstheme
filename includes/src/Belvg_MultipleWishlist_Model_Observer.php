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
* @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
* @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
*/
class Belvg_MultipleWishlist_Model_Observer
{

    protected $_itemId;

    public function addWishlist (Varien_Object $observer)
    {
        if (Mage::helper('multiplewishlist')->isEnabled()) {
            $wishlist = $observer->getWishlist();
            $item = $observer->getItem();
            
            $wishlist_tab_id = (int) Mage::app()->getRequest()->getParam(
                    'tab_id');
            $wishlist_tab_name = Mage::app()->getRequest()->getParam(
                    'wishlist_name');
            
            if (! is_null($wishlist_tab_id) && $wishlist_tab_id != 0) {
                $tab = Mage::getModel('multiplewishlist/tab')->getTab($wishlist, 
                        $wishlist_tab_id, $wishlist_tab_name);
                if ($tab->getId()) {
                    Mage::getModel('multiplewishlist/item')->getItem($wishlist, 
                            $tab, $item);
                    Mage::getModel('core/session')->setWishlistActiveTabId(
                            $tab->getId());
                }
            } elseif ($wishlist_tab_id == 0) {
                $item = Mage::getModel('multiplewishlist/item')->checkItemExists(
                        $wishlist->getId(), $item->getId());
                
                if ($item) {
                    $item->delete();
                }
            }
        }
    }

    /*
    public function setWislistItem (Varien_Object $observer)
    {
        $id = $observer->getItemId();
        $this->_itemId = $id;
    }

    public function updateWishlistItem (Varien_Object $observer)
    {
        if (Mage::helper('multiplewishlist')->isEnabled()) {
            $wishlist = $observer->getWishlist();
            $item = $observer->getItem();
            $product = $observer->getProduct();
            
            $item_old = Mage::getModel('multiplewishlist/item')->checkItemExists(
                    $wishlist->getId(), $item->getId());
            
            if ($item_old) {
                $tab = Mage::getModel('multiplewishlist/tab')->checkTabExistsById(
                        $wishlist->getId(), $item_old->getWishlistTabId());
                
                $item = $wishlist->getItemCollection()->addFieldToFilter('product_id', $product->getId())->getFirstItem();
                
                Mage::getModel('multiplewishlist/item')->getItem($wishlist, 
                            $tab, $item);
                
                if ($item_old) {
                    $item_old->delete();
                }
            }
        }
    } */
}
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

class Belvg_MultipleWishlist_Model_Item extends Mage_Core_Model_Abstract
{

    public function _construct ()
    {
        $this->_init('multiplewishlist/item');
    }

    public function getItem ($wishlist, $tab, $item)
    {
        if ($_item = $this->checkItemExists($wishlist->getId(), $item->getId(), 
                $tab->getId())) {
            return $_item;
        }
        
        return $this->_addItem($wishlist, $tab, $item);
    }

    protected function _addItem ($wishlist, $tab, $_item)
    {
        $item = new Belvg_MultipleWishlist_Model_Item();
        $item->setWishlistId($wishlist->getId());
        $item->setWishlistTabId($tab->getId());
        $item->setItemId($_item->getId());
        $item->save();
        
        return $item;
    }

    public function checkItemExists ($wishlist_id, $item_id, $tab_id = NULL)
    {
        $item = $this->getCollection()
            ->addFieldToFilter('wishlist_id', 
                array(
                        'eq' => $wishlist_id
                ))
            ->addFieldToFilter('item_id', 
                array(
                        'eq' => $item_id
                ))
            ->getFirstItem();
        
        if ($item->getId()) {
            if ($item->getWishlistTabId() != $tab_id && $tab_id != 0) {
                $item->setWishlistTabId($tab_id);
                $item->save();
            }
            
            return $item;
        }
        
        return FALSE;
    }

    public function moveItems ($wishlist, $itemIds, $tabId)
    {
        if ($tabId != 0 && ! ($tab = Mage::getModel('multiplewishlist/tab')->checkTabExistsById(
                $wishlist->getId(), $tabId))) {
            Mage::throwException(
                    Mage::helper('multiplewishlist')->__(
                            "Requested wishlist doesn't exist"));
        } elseif ($tabId == 0) {
            $tab = Mage::helper('multiplewishlist')->getDefaultTabs();
            $tab = $tab[0];
        }
        
        foreach ($itemIds as $k => $itemId) {
            $item = $this->checkItemExists($wishlist->getId(), $itemId, $tabId);
            
            if (! $item) {
                $item = new Belvg_MultipleWishlist_Model_Item();
            }
            
            if ($item->getId() && $tabId == 0) {
                $item->delete();
            } else {
                $item->setWishlistId($wishlist->getId());
                $item->setWishlistTabId($tabId);
                $item->setItemId($itemId);
                $item->save();
            }
        }
        
        return array(
                'item' => $item,
                'tab' => $tab
        );
    }
}
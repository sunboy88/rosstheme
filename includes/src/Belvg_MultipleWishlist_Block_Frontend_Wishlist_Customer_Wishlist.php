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
class Belvg_MultipleWishlist_Block_Frontend_Wishlist_Customer_Wishlist extends Mage_Wishlist_Block_Customer_Wishlist
{

    protected $_wishlistTabs;

    protected $_activeTabId;

    public function getWishlistTabs ()
    {
        if (is_null($this->_wishlistTabs)) {
            $tabs = Mage::getModel('multiplewishlist/tab')->getTabs();
            $this->_wishlistTabs = Mage::helper('multiplewishlist')->prepareTabs(
                    $tabs, FALSE);
        }

        return $this->_wishlistTabs;
    }

    public function getWishlistItems ()
    {
        if (! Mage::helper('multiplewishlist')->isEnabled()) {
            return parent::getWishlistItems();
        }

        $tab_id = func_get_arg(0);

        if (! isset($this->_collection[$tab_id]) ||
                 is_null($this->_collection[$tab_id])) {
            $this->_collection[$tab_id] = Mage::getSingleton(
                    'multiplewishlist/tab')->getCollectionForTab($tab_id);
        }

        return $this->_collection[$tab_id];
    }

    public function getActiveTabId ()
    {
        if (is_null($this->_activeTabId)) {
            $this->_activeTabId = Mage::getModel('core/session')->getWishlistActiveTabId();
            Mage::getModel('core/session')->setWishlistActiveTabId(NULL);
        }

        return $this->_activeTabId;
    }

    public function isShowButton ($tabId)
    {
        return count($this->getWishlistItems($tabId)) > 0;
    }

    public function getJsonConfig ()
    {
        return Zend_Json::encode(
                Mage::helper('multiplewishlist')->getWishlistConfig());
    }
}
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
class Belvg_MultipleWishlist_Block_Frontend_Wishlist_Buttons_Move extends Mage_Core_Block_Template
{

    protected $_options;

    public function getWishlist ()
    {
        return Mage::helper('wishlist')->getWishlist();
    }

    public function isShow ()
    {
        if (count($this->getOptions()) > 0) {
            return TRUE;
        }
        
        return FALSE;
    }

    public function getTabs ()
    {
        return $this->getLayout()
            ->getBlock('customer.wishlist')
            ->getWishlistTabs();
    }

    public function getOptions ()
    {
        if (! isset($this->_options[$this->getTabId()]) ||
                 is_null($this->_options[$this->getTabId()])) {
            $tabs = $this->getTabs();
            
            foreach ($tabs as $k => $tab) {
                if ($tab->getId() == $this->getTabId()) {
                    unset($tabs[$k]);
                }
            }
            
            $this->_options[$this->getTabId()] = $tabs;
        }
        
        return $this->_options[$this->getTabId()];
    }
}
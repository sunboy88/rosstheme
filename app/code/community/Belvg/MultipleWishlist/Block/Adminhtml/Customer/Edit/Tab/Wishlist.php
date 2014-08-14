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
class Belvg_MultipleWishlist_Block_Adminhtml_Customer_Edit_Tab_Wishlist extends Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist
{

    protected function _prepareCollection ()
    {
        if (! Mage::helper('multiplewishlist')->isEnabled()) {
            return parent::_prepareCollection();
        }

        
        
        if (method_exists($this, '_createCollection')) {
            $collection = $this->_createCollection()
                ->addCustomerIdFilter($this->_getCustomer()
                ->getId())
                ->resetSortOrder()
                ->addDaysInWishlist()
                ->addStoreData();
        } else {
            $wishlist = Mage::getModel('wishlist/wishlist');
            $collection = $wishlist->loadByCustomer($this->_getCustomer())
            ->getItemCollection()
            ->resetSortOrder()
            ->addDaysInWishlist()
            ->addStoreData();
            
            /*@var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
            
            $collection->getSelect()->reset('where');
            $collection->getSelect()->where('main_table.wishlist_id', $wishlist->getId());
        }
        
        Mage::getModel('multiplewishlist/tab')->addWishlistTabToSelect(
                $collection);

        $this->setCollection($collection);

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns ()
    {
        $this->addColumnAfter('wishlist_tab_id',
                array(
                        'header' => $this->__('Wishlist'),
                        'index' => 'wishlist_tab_id',
                        'filter' => FALSE,
                        'renderer' => new Belvg_MultipleWishlist_Block_Adminhtml_Customer_Edit_Tab_Renderer_Name()
                ), 'product_name');

        return parent::_prepareColumns();
    }
}
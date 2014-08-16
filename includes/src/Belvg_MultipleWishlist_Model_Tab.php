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

class Belvg_MultipleWishlist_Model_Tab extends Mage_Core_Model_Abstract
{

    const NEW_TAB = - 1;

    protected $_itemWislistCollection;

    protected $_collectionTabs;

    public function _construct ()
    {
        $this->_init('multiplewishlist/tab');
    }

    public function getTab ($wishlist, $wishlist_tab_id, $wishlist_tab_name)
    {
        $wishlist_tab_name = trim($wishlist_tab_name);
        
        if (empty($wishlist_tab_name) && $wishlist_tab_id == self::NEW_TAB) {
            return new Varien_Object(array('id'=>0));
        }
        
        if ($tab = $this->_getTab($wishlist->getId(), $wishlist_tab_id, 
                $wishlist_tab_name)) {
            return $tab;
        } else {
            return $this->_addTab($wishlist->getId(), $wishlist_tab_name);
        }
        
        return FALSE;
    }

    protected function _getTab ($wishlist_id, $wishlist_tab_id, 
            $wishlist_tab_name)
    {
        if ($wishlist_tab_id == self::NEW_TAB) {
            if ($tab = $this->checkTabExistsByName($wishlist_id, 
                    $wishlist_tab_name)) {
                return $tab;
            }
        } else {
            if ($tab = $this->checkTabExistsById($wishlist_id, $wishlist_tab_id)) {
                return $tab;
            }
        }
        
        return FALSE;
    }

    public function checkTabExistsByName ($wishlist_id, $wishlist_tab_name)
    {
        if (empty($wishlist_tab_name)) {
            return FALSE;
        }
        
        $model = $this->getCollection()
            ->addFieldToFilter('wishlist_id', 
                array(
                        'eq' => $wishlist_id
                ))
            ->addFieldToFilter('wishlist_name', 
                array(
                        'eq' => $wishlist_tab_name
                ))
            ->getFirstItem();
        
        if ($model->getId()) {
            return $model;
        }
        
        return FALSE;
    }

    public function checkTabExistsById ($wishlist_id, $wishlist_tab_id)
    {
        $model = $this->getCollection()
            ->addFieldToFilter('wishlist_id', 
                array(
                        'eq' => $wishlist_id
                ))
            ->addFieldToFilter('entity_id', 
                array(
                        'eq' => $wishlist_tab_id
                ))
            ->getFirstItem();
        
        if ($model->getId()) {
            return $model;
        }
        
        return FALSE;
    }

    protected function _addTab ($wishlist_id, $wishlist_tab_name)
    {
        if (empty($wishlist_tab_name)) {
            return FALSE;
        }
        
        $model = new Belvg_MultipleWishlist_Model_Tab();
        $model->setWishlistId($wishlist_id);
        $model->setWishlistName($wishlist_tab_name);
        $model->save();
        
        return $model;
    }

    public function getTabs ()
    {
        $wishlist = $this->getWishlist();
        
        if (! $wishlist) {
            return array();
        }
        
        $collection = $this->getCollection()
            ->addFieldToFilter('wishlist_id', array(
                'eq' => $wishlist->getId()
        ))
            ->setOrder('entity_id', 'ASC')
            ->load();
        return $collection;
    }

    public function getWishlist ($wishlistId = NULL)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        
        if (! $customerId && ! $wishlistId) {
            return;
        }
        
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }
        
        try {
            
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist');
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, TRUE);
            }
            
            if (! $wishlist->getId() ||
                     ($wishlist->getCustomerId() != $customerId && $customerId)) {
                return FALSE;
            }
            
            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            return FALSE;
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e, 
                    Mage::helper('wishlist')->__(
                            'Wishlist could not be created.'));
            return FALSE;
        }
        
        return $wishlist;
    }

    public function joinTabToSelect ($collection)
    {
        /* @var $collection Mage_Wishlist_Model_Resource_Wishlist_Collection */
        $collection->getSelect()->join(
                array(
                        'bitem' => Mage::getModel('core/resource')->getTableName(
                                'multiplewishlist/item')
                ), 'bitem.item_id=main_table.wishlist_item_id', 
                array(
                        'bitem.wishlist_tab_id'
                ));
        
        return $collection;
    }

    public function addWishlistTabToSelect ($collection, $alias = 'tab')
    {
        $this->addWishlistTabIdToSelect($collection, 'bitem');
        
        $collection->getSelect()->joinLeft(
                array(
                        $alias => Mage::getModel('core/resource')->getTableName(
                                'multiplewishlist/tab')
                ), 'bitem.wishlist_tab_id = ' . $alias . '.entity_id', 
                array(
                        'wishlist_name',
                        'bitem.wishlist_tab_id'
                ));
        
        return $collection;
    }

    public function addWishlistTabIdToSelect ($collection, $alias = 'bitem', 
            $tabId = NULL)
    {
        $tabId = (int) $tabId;
        
        if ($tabId == 0) {
            $cond = '';
        } else {
            $cond = ' AND bitem.wishlist_tab_id=' . (int) $tabId;
        }
        
        $methodName = $tabId == 0 ? 'joinLeft' : 'join';
        
        $collection->getSelect()->$methodName(
                array(
                        $alias => Mage::getModel('core/resource')->getTableName(
                                'multiplewishlist/item')
                ), $alias . '.item_id=main_table.wishlist_item_id' . $cond, 
                array(
                        $alias . '.wishlist_tab_id'
                ));
    }

    public function getWishlistItemCollection ($wishlistId = NULL, $tabId = NULL)
    {
        if (is_null($this->_itemWislistCollection)) {
            $currentWebsiteOnly = ! Mage::app()->getStore()->isAdmin();
            $this->_itemWislistCollection = Mage::getResourceModel(
                    'wishlist/item_collection')->addFieldToFilter(
                    'main_table.wishlist_id', 
                    $this->getWishlist($wishlistId)
                        ->getId())
                ->addStoreFilter(
                    Mage::getModel('wishlist/wishlist')->getSharedStoreIds(
                            $currentWebsiteOnly))
                ->setVisibilityFilter();
            
            $this->addWishlistTabIdToSelect($this->_itemWislistCollection, 
                    'bitem', $tabId);
            
            if ($tabId === 0) {
                $this->_itemWislistCollection->getSelect()->where(
                        'ISNULL(bitem.wishlist_tab_id)');
            }
        }
        
        return $this->_itemWislistCollection;
    }

    protected function _getCollectionTabs ()
    {
        if (is_null($this->_collectionTabs)) {
            $collection = $this->getWishlistItemCollection();
            
            foreach ($collection as $item) {
                $this->_collectionTabs[(int) $item->getWishlistTabId()][] = $item;
            }
        }
        
        return $this->_collectionTabs;
    }

    public function getCollectionForTab ($tab_id)
    {
        $collection = $this->_getCollectionTabs();
        
        return isset($collection[$tab_id]) ? $collection[$tab_id] : array();
    }
}
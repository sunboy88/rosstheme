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
require_once 'Mage/Wishlist/controllers/IndexController.php';

class Belvg_MultipleWishlist_IndexController extends Mage_Wishlist_IndexController
{

    public function getTabId ()
    {
        return (int) Mage::app()->getRequest()->getParam('tab-id');
    }

    public function addWishlistAction ()
    {
        if (! $this->_getWishlist() ||
                 ! Mage::helper('multiplewishlist')->isEnabled()) {
            return $this->norouteAction();
        }
        
        $session = Mage::getSingleton('customer/session');
        
        $name = Mage::app()->getRequest()->getParam('wishlist-name');
        
        $name = trim($name);
        
        if (empty($name)) {
            $session->addError($this->__('Please enter the name of wishlist.'));
            $this->_redirect('*/');
            return;
        }
        
        $tab = Mage::getModel('multiplewishlist/tab')->getTab(
                $this->_getWishlist(), - 1, $name);
        
        if ($tab->getId()) {
            Mage::getModel('core/session')->setWishlistActiveTabId(
                    $tab->getId());
        } else {
            $session->addError($this->__('Unable to add wishlist.'));
        }
        
        $this->_redirect('*', 
                array(
                        'wishlist_id' => $this->_getWishlist()
                            ->getId()
                ));
    }

    public function updateAction ()
    {
        $tab_id = $this->getTabId();
        
        Mage::getModel('core/session')->setWishlistActiveTabId($tab_id);
        
        parent::updateAction();
    }

    public function deleteTabAction ()
    {
        if (! $this->_getWishlist() ||
                 ! Mage::helper('multiplewishlist')->isEnabled()) {
            return $this->norouteAction();
        }
        
        $tab_id = $this->getTabId();
        
        if ($tab_id != 0 && ($tab = Mage::getSingleton('multiplewishlist/tab')->checkTabExistsById(
                $this->_getWishlist()
                    ->getId(), $tab_id))) {
            $collection = Mage::getSingleton('multiplewishlist/tab')->getWishlistItemCollection(
                    NULL, $tab_id);
            
            foreach ($collection as $item) {
                $bitem = Mage::getSingleton('multiplewishlist/item')->checkItemExists(
                        $this->_getWishlist()
                            ->getId(), $item->getId());
                
                if ($bitem) {
                    $bitem->delete();
                }
                
                $item->delete();
            }
            
            $tab->delete();
        }
        
        $this->_redirect('*', 
                array(
                        'wishlist_id' => $this->_getWishlist()
                            ->getId()
                ));
    }

    public function moveItemAction ()
    {
        if (! $this->_getWishlist() ||
                 ! Mage::helper('multiplewishlist')->isEnabled()) {
            return $this->norouteAction();
        }
        
        $session = Mage::getSingleton('customer/session');
        
        $itemsIds = Mage::app()->getRequest()->getParam('item', array());
        
        if (count($itemsIds) == 0) {
            $session->addError($this->__('Please select items to move.'));
            $this->_redirect('*/');
            return;
        }
        
        $tabId = (int) Mage::app()->getRequest()->getParam('moveto', NULL);
        
        if (is_null($tabId)) {
            $session->addError(
                    $this->__('Please select wishlist to move items.'));
            $this->_redirect('*/');
            return;
        }
        
        try {
            $result = Mage::getModel('multiplewishlist/item')->moveItems(
                    $this->_getWishlist(), $itemsIds, $tabId);
            
            $message = $this->__(
                    '%1$s items has been moved to wishlist "%2$s".', 
                    count($itemsIds), $result['tab']->getWishlistName());
            $session->addSuccess($message);
            
            Mage::getModel('core/session')->setWishlistActiveTabId($tabId);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            return F;
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e, 
                    Mage::helper('multiplewishlist')->__(
                            'Items could not be moved.'));
            return FALSE;
        }
        
        $this->_redirect('*', 
                array(
                        'wishlist_id' => $this->_getWishlist()
                            ->getId()
                ));
    }

    public function allcartAction ()
    {
        if (! Mage::helper('multiplewishlist')->isEnabled()) {
            return parent::allcartAction();
        }
        
        $wishlist = $this->_getWishlist();
        if (! $wishlist) {
            $this->_forward('noRoute');
            return;
        }
        
        $isOwner = $wishlist->isOwner(
                Mage::getSingleton('customer/session')->getCustomerId());
        
        $messages = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();
        
        $cart = Mage::getSingleton('checkout/cart');
        
        /* BELVG START */
        
        $tab_id = $this->getTabId();
        
        $collection = Mage::getSingleton('multiplewishlist/tab')->getWishlistItemCollection(
                NULL, $tab_id);
        
        /* BELVG END */
        
        $qtys = $this->getRequest()->getParam('qty');
        foreach ($collection as $item) {
            /**
             *
             * @var Mage_Wishlist_Model_Item
             */
            try {
                $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                $item->unsProduct();
                
                // Set qty
                if (isset($qtys[$item->getId()])) {
                    $qty = $this->_processLocalizedQty($qtys[$item->getId()]);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                }
                
                $item->getProduct()->setDisableAddToCart($disableAddToCart);
                // Add to cart
                if ($item->addToCart($cart, $isOwner)) {
                    $addedItems[] = $item->getProduct();
                    
                    /* BELVG START */
                    $bitem = Mage::getSingleton('multiplewishlist/item')->checkItemExists(
                            $wishlist->getId(), $item->getId());
                    
                    if ($bitem) {
                        $bitem->delete();
                    }
                    
                    /* BELVG END */
                }
            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() ==
                         Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $notSalable[] = $item;
                } else {
                    if ($e->getCode() ==
                             Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                        $hasOptions[] = $item;
                    } else {
                        $messages[] = $this->__('%s for "%s".', 
                                trim($e->getMessage(), '.'), 
                                $item->getProduct()
                                    ->getName());
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $messages[] = Mage::helper('wishlist')->__(
                        'Cannot add the item to shopping cart.');
            }
        }
        
        if ($isOwner) {
            $indexUrl = Mage::helper('wishlist')->getListUrl($wishlist->getId());
        } else {
            $indexUrl = Mage::getUrl('wishlist/shared', 
                    array(
                            'code' => $wishlist->getSharingCode()
                    ));
        }
        
        if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
            $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
        } else {
            if ($this->_getRefererUrl()) {
                $redirectUrl = $this->_getRefererUrl();
            } else {
                $redirectUrl = $indexUrl;
            }
        }
        
        if ($notSalable) {
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            
            $messages[] = Mage::helper('wishlist')->__(
                    'Unable to add the following product(s) to shopping cart: %s.', 
                    join(', ', $products));
        }
        
        if ($hasOptions) {
            $products = array();
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            
            $messages[] = Mage::helper('wishlist')->__(
                    'Product(s) %s have required options. Each of them can be added to cart separately only.', 
                    join(', ', $products));
        }
        
        if ($messages) {
            $isMessageSole = (count($messages) == 1);
            if ($isMessageSole && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                if ($isOwner) {
                    $item->delete();
                }
                
                $redirectUrl = $item->getProductUrl();
            } else {
                $wishlistSession = Mage::getSingleton('wishlist/session');
                foreach ($messages as $message) {
                    $wishlistSession->addError($message);
                }
                
                $redirectUrl = $indexUrl;
            }
        }
        
        if ($addedItems) {
            // save wishlist model for setting date of last update
            try {
                $wishlist->save();
            } catch (Exception $e) {
                Mage::getSingleton('wishlist/session')->addError(
                        $this->__('Cannot update wishlist'));
                $redirectUrl = $indexUrl;
            }
            
            $products = array();
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }
            
            Mage::getSingleton('checkout/session')->addSuccess(
                    Mage::helper('wishlist')->__(
                            '%d product(s) have been added to shopping cart: %s.', 
                            count($addedItems), join(', ', $products)));
        }
        
        // save cart and collect totals
        $cart->save()
            ->getQuote()
            ->collectTotals();
        
        Mage::helper('wishlist')->calculate();
        
        $this->_redirectUrl($redirectUrl);
    }

    public function fromcartAction ()
    {
        if (! Mage::helper('multiplewishlist')->isEnabled()) {
            return parent::allcartAction();
        }
        
        $wishlist = $this->_getWishlist();
        if (! $wishlist) {
            return $this->norouteAction();
        }
        
        $itemId = (int) $this->getRequest()->getParam('item');
        
        /* @var Mage_Checkout_Model_Cart $cart */
        $cart = Mage::getSingleton('checkout/cart');
        $session = Mage::getSingleton('checkout/session');
        
        try {
            $item = $cart->getQuote()->getItemById($itemId);
            if (! $item) {
                Mage::throwException(
                        Mage::helper('wishlist')->__(
                                "Requested cart item doesn't exist"));
            }
            
            $productId = $item->getProductId();
            $buyRequest = $item->getBuyRequest();
            
            /* BELVG START */
            
            $wishItem = $wishlist->addNewItem($productId, $buyRequest);
            
            if (! is_string($wishItem)) {
                $wishlist_tab_id = (int) Mage::app()->getRequest()->getParam(
                        'tab_id');
                $wishlist_tab_name = Mage::app()->getRequest()->getParam(
                        'wishlist_name');
                
                if (! is_null($wishlist_tab_id) && $wishlist_tab_id != 0) {
                    $tab = Mage::getModel('multiplewishlist/tab')->getTab(
                            $wishlist, $wishlist_tab_id, $wishlist_tab_name);
                    if ($tab->getId()) {
                        Mage::getModel('multiplewishlist/item')->getItem(
                                $wishlist, $tab, $wishItem);
                        Mage::getModel('core/session')->setWishlistActiveTabId(
                                $tab->getId());
                    }
                }
            }
            
            /* BELVG END */
            
            $productIds[] = $productId;
            $cart->getQuote()->removeItem($itemId);
            $cart->save();
            Mage::helper('wishlist')->calculate();
            $productName = Mage::helper('core')->escapeHtml(
                    $item->getProduct()
                        ->getName());
            // $wishlistName =
            // Mage::helper('core')->escapeHtml($wishlist->getName());
            
            /* BELVG START */
            
            $wishlistName = $wishlist_tab_id && $tab ? $tab->getWishlistName() : Mage::helper(
                    'core')->escapeHtml($wishlist->getName());
            
            /* BELVG END */
            
            $session->addSuccess(
                    Mage::helper('wishlist')->__(
                            "%s has been moved to wishlist %s", $productName, 
                            $wishlistName));
            $wishlist->save();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, 
                    Mage::helper('wishlist')->__('Cannot move item to wishlist'));
        }
        
        return $this->_redirectUrl(Mage::helper('checkout/cart')->getCartUrl());
    }

    public function updateItemOptionsAction ()
    {
        if (! Mage::helper('multiplewishlist')->isEnabled()) {
            return parent::updateItemOptionsAction();
        }
        
        $session = Mage::getSingleton('customer/session');
        $wishlist = $this->_getWishlist();
        if (! $wishlist) {
            $this->_redirect('*/');
            return;
        }
        
        $productId = (int) $this->getRequest()->getParam('product');
        if (! $productId) {
            $this->_redirect('*/');
            return;
        }
        
        $product = Mage::getModel('catalog/product')->load($productId);
        if (! $product->getId() || ! $product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_redirect('*/');
            return;
        }
        
        try {
            $id = (int) $this->getRequest()->getParam('id');
            $buyRequest = new Varien_Object($this->getRequest()->getParams());
            
            /* BELVG START */
            
            $item_old = Mage::getModel('multiplewishlist/item')->checkItemExists(
                    $wishlist->getId(), 
                    $wishlist->getItem($id)
                        ->getId());
            
            /* BELVG END */
            
            $wishlist->updateItem($id, $buyRequest)->save();
            
            Mage::helper('wishlist')->calculate();
            Mage::dispatchEvent('wishlist_update_item', 
                    array(
                            'wishlist' => $wishlist,
                            'product' => $product,
                            'item' => $wishlist->getItem($id)
                    ));
            
            Mage::helper('wishlist')->calculate();
            
            /* BELVG START */
            
            if ($item_old) {
                $tab = Mage::getModel('multiplewishlist/tab')->checkTabExistsById(
                        $wishlist->getId(), $item_old->getWishlistTabId());
                
                if ($tab->getId()) {
                    Mage::getModel('core/session')->setWishlistActiveTabId(
                            $tab->getId());
                    
                    $item = Mage::getResourceModel('wishlist/item_collection')->addWishlistFilter(
                            $wishlist)
                        ->addStoreFilter($wishlist->getSharedStoreIds())
                        ->setVisibilityFilter()
                        ->addFieldToFilter('product_id', $product->getId())
                        ->getFirstItem();
                    
                    Mage::getModel('multiplewishlist/item')->getItem($wishlist, 
                            $tab, $item);
                }
                
                if ($item_old) {
                    $item_old->delete();
                }
            }
            
            /* BELVG END */
            
            $message = $this->__('%1$s has been updated in your wishlist.', 
                    $product->getName());
            $session->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addError(
                    $this->__('An error occurred while updating wishlist.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*');
    }
}
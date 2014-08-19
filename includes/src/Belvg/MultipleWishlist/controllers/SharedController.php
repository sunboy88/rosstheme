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

require_once 'Mage/Wishlist/controllers/SharedController.php';

class Belvg_MultipleWishlist_SharedController extends Mage_Wishlist_SharedController
{

    public function getTabId ()
    {
        return (int) Mage::app()->getRequest()->getParam('tab-id');
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
        Mage::getModel('core/session')->setWishlistActiveTabId($tab_id);
        $collection = Mage::getSingleton('multiplewishlist/tab')->getWishlistItemCollection(
                $wishlist->getId(), $tab_id);
        
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
}
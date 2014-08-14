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


class AW_Collpur_Model_Observer extends Mage_Core_Block_Template {

    public function prepareCart($observer) {

        $buyRequest = $observer->getBuyRequest();
        $request = Mage::app()->getRequest();
        //var_dump($buyRequest->getData('qty')); die;
        if ($buyRequest && $dealId = $request->getParam(AW_Collpur_Block_Dealview::DEAL_PARAM)) {
            $deal = Mage::getModel('collpur/deal')->load((int) $dealId);
            /* First of all we shoud validate that product is actually deal */
            if ($deal->getProductId() != $request->getParam(AW_Collpur_Block_Dealview::PRODUCT_PARAM)) {
                throw new Mage_Checkout_Exception();
            }

            /* Validate not only qty adding at the moment - currently is one, but also qty of deal already in cart */
            $qty = 0;
            $cartItems = Mage::getModel('checkout/cart')->getItems();
            foreach ($cartItems as $item) {
                $itemBuyRequest = Mage::helper('collpur')->getBuyRequest($item, true);
                if ($itemBuyRequest->getDealId()) {
                    if ($itemBuyRequest->getDealId() == $dealId) {
                        $qty += $itemBuyRequest->getQty();
                    }
                }
            }

            $qty += $buyRequest->getData('qty');
            //$deal = Mage::getModel('collpur/deal')->load($dealId);
            if ($deal->isAvailable()) {
                if ($deal->qtyIsNotAllowed($qty)) {
                    Mage::getSingleton('checkout/session')->addError($this->__('Deal qty is not available'));
                    throw new Mage_Checkout_Exception($this->__('Deal qty is not available now'));
                }
                $buyRequest->setData('deal_id', $dealId);
                $observer->getProduct()->addCustomOption('aw_collpur_price', Mage::getModel('collpur/deal')->load($dealId)->getPrice());
                $observer->getProduct()->addCustomOption('aw_collpur_dealidentity', $dealId);
            } else {
                Mage::getSingleton('checkout/session')->setRedirectUrl($this->_getRefererUrl());
                throw new Mage_Core_Exception($this->__('Deal is not available now'));
            }
        }
    }

    public function updateCheckoutCartItemsBefore($observer)
    {
        $data = $observer->getInfo();
        foreach ($data as $itemId => $itemInfo) {
            $qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
            $item = $observer->getCart()->getQuote()->getItemById($itemId);
            if (!$qty || !$item) {
                continue;
            }
            $buyRequest = Mage::helper('collpur')->getBuyRequest($item, true);
            if ($buyRequest && $dealId = $buyRequest->getDealId()) {
                $deal = Mage::getModel('collpur/deal')->load($dealId);
                if (!$deal->isAvailable()) {
                    throw new Mage_Core_Exception($this->__('Deal %s is not available now', $deal->getDealName()));
                }
                try {
                    $deal->validateRequestedQty($qty);
                } catch (Mage_Core_Exception $e) {
                    Mage::register('collpur_deals_item_' . $item->getId(), $item->getQty(), true);
                    throw new Mage_Core_Exception($e->getMessage());
                }
            }
        }
        return $this;
    }

    public function updateCheckoutCartItemsAfter($observer) {
        $data = $observer->getInfo();
        foreach ($data as $itemId => $itemInfo) {
            $item = $observer->getCart()->getQuote()->getItemById($itemId);
            if (!$item)
                continue;
            if ($itemQty = Mage::registry('collpur_deals_item_' . $item->getId())) {
                $item->setQty($itemQty);
            }
        }
    }

    public function processMenu($event) {

        if ($availableMenus = AW_Collpur_Helper_Deals::$menus) {

            $availableMenus = (array) array_pop($availableMenus);
            $processedMenus = array();
            $menus = $event->getMenu()->getData();

            foreach ($menus as $menu) {
                if (in_array($menu['key'], $availableMenus)) {
                    $processedMenus[] = $menu;
                }
            }

            $event->getMenu()->setData($processedMenus);
        }
    }

    public function getFinalPrice($event) {
        $product = $event->getProduct();
        if (!$product->getCustomOption('aw_collpur_price') || !$product->getCustomOption('aw_collpur_price')->getValue())
            return;
        $product->setFinalPrice($product->getCustomOption('aw_collpur_price')->getValue());
    }

    public function orderSaveAfter($observer) {

        $order = $observer->getOrder();
        $items = $order->getAllItems();
        foreach ($items as $item) {
            // Continue with iteration if its a child
            if ($item->getParentItem()) {
                continue;
            }
            $buyRequest = Mage::helper('collpur')->getBuyRequest($item); //$this->_getBuyRequest($item);
            if ($buyRequest && $dealId = $buyRequest->getData('deal_id')) {

                $errors = true;
                if (Mage::app()->getRequest()->getControllerName() != 'sales_order_invoice') {
                    $errors = false;
                }
                if (!Mage::getModel('collpur/deal')->load($dealId)->getId()) {
                    return;
                }

                /* Prevent from error if invoice is created for 2 deals
                 * one of items is set to 0 as its already closed and cannot be
                 * incoiced, the second is actually invoiced
                 */
                $request = Mage::app()->getRequest()->getPost();
                if (isset($request['invoice']['items'][$item->getId()])) {
                    if ($request['invoice']['items'][$item->getId()] == 0) {
                        continue;
                    }
                }

                /* order item is deal */
                Mage::getModel('collpur/deal')
                        ->load($dealId)
                        ->processOrderItem($item, $errors);
            }
        }
    }

    public function downloadableLinkSave($observer) {
        if ($observer->getObject() instanceof Mage_Downloadable_Model_Link_Purchased_Item) {
            $link = $observer->getObject();
            $linkOrderItemId = $link->getOrderItemId();
            if ($linkOrderItemId) {
                $purchase = Mage::getModel('collpur/dealpurchases')->loadByOrderItemId($linkOrderItemId);
                $deal = $purchase->loadDeal();

                if ($deal->getId()) {
                    if (!$deal->isClosed() || !$deal->getData('is_success')) {
                        $link->setStatus(Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING);
                    }
                }
            }
        }
    }

    public function prepareRewirtes() {
        if (!Mage::getStoreConfig('advanced/modules_disable_output/AW_Collpur')) {
            $node = Mage::getConfig()->getNode('global/blocks/checkout/rewrite');
            $dnodes = Mage::getConfig()->getNode('global/blocks/checkout/drewrite');

            foreach ($dnodes->children() as $dnode) {
                $node->appendChild($dnode);
            }
        }
    }

    protected function _getRefererUrl() {
        $this->setRequest(Mage::app()->getRequest());
        $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
        if ($url = $this->getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_REFERER_URL)) {
            $refererUrl = $url;
        }
        if ($url = $this->getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_BASE64_URL)) {
            $refererUrl = Mage::helper('core')->urlDecode($url);
        }
        if ($url = $this->getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_URL_ENCODED)) {
            $refererUrl = Mage::helper('core')->urlDecode($url);
        }
        return $refererUrl;
    }

}


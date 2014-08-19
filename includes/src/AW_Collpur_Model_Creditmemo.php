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


class AW_Collpur_Model_Creditmemo extends Mage_Core_Model_Abstract {

    protected $tempData;
    protected $_shippingAmount = 0;

    public function processCreditMemo($purchase) {

        $order = Mage::getModel('sales/order')->load($purchase->getData('order_id'));
        $orderItem = $order->getItemById($purchase->getData('order_item_id'));
        $data['order_id'] = $purchase->getData('order_id');


        $data['creditmemo']['items'][$orderItem->getId()]['qty'] = $purchase->getData('qty_purchased');

        if ($orderItem->getQtyInvoiced() > $orderItem->getQtyRefunded()) {
            $this->_shippingAmount = $this->_getProcessedShippingAmount($purchase, $orderItem);
        }
        $this->_addShippingAmount(&$data);
        $this->tempData = $data;

        $creditmemo = $this->_initCreditmemo();

        if ($creditmemo) {
            if (($creditmemo->getGrandTotal() <= 0) && (!$creditmemo->getAllowZeroGrandTotal())) {
                return;
            }
            $creditmemo->register();
            $this->_saveCreditmemo($creditmemo);
        }
    }

    private function _getProcessedShippingAmount($purchase, $orderItem) {

        $shippingAmount = 0;
        $shippingPerItem = $purchase->getShippingAmount() / $purchase->getQtyOrdered();

        $invShipping = $shippingPerItem * ($orderItem->getQtyInvoiced() - $orderItem->getQtyShipped() - $purchase->getRefundState());
        $invRefund = $shippingPerItem * ($orderItem->getQtyInvoiced() - $orderItem->getQtyRefunded());


        $shippingAmount = min($invShipping, $invRefund);

        if ($shippingAmount < 0) {
            $shippingAmount = 0;
        }

        $possibleRefund = ($purchase->getQtyOrdered() * $shippingPerItem) - $orderItem->getOrder()->getBaseShippingRefunded();

        if ($possibleRefund <= 0 && (int) $orderItem->getQtyRefunded()) {
            $shippingAmount = 0;
        } else if ($possibleRefund <= $shippingAmount) {
            $shippingAmount = $possibleRefund;
        }
        /* Set refund state */
        $purchase->setRefundState($orderItem->getQtyInvoiced() - $orderItem->getQtyShipped())->save();
        return number_format((float) $shippingAmount, 4);
    }

    private function _addShippingAmount($data) {

        $data['shipping_amount'] = $this->_shippingAmount;
        $data['adjustment_positive'] = '0';
        $data['adjustment_negative'] = '0';
    }

    protected function _initCreditmemo() {

        $data = $this->tempData['creditmemo'];
        $orderId = $this->tempData['order_id'];

        $order = Mage::getModel('sales/order')->load($orderId);

        if (!$order->canCreditmemo()) {
            return false;
        }

        if (isset($data['items'])) {
            $savedData = $data['items'];
        } else {
            $savedData = array();
        }

        $qtys = array();
        $backToStock = array();
        foreach ($savedData as $orderItemId => $itemData) {
            if (isset($itemData['qty'])) {
                $qtys[$orderItemId] = $itemData['qty'];
            }
            if (isset($itemData['back_to_stock'])) {
                $backToStock[$orderItemId] = true;
            }
        }
        $data['qtys'] = $qtys;
        $this->_addShippingAmount(&$data);

        $service = Mage::getModel('sales/service_order', $order);
        $creditmemo = $service->prepareCreditmemo($data);

        /**
         * Process back to stock flags
         */
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $orderItem = $creditmemoItem->getOrderItem();
            $parentId = $orderItem->getParentItemId();
            if (isset($backToStock[$orderItem->getId()])) {
                $creditmemoItem->setBackToStock(true);
            } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                $creditmemoItem->setBackToStock(true);
            } elseif (empty($savedData)) {
                $creditmemoItem->setBackToStock(Mage::helper('cataloginventory')->isAutoReturnEnabled());
            } else {
                $creditmemoItem->setBackToStock(false);
            }
        }

        return $creditmemo;
    }

    /**
     * Save creditmemo and related order, invoice in one transaction
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     */
    protected function _saveCreditmemo($creditmemo) {
        $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($creditmemo)
                        ->addObject($creditmemo->getOrder());
        if ($creditmemo->getInvoice()) {
            $transactionSave->addObject($creditmemo->getInvoice());
        }
        $transactionSave->save();

        return $this;
    }

}
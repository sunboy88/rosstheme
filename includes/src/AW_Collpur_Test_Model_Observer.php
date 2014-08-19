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


class AW_Collpur_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case {

    /**
     * Core test method that calls 2 methods from  Mage::getModel('collpur/observer')
     * 1. prepareCart 2. getFinalPrice
     * After prepareCart validation, finalPrice should be changed only for product #17 array(2, 1, 17,20),
     * as the rest ones are not validated with Mage_Core_Exception exceptions
     *
     * @loadFixture testPrepareCart
     * @dataProvider provider__testPrepareCartObserver

     */
    public function testPrepareCartObserver($dealId, $qty, $productId, $expectedPrice) {

        $deal = Mage::getModel('collpur/deal')->load($dealId);
        /* set deal id in request */
        Mage::app()->getRequest()->setParam('deal_id', $dealId);
        Mage::app()->getRequest()->setParam('product', $productId);
        $observer = new Varien_Object();
        $buyRequest = new Varien_Object();
        $product = Mage::getModel('catalog/product')->load($productId);
        $buyRequest->setQty($qty);
        $observer->setBuyRequest($buyRequest);
        $observer->setProduct($product);

        if ($dealId == 1 || $dealId == 4) {
            $this->setExpectedException('Mage_Core_Exception');
        }

        if ($dealId == 5) {
            $this->setExpectedException('Mage_Checkout_Exception');
        }

        Mage::getModel('collpur/observer')->prepareCart($observer);
        Mage::getModel('collpur/observer')->getFinalPrice($observer);


        $this->assertEquals($observer->getProduct()->getFinalPrice(), $expectedPrice);
    }

    public function provider__testPrepareCartObserver() {

        return array(
            array(1, 1, 16, ''),
            array(2, 1, 17, 20),
            array(4, 100, 17, ''),
            array(5, 1, 17, '')
        );
    }

    
    /**
     * 
     * @loadFixture testPrepareCart
     * @dataProvider provider__testPrepareCartObserverQty
     * The main goul of this test is to check if the error will be araised if items are added one by one
     *
     */

    public function testPrepareCartObserverQty($dealId, $qty, $productId, $expectedPrice,$infoBuyRequest,$count,$uid) {

        $deal = Mage::getModel('collpur/deal')->load($dealId);
        /* set deal id in request */
        Mage::app()->getRequest()->setParam('deal_id', $dealId);
        Mage::app()->getRequest()->setParam('product', $productId);
        $observer = new Varien_Object();
        $buyRequest = new Varien_Object();
        $product = Mage::getModel('catalog/product')->load($productId);
        $buyRequest->setQty($qty);
        $observer->setBuyRequest($buyRequest);
        $observer->setProduct($product);

        /* set quote id and replace existing one in session */
        $quote = $this->getModelMock('sales/quote');

        $quoteItemsCollection = array();

           for($i=0;$i<$count;$i++) {
                 $item = $this->getModelMock('sales/quote_item');
                 $optionByCode = new Varien_Object();
                 $optionByCode->setValue($infoBuyRequest);
                 $item->expects($this->any())->method('getOptionByCode')->will($this->returnValue($optionByCode));
                 $item->expects($this->any())->method('getQty')->will($this->returnValue(1));
                 $quoteItemsCollection[] = $item;
            }

        $quote->expects($this->any())->method('getId')->will($this->returnValue(1));
        $quote->expects($this->any())->method('getItemsCollection')->will($this->returnValue($quoteItemsCollection));
        Mage::getSingleton('checkout/session')->replaceQuote($quote);

            if($uid == '002') {
                 $this->setExpectedException('Mage_Checkout_Exception');
            }
 
        Mage::getModel('collpur/observer')->prepareCart($observer);

    }

     public function provider__testPrepareCartObserverQty() {

        return array(
            array(2, 1, 17, 20, 'a:3:{s:7:"product";i:156;s:7:"deal_id";i:2;s:3:"qty";i:1;}',10,'001'),
            array(2, 1, 17, 20, 'a:3:{s:7:"product";i:156;s:7:"deal_id";i:2;s:3:"qty";i:1;}',20,'002'),
        );
    }

    /**
     *  @test
     *  Simple coverage just to make sure function is present
     *
     */
    public function testProcessMenu() {

        $menus = new Varien_Object(array(1, 2, 3));
        $event = new Varien_Object();
        $event->setMenu($menus);
        AW_Collpur_Helper_Deals::$menus = array(1, 2, 3);

        Mage::getModel('collpur/observer')->processMenu($event);
    }

    /**
     * @test
     * @loadFixture testGetProduct
     * @dataProvider provider__testOrderSaveAfter
     *
     *  1. OrderSaveAfter
     *  2.  Mage::getModel('collpur/deal')
      ->load($dealId)
      ->processOrderItem($item);
     *  3. AW_Collpur_Model_Dealpurchases->connectWithFreeCoupons
     *
     * This the most important functions that should be tested
     * 
     */
    public function testOrderSaveAfter($data) {

        $orderMock = $this->getModelMock('sales/order', array('getAllItems'));

        /* Prepare array of fake sales order items */
        $orderItems = array();
        for ($i = 0; $i < 3; $i++) {
            $salesOrderMock = $this->getModelMock('sales/order_item', array('getProductOptionByCode', 'getId'));
            $itemData = new Varien_Object();
            $itemData->setDealId($data['dealId']);

            /* Prepare fake order for orderItem */
            $orderItem = new Varien_Object();
            $orderItem->setId($data['dealId']);
            $orderItem->setCustomerName('Test order customer');
            $orderItem->setCustomerId($data['dealId']);
            /*             * ********************************* */
            $salesOrderMock->expects($this->any())->method('getProductOptionByCode')->will($this->returnValue($itemData));
            $salesOrderMock->expects($this->any())->method('getId')->will($this->returnValue($data['dealId']));

            $salesOrderMock->setOrder($orderItem);

            $salesOrderMock->setData('qty_invoiced', $data['qtyToAdd']);
            $salesOrderMock->setData('qty_refunded', 0);

            $orderItems[] = $salesOrderMock;
        }

        $orderMock->expects($this->any())->method('getAllItems')->will($this->returnValue($orderItems));

        $observer = new Varien_Object();
        $observer->setOrder($orderMock);

        Mage::app()->getRequest()->setControllerName('sales_order_invoice');

        $exception = false;
        if ($data['uid'] == '003' || $data['uid'] == '001' || $data['uid'] == '004') {
            $this->setExpectedException('Mage_Core_Exception');
            $exception = true;
        }

        Mage::getModel('collpur/observer')->orderSaveAfter($observer);


        /*
         * If there was no exceiption during call of
         * Mage::getModel('collpur/deal')
          ->load($dealId)
          ->processOrderItem($item);
         * proceed with test
         *
         */
        if (!$exception) {
            /* Check that number of purchases generated correct */
            $purchasesCount = Mage::getResourceModel('collpur/dealpurchases_collection')->count();
            $this->assertEquals($purchasesCount, $data['qtyToAdd'], 'Incorrect purchases collection count');
            /*  Check that correct number of coupons were generated */
            $couponsCount = Mage::getResourceModel('collpur/coupon_collection')->count();
            $this->assertEquals($couponsCount, $data['qtyToAdd'], 'Incorrect coupons collection count');
        }
    }

    /* See testGetProduct fixture for more info */

    public function provider__testOrderSaveAfter() {

        return array(
            array(
                array('dealId' => 1, 'qtyToAdd' => 5, 'maxPurch' => 1000, 'uid' => '001') // Deal #1 is not available an Error shoud be generated
            ),
            array(
                array('dealId' => 2, 'qtyToAdd' => 2, 'maxPurch' => 1000, 'uid' => '002') // Deal #2 is ok
            ),
            array(
                array('dealId' => 2, 'qtyToAdd' => 2000, 'maxPurch' => 1000, 'uid' => '003') // Deal #3 purchase exeeds max allowed purchases => Error
            ),
            array(
                array('dealId' => 2, 'qtyToAdd' => 16, 'maxPurch' => 20, 'uid' => '004') // Deal #4 qty to Reach deal is exeeded => auto close === true => Error
            ),
            array(
                array('dealId' => 5, 'qtyToAdd' => 2, 'maxPurch' => 20, 'uid' => '005') // Deal #5 is OK
            ),
        );
    }

    /**
     * @test
     * @loadFixture downloadableLinks
     * @dataProvider provider__testDownloadableLinkSave
     * 
     */
    public function testDownloadableLinkSave($id, $exp) {

        $fakeLink = AW_Collpur_Test_Model_Mocks_Linkitem::instance();
        $fakeLink->setOrderItemId($id);
        $observer = new Varien_Object();
        $observer->setObject($fakeLink);

        Mage::getModel('collpur/observer')->downloadableLinkSave($observer);

        $this->assertEquals($fakeLink->getStatus(), $exp);
    }

    public function provider__testDownloadableLinkSave() {

        return array(
            array(1, null), // Deal is closed and active --> don't touch links status
            array(2, Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING), // Deal is not closed set to pending
            array(3, Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING), // Deal is closed but not successed --> set to pending
            array(15555, null) // No such deal, no status
        );
    }

}
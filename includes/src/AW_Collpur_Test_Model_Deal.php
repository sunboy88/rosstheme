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


class AW_Collpur_Test_Model_Deal extends EcomDev_PHPUnit_Test_Case {

    protected $_itemQtyInvoiced;
    protected $_itemQtyRefunded;

    public function setup() {
        AW_Collpur_Test_Model_Mocks_Foreignresetter::dropForeignKeys();
        parent::setup();
    }

    private function _getDealModel() {

        return Mage::getModel('collpur/deal');
    }

    /**
     * @dataProvider dataProvider
     * @loadFixture
     * @loadExpectation
     */
    public function testCheckSuccess($dealId) {
        $deal = Mage::getModel('collpur/deal')->load($dealId);
        $this->assertEquals($deal->getData('is_success'), $this->expected('deal-' . $dealId)->getData('is_success'));
    }

    public function getDataCallback($method) {
        if ($method == 'qty_invoiced')
            return $this->_itemQtyInvoiced;
        if ($method == 'qty_refunded')
            return $this->_itemQtyRefunded;
    }

    protected function _prepareOrderItemMock($orderItemId) {
        $orderMock = $this->getModelMock('sales/order', array('getCustomerName'));
        $orderMock
                ->expects($this->any())
                ->method('getCustomerName')
                ->will($this->returnValue('Vasya Pypkin'));

        $orderItemMock = $this->getModelMock('sales/order_item', array('getId', 'getOrder', 'getData'));
        $orderItemMock
                ->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($orderItemId));
        $orderItemMock
                ->expects($this->any())
                ->method('getOrder')
                ->will($this->returnValue($orderMock));
        $orderItemMock
                ->expects($this->any())
                ->method('getData')
                ->will($this->returnCallback(array($this, 'getDataCallback')));

        return $orderItemMock;
    }

    /**
     * 
     * @test
     * @loadFixture successedDeals
     * @loadFixture coupons
     * @loadFixture quote
     * @loadFixture quoteItem
     * @loadFixture quoteAddress
     * @loadFixture successedPurchases
     * @loadFixture sales_order
     * @loadFixture sales_order_item 
     * @dataProvider provider__calculateShippingAmount
     * 
     */
    public function calculateShippingAmount($data) {

        $order = Mage::getModel('sales/order')->load($data['orderId']);

        foreach($order->getAllItems() as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $item->setProduct($product);
            $this->assertEquals(Mage::getModel('collpur/deal')->calculateShippingAmount($item),$data['result']);
        }
 
    }

    public function provider__calculateShippingAmount() {
        
        return array(  
            array(array('orderId'=>4,'result'=>10))
        );        
    }

    /**
     * @dataProvider dataProvider
     * @loadFixture
     * @loadExpectation
     */
    public function testProcessOrderItem($dealId, $orderItemId, $qtyInvoiced, $qtyRefunded) {
        $this->_itemQtyInvoiced = $qtyInvoiced;
        $this->_itemQtyRefunded = $qtyRefunded;
        $exMessage = null;
        try {
            Mage::getModel('collpur/deal')
                    ->load($dealId)
                    ->processOrderItem($this->_prepareOrderItemMock($orderItemId));

            $readyDeal = Mage::getModel('collpur/deal')->load($dealId);
            $this->assertEquals($readyDeal->getPurchasesCount(), $this->expected($dealId . '-' . $orderItemId . '-' . $qtyInvoiced . '-' . $qtyRefunded)->getPurchasesCount());
        } catch (Mage_Core_Exception $ex) {
            $exMessage = $ex->getMessage();
        }

        $this->assertEquals($exMessage, $this->expected($dealId . '-' . $orderItemId . '-' . $qtyInvoiced . '-' . $qtyRefunded)->getExceptionMessage());
    }

    /**
     * @loadFixture
     * @loadFixture inventoryStockItem
     * @loadFixture catalog_product_website
     * @dataProvider provider__testGetMenuSize
     * The main goul here is to test ACTIVE DEALS SELECT WITH BASE FILTERS APPLIED ON PRODUCT
     * Default settings for liked product:
     * 1. Website id - 1
     * 2. Stock status for website 1 is - 1
     * 3. Store id for deal with product id 16 is 1
     *
     */
    public function testGetMenuSize($data) {

        /* Update status attribute for specific store id */
        if ($data['update_status'] == 1) {
            Mage::getSingleton('catalog/product_action')->updateAttributes(array($data['product_id']), array('status' => $data['status']), $data['store_id']);
        } else {
            Mage::getSingleton('catalog/product_action')->updateAttributes(array($data['product_id']), array('status' => 0), $data['store_id']);
        }

        $this->_initTestGetMenuSizeEnv($data);
        $sectionSize = $this->_getDealModel()->getMenuSize($data['section']);
        $this->assertEquals($sectionSize, $data['expectedcount'], $data['errorMessage']);
    }

    private function _initTestGetMenuSizeEnv($data) {
        Mage::app()->getWebsite()->setId($data['website_id']);
        Mage::app()->getStore()->setId($data['store_id']);
    }

    public function provider__testGetMenuSize() {

        return array(
            array(
                array(
                    'section' => AW_Collpur_Helper_Deals::RUNNING,
                    'website_id' => 1,
                    'store_id' => 1,
                    'update_status' => 1,
                    'product_id' => 16,
                    'status' => 1,
                    'expectedcount' => 1,
                    'uid' => '001',
                    'errorMessage' => ''
                )
            ),
            /* Here linked product is disabled for store 1, so deal shoud not be visible */
            array(
                array(
                    'section' => AW_Collpur_Helper_Deals::RUNNING,
                    'website_id' => 1,
                    'store_id' => 1,
                    'update_status' => 1,
                    'product_id' => 16,
                    'status' => 0,
                    'expectedcount' => 0,
                    'uid' => '002',
                    'errorMessage' => 'Linked product status was set to 0 for store  1, and deal shoud not be visible, but it is!'
                )
            ),
            /* Here we check website filter. IF linked product and deal website visibility differs */
            array(
                array(
                    'section' => AW_Collpur_Helper_Deals::RUNNING,
                    'website_id' => 2,
                    'store_id' => 1,
                    'update_status' => 1,
                    'product_id' => 16,
                    'status' => 1,
                    'expectedcount' => 0,
                    'uid' => '002',
                    'errorMessage' => 'Linked product website was set to 2, and deal shoud not be visible, but it is!'
                )
            ),
            array(
                array(
                    'section' => AW_Collpur_Helper_Deals::RUNNING,
                    'website_id' => 2,
                    'store_id' => 2, // store id differes from default 1 != 2
                    'update_status' => 1,
                    'product_id' => 16,
                    'status' => 1,
                    'expectedcount' => 0,
                    'uid' => '002',
                    'errorMessage' => 'Linked product store id was set to 2, and deal shoud not be visible, but it is!'
                )
            ),
        );
    }

    /**
     * @loadFixture
     * @loadFixture inventoryStockItem
     * @loadFixture catalog_product_website
     * @dataProvider provider__testGetMenuSizeUpcoming
     * The main goul here is to test UPCOMING DEALS FILTER
     * DEALS 5 and 6 are considerred to be upcoming
     * #6 Max amount filter is not taken into consideration
     * #5 is itself in future
     *
     */
    public function testGetMenuSizeUpcoming($data) {
        /* Update status attribute for specific store id */
        if ($data['update_status'] == 1) {
            Mage::getSingleton('catalog/product_action')->updateAttributes(array($data['product_id']), array('status' => $data['status']), $data['store_id']);
        } else {
            Mage::getSingleton('catalog/product_action')->updateAttributes(array($data['product_id']), array('status' => 0), $data['store_id']);
        }

        $this->_initTestGetMenuSizeEnv($data);
        $sectionSize = $this->_getDealModel()->getMenuSize($data['section']);
        $this->assertEquals($sectionSize, $data['expectedcount'], $data['errorMessage']);
        $futureCollection = Mage::getModel('collpur/deal')->getCollection()->addIsActiveFilter()->getFutureDeals()->setOrder('id ASC')->getAllIds();
        $futureIds = implode(",", $futureCollection);

        if ($futureIds !== $data['expectedIds']) {
            $this->fail('Incorrect ids of future deals should be ' . $data['expectedIds']);
        }
    }

    public function provider__testGetMenuSizeUpcoming() {

        return array(
            array(
                array(
                    'section' => AW_Collpur_Helper_Deals::NOT_RUNNING,
                    'website_id' => 1,
                    'store_id' => 1,
                    'update_status' => 1,
                    'product_id' => 16,
                    'status' => 1,
                    'expectedcount' => 2,
                    'uid' => '001',
                    'expectedIds' => '5,6',
                    'errorMessage' => ''
                )
            )
        );
    }

    /**
     * @loadFixture
     * @loadFixture inventoryStockItem
     * @loadFixture catalog_product_website
     * @dataProvider provider__testGetMenuSizeClosed
     * The main goul here is to test Closed DEALS FILTER
     * DEAL 4 is closed only
     */
    public function testGetMenuSizeClosed($data) {
        /* Update status attribute for specific store id */
        if ($data['update_status'] == 1) {
            Mage::getSingleton('catalog/product_action')->updateAttributes(array($data['product_id']), array('status' => $data['status']), $data['store_id']);
        } else {
            Mage::getSingleton('catalog/product_action')->updateAttributes(array($data['product_id']), array('status' => 0), $data['store_id']);
        }

        $this->_initTestGetMenuSizeEnv($data);
        $sectionSize = $this->_getDealModel()->getMenuSize($data['section']);

        $this->assertEquals($sectionSize, $data['expectedcount'], $data['errorMessage']);
        $futureCollection = Mage::getModel('collpur/deal')->getCollection()->addIsActiveFilter()->getClosedDeals()->setOrder('id ASC')->getAllIds();
        $futureIds = implode(",", $futureCollection);

        if ($futureIds !== $data['expectedIds']) {
            $this->fail('Incorrect ids of future deals should be ' . $data['expectedIds']);
        }
    }

    public function provider__testGetMenuSizeClosed() {

        return array(
            array(
                array(
                    'section' => AW_Collpur_Helper_Deals::CLOSED,
                    'website_id' => 1,
                    'store_id' => 1,
                    'update_status' => 1,
                    'product_id' => 16,
                    'status' => 1,
                    'expectedcount' => 1,
                    'uid' => '001',
                    'expectedIds' => '4',
                    'errorMessage' => ''
                )
            )
        );
    }

    /**
     * @loadFixture
     * @loadFixture inventoryStockItem
     * @loadFixture catalog_product_website
     * @dataProvider provider__testGetMenuSizeFeatured
     * The main goul here is to test Closed DEALS FILTER
     * 
     */
    public function testGetMenuSizeFeatured($data) {
        /* Update status attribute for specific store id */
        if ($data['update_status'] == 1) {
            Mage::getSingleton('catalog/product_action')->updateAttributes(array($data['product_id']), array('status' => $data['status']), $data['store_id']);
        } else {
            Mage::getSingleton('catalog/product_action')->updateAttributes(array($data['product_id']), array('status' => 0), $data['store_id']);
        }

        $this->_initTestGetMenuSizeEnv($data);
        $sectionSize = $this->_getDealModel()->getMenuSize($data['section']);

        if ($sectionSize !== false) {
            $this->fail("There should not be featured deals");
        }
    }

    public function provider__testGetMenuSizeFeatured() {

        return array(
            array(
                array(
                    'section' => AW_Collpur_Helper_Deals::FEATURED,
                    'website_id' => 1,
                    'store_id' => 1,
                    'update_status' => 1,
                    'product_id' => 16,
                    'status' => 1,
                    'expectedcount' => 1,
                    'uid' => '001',
                    'errorMessage' => ''
                )
            )
        );
    }

    /**
     * 
     * @test
     * @loadFixture
     * @loadFixture inventoryStockItem
     * @loadFixture catalog_product_website
     * @dataProvider provider__isAllowed
     * The main goal is to check weather deal is allowed
     * if specific section is disabled
     * 
     */
      public function isAllowed($data) {          
          $this->_prepareIsAllowedConfig(); 
          foreach($data['deal_ids'] as $deal) {
               $this->assertEquals((int) Mage::getModel('collpur/deal')->load($deal['id'])->isAllowed(),(int) $deal['validation'],$deal['id'].' fail');
          }
      }
      private function _prepareIsAllowedConfig() {
          $config = AW_Collpur_Model_Source_Menus::getMenusArray(false);
          foreach($config as $menu) {
              if($menu['key'] == AW_Collpur_Helper_Deals::RUNNING || $menu['key'] == AW_Collpur_Helper_Deals::NOT_RUNNING) {                  
                  Mage::app()->getStore(0)->setConfig("collpur/{$menu['alias']}/enabled",0);
              }              
          }         
      }
      public function provider__isAllowed() { 
          return array (              
              array(                  
                  array('deal_ids' =>
                      array(
                       array('id'=>1,'validation'=>true),
                       array('id'=>2,'validation'=>false), // This section is closed and deal is running, so err 404
                       array('id'=>3,'validation'=>true),
                       array('id'=>4,'validation'=>true),
                       array('id'=>5,'validation'=>false),
                       array('id'=>6,'validation'=>false)
                      )
                   )
              )
          );
      }

    /**
     * @dataProvider dataProvider
     * @loadFixture
     * @loadExpectation
     */
    public function testMassProcessOrderItem($dealId, $orderItemId, $massQty) {
        $this->_itemQtyInvoiced = $massQty;
        $this->_itemQtyRefunded = 0;
        $exMessage = null;
        try {
            Mage::getModel('collpur/deal')
                    ->load($dealId)
                    ->processOrderItem($this->_prepareOrderItemMock($orderItemId));
        } catch (Mage_Core_Exception $ex) {
            $exMessage = $ex->getMessage();
        }

        $this->assertEquals($exMessage, $this->expected($dealId . '-' . $orderItemId . '-' . $massQty)->getExceptionMessage());

        $readyDeal = Mage::getModel('collpur/deal')->load($dealId);
        $this->assertEquals($readyDeal->getPurchasesCount(), $this->expected($dealId . '-' . $orderItemId . '-' . $massQty)->getPurchasesCount());
        $this->assertEquals($readyDeal->getData('is_success'), $this->expected($dealId . '-' . $orderItemId . '-' . $massQty)->getData('is_success'));
        $this->assertEquals($readyDeal->getData('close_state'), $this->expected($dealId . '-' . $orderItemId . '-' . $massQty)->getData('close_state'));
        $this->assertEquals($readyDeal->getData('purchases_left'), $this->expected($dealId . '-' . $orderItemId . '-' . $massQty)->getData('purchases_left'));
    }

    /**
     * @loadFixture
     * @loadExpectation
     */
    public function testJoinProcesses() {
        $collection =
                        Mage::getModel('collpur/deal')
                        ->getCollection()
                        ->joinProcesses();
        foreach ($collection as $deal) {
            $this->assertEquals($deal->getData('progress'), $this->expected('deal-' . $deal->getId())->getData('progress'));
        }
    }

    /**
     * @loadFixture
     * @dataProvider provider__testCloseFailed
     */
    public function testCloseAsFailed($id, $status) {
        Mage::getModel('collpur/deal')->load($id)->closeAsFailed();
        $this->assertEquals($status, Mage::getModel('collpur/coupon')->load($id)->getStatus());
    }

    public function provider__testCloseFailed() {

        return array(array(1, AW_Collpur_Model_Coupon::STATUS_EXPIRED), array(2, AW_Collpur_Model_Coupon::STATUS_PENDING));
    }

    /**
     * @loadFixture
     * @dataProvider provider__testGetProduct
     */
    public function testGetProduct($id) {

        $deal = Mage::getModel('collpur/deal')->load(1);
        $this->assertEquals(Mage::getModel('catalog/product')->load($id)->getName(), $deal->getProduct()->getName());
    }

    public function provider__testGetProduct() {
        return array(array(16));
    }

    /**
     * @loadFixture testGetProduct
     * @dataProvider provider__testGetProduct
     */
    public function testGetDealName($id) {

        $deal = Mage::getModel('collpur/deal')->load(1);
        $this->assertEquals(Mage::getModel('catalog/product')->load($id)->getName(), $deal->getDealName());
    }

    /**
     * @loadFixture testGetProduct
     * @dataProvider provider__testCollectCouponCodes
     * @loadExpectation
     *
     */
    public function testCollectCouponCodes($id, $uid) {

        $deal = Mage::getModel('collpur/deal')->load($id);
        $this->assertEquals($this->expected($uid)->getCodes(), $deal->collectCouponCodes($id));
    }

    public function provider__testCollectCouponCodes() {
        return array(array(1, '001'));
    }

    /**
     * @loadFixture testGetProduct
     * @dataProvider provider__testCalculateCouponExpireDate
     *
     */
    public function testCalculateCouponExpireDate($dealId, $storeId, $days, $uid) {

        $currentDate = gmdate('Y-m-d h:i:s');

        $deal = Mage::getModel('collpur/deal')->load($dealId);
        $deal->setAvailableTo($currentDate);
        $expirationDate = $deal->calculateCouponExpireDate($dealId, $deal, $storeId, true, true);

        $currentDate = new Zend_Date($currentDate, Zend_Date::ISO_8601);
        $dateAfterStamp = $currentDate->addDay($days)->getTimestamp();
        $expected = Mage::getModel('core/locale')->storeDate($storeId, $dateAfterStamp, true)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $this->assertEquals($expirationDate, $expected);
    }

    public function provider__testCalculateCouponExpireDate() {
        return array(
            array(1, 1, 4, '001')
        );
    }

    /**
     * 
     * @loadFixture testGetProduct
     * @dataProvider provider__testReopen
     *
     */
    public function testReopen($dealId) {

        $deal = Mage::getModel('collpur/deal')->load($dealId);
        $deal->reopen();
        $reopenedDeal = Mage::getModel('collpur/deal')->load($dealId);

        if (!$deal->getIsSuccess()) {
            $this->assertEquals($reopenedDeal->getCloseState(), AW_Collpur_Model_Deal::STATE_OPEN);
            $this->assertEquals($reopenedDeal->getSentBeforeFlag(), 0);
            $this->assertEquals($reopenedDeal->getExpiredFlag(), 0);
            $this->assertEquals($reopenedDeal->getIsSuccessedFlag(), 0);
            $this->assertEquals($reopenedDeal->getQtyToReachDeal(), $reopenedDeal->getPurchasesLeft());
        } else {
            $this->assertEquals($reopenedDeal->getCloseState(), AW_Collpur_Model_Deal::STATE_OPEN);
        }
    }

    public function provider__testReopen() {

        return array(
            array(1),
            array(17)
        );
    }

    /**
     * @loadFixture testGetProduct
     * @dataProvider provider__testSimpleAssertions
     * 
     */
    public function testSimpleAssertions($dealId, $opened, $closed, $featured, $running, $notRunning, $archived, $isAvailable, $progress) {

        $deal = Mage::getModel('collpur/deal')->load($dealId);
        $this->assertEquals($deal->isOpen(), (bool) $opened, "Deal #{$dealId} open state is incorrect");
        $this->assertEquals($deal->isFeatured(), (bool) $featured, "Deal #{$dealId} featured state is incorrect");
        $this->assertEquals($deal->isClosed(), (bool) $closed, "Deal #{$dealId} close state is incorrect");
        $this->assertEquals($deal->isArchived(), (bool) $archived, "Deal #{$dealId} archived state is incorrect");
        $this->assertEquals($deal->isRunning(), (bool) $running, "Deal #{$dealId} running state is incorrect");
        $this->assertEquals($deal->isNotRunning(), (bool) $notRunning, "Deal #{$dealId} notRunning state is incorrect");
        $this->assertEquals($deal->isAvailable(), (bool) $isAvailable, "Deal #{$dealId} isAvailable state is incorrect");
        $this->assertEquals($deal->getProgress(), $progress, "Deal #{$dealId} progress state is incorrect");
    }

    public function provider__testSimpleAssertions() {

        return array(
            /* Deal  id, opened, closed, featured, running, not running, archived, available,        progress */
            array(1, false, true, true, false, false, false, false, AW_Collpur_Model_Source_Progress::PROGRESS_CLOSED),
            array(2, true, false, false, true, false, false, true, AW_Collpur_Model_Source_Progress::PROGRESS_RUNNING),
            array(3, true, false, false, true, false, false, true, AW_Collpur_Model_Source_Progress::PROGRESS_RUNNING),
            array(4, false, false, false, false, false, true, false, AW_Collpur_Model_Source_Progress::PROGRESS_ARCHIVED),
            array(5, true, false, false, false, false, false, false, AW_Collpur_Model_Source_Progress::PROGRESS_EXPIRED),
            array(6, true, false, false, false, true, false, false, AW_Collpur_Model_Source_Progress::PROGRESS_NOT_RUNNING),
        );
    }

    /**
     * @loadFixture testGetProduct     
     */
    public function testFeatured() {

        $this->assertEquals(0, (int) $this->_getDealModel('collpur/deal')->getRandomFeaturedId());
    }

    /**
     * @loadFixture testGetProduct
     * @dataProvider provider__testSimpleActions
     * 
     */
    public function testSimpleActions($dealId) {

        $deal = $this->_getDealModel()->load($dealId);
        $deal->archive();
        $this->assertEquals(AW_Collpur_Model_Deal::STATE_ARCHIVED, $deal->getCloseState());
        $deal->close();
        $this->assertEquals($deal->getCloseState(), AW_Collpur_Model_Deal::STATE_CLOSED);
        $deal->closeAsSuccess();

        /* See  public function testCloseAsFailed($id,$status) */
        //  $deal->closeAsFailed();

        /* See public function testReopen */
        // $deal->reopen();
    }

    public function provider__testSimpleActions() {
        return array(
            array(1)
        );
    }

    /**
     * @test
     * @loadFixture testGetProduct
     * @dataProvider provider__testLoadActiveDealByProduct
     */
    public function testLoadActiveDealByProduct($id) {

        $deal = $this->_getDealModel()->loadActiveDealByProduct(1);
        $this->assertEquals(2, $deal->getId());
    }

    public function provider__testLoadActiveDealByProduct() {
        return array(
            array(1)
        );
    }

    /**
     * @test
     * @loadFixture
     * @loadFixture order
     * @loadFixture orderItem
     * @loadFixture downloadableData
     * @dataProvider provider__testAdditionalProductProcessing
     * This is core function processing downloadable products links statuses
     * 1. IF product is downloadable
     * 2. IF its statue is closed && successed => All of its links shoud be set as available, otherwise shoud be suspended
     */
    public function testAdditionalProductProcessing($data) {

        $product = new Varien_Object();
        $product->setTypeId(Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE);
        /* Create and prepare mock object */
        $dealMock = $this->getModelMock('collpur/deal', array('getProduct', 'isClosed'));
        $dealMock->expects($this->any())->method('getProduct')->will($this->returnValue($product));
        $dealMock->expects($this->any())->method('isClosed')->will($this->returnValue(AW_Collpur_Model_Deal::STATE_CLOSED == $data['close']));
        $dealMock->setId(1);
        $dealMock->setData('is_success', $data['success']);
        /*         * ****************************************************************** */
        $dealMock->additionalProductProcessing();
        $link = Mage::getModel('downloadable/link_purchased_item')->load(1);
        $this->assertEquals($data['status'], $link->getStatus());
    }

    public function provider__testAdditionalProductProcessing() {
        return array(
            // Deal opened and not succecced => pending
            array(array('orderId' => 1, 'close' => AW_Collpur_Model_Deal::STATE_OPEN, 'success' => 0, 'status' => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING)),
            // Deal closed and successed => available
            array(array('orderId' => 1, 'close' => AW_Collpur_Model_Deal::STATE_CLOSED, 'success' => 1, 'status' => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE)),
            // Deal closed, but not successed => pending
            array(array('orderId' => 1, 'close' => AW_Collpur_Model_Deal::STATE_CLOSED, 'success' => 0, 'status' => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING)),
            // Deal successed, but not closed => pending though it is realy impossible
            array(array('orderId' => 1, 'close' => AW_Collpur_Model_Deal::STATE_ARCHIVED, 'success' => 1, 'status' => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING)),
        );
    }

}
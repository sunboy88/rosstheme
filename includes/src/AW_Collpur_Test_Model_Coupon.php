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


class AW_Collpur_Test_Model_Coupon extends EcomDev_PHPUnit_Test_Case {

    protected function tearDown() {
        parent::tearDown();

        $couponTable = Mage::getSingleton('core/resource')->getTableName('collpur/coupon');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->truncate($couponTable);
    }

    /**
     * 
     * @dataProvider dataProvider
     * @loadFixture
     * @loadExpectation
     */
    public function testGenerateCoupon($dealId, $uniqueId) {
        $deal = Mage::getModel('collpur/deal')->load($dealId);

        $generatedCoupon = Mage::getModel('collpur/coupon')->generateCoupon($deal, $uniqueId);
        $associatedPurchase = Mage::getModel('collpur/dealpurchases')->load($generatedCoupon->getData('purchase_id'));

        $this->assertEquals($generatedCoupon->getDealId(), $this->expected($dealId . '-' . $uniqueId)->getDealId());
        $this->assertEquals($associatedPurchase->getId(), $this->expected($dealId . '-' . $uniqueId)->getPurchaseId());
    }

    /**
     *
     * @dataProvider provider__testGenerateCoupons
     * @loadFixture testGenerateCoupon
     * @loadExpectation testGenerateCoupon
     * Simple coverage as testGenerateCoupon function calls generateCoupons function
     */
    public function testGenerateCoupons($data) {
        $deal = Mage::getModel('collpur/deal')->load($data['dealId']);
        Mage::getModel('collpur/coupon')->generateCoupons($deal, $data['coupons']);

        $couponsCount = Mage::getModel('collpur/coupon')->getCollection()->count();
        $this->assertEquals(
                $couponsCount,
                $data['expected'],
                'Incorrect number of coupons have been generated. Shoud be ' . $data['expected'] . ' generated ' . $couponsCount
        );

        $this->dependGetCouponCountForDeal($deal, $data);
    }

    public function dependGetCouponCountForDeal($deal, $data) {

        $count = Mage::getModel('collpur/coupon')->getCouponCountForDeal($deal);
        $this->assertEquals(
                $count,
                $data['expected'],
                'Incorrect coupon cound for deal ' . $deal->getId() . ' Coupons count is ' . $count . 'shoud be ' . $data['expected']
        );
    }

    public function provider__testGenerateCoupons() {

        return array(
            array(
                array('dealId' => 1, 'coupons' => 40, 'expected' => 40)
            )
        );
    }

    /**
     *
     * @dataProvider dataProvider
     * @loadFixture
     * @loadExpectation
     */
    public function testProcessPurchase($orderItemId, $dealId, $customerName, $qtyPurchased, $qtyWithCoupons) {

        $purchase = Mage::getModel('collpur/dealpurchases')
                        ->setData('order_item_id', $orderItemId)
                        ->setData('deal_id', $dealId)
                        ->setData('customer_name', $customerName)
                        ->setData('qty_purchased', $qtyPurchased)
                        ->setData('qty_with_coupons', $qtyWithCoupons)
                        ->save();
        
        $purchase->connectWithFreeCoupons();

        $coupon =
            Mage::getModel('collpur/coupon')
            ->getCollection()
            ->addFieldToFilter('purchase_id', $purchase->getId())
            ->getFirstItem();

        $this->assertEquals($coupon->getStatus(), $this->expected($orderItemId . '-' . $dealId)->getCouponStatus());
    }

}
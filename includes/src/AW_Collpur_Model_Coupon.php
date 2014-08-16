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


class AW_Collpur_Model_Coupon extends Mage_Core_Model_Abstract
{
    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';
    const STATUS_PENDING = 'pending';
    const STATUS_NOT_USED = 'not_used';
    const STATUS_EXPIRED = 'expired';

    public static function generateCoupons($deal, $couponsQty)
    {
        $couponsCount =
            Mage::getModel('collpur/coupon')
                ->getCollection()
                ->addFieldToFilter('deal_id', $deal->getId())
                ->getSize();
        for ($i = $couponsCount; $i < $couponsQty + $couponsCount; $i++)
        {
            $coupon = new self();
            $coupon->generateCoupon($deal, $i);
        }


        if($deal->getIsSuccess() && $deal->getIsSuccessedFlag()) {
            AW_Collpur_Model_Cron::_processSuccessedDeals();
        }

    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('collpur/coupon');
    }

    public function generateCoupon($deal, $uniqueId)
    {
        $this
            ->setDealId($deal->getId())          
            ->setCouponCode($this->_genUqCC($deal))
            ->setStatus(self::STATUS_PENDING)
            ->setCouponDateUpdated(Mage::getModel('core/date')->gmtDate())
            ->save();

        $purchaseWithoutCoupon = Mage::getModel('collpur/dealpurchases')->loadPurchaseWithoutCoupon($deal->getId());
        if ($purchaseWithoutCoupon->getId())
        {
            $purchaseWithoutCoupon->connectWithFreeCoupons();
        }

        /* 
         * Will return new reloaded object, because $purchaseWithoutCoupon->connectWithFreeCoupons() can set purchase_id to currunt coupon,
         * so this property will not be avalable if $this will be returned
         */
        return self::load($this->getId());
    }

    private function _genUqCC($deal) {

        $unique = false;
        do {
            $code = $this->_fetchCouponCode($deal->getCouponPrefix(),$deal);
            if (Mage::getResourceModel('collpur/coupon')->isUnique($code)) {
                $unique = true;
            }
        } while (!$unique);

        return $code;
    }

    private function _fetchCouponCode($prefix,$deal) {

        $params = Mage::app()->getRequest()->getParams();

        $savePrefix = false;
        $saveExpire = false;

        if (isset($params['prefix'])) {
            $savePrefix = true;
            $prefix = trim($params['prefix']);
        }

        if (isset($params['expire'])) {
            if (trim($params['expire']) == '0' || (int) $params['expire'] > 0) {
                $saveExpire = true;
                $expire = trim($params['expire']);
            }
        }

        $letters = range('A', 'Z');
        $numbers = range(0, 9);
        shuffle($letters);
        shuffle($numbers);
        $scope = array_merge(array_slice($numbers, 0, 3), array_slice($letters, 0, 3));
        shuffle($scope);
        $scope = implode("", $scope);
        $prefix = trim($prefix);

        /**
         * Save coupon prefix and enable state on coupons generation
         */
        $deal->setEnableCoupons(1);
        if ($savePrefix)
            $deal->setCouponPrefix($prefix);
        if ($saveExpire)
            $deal->setCouponExpireAfterDays($expire);
        $deal->save();

        if ($prefix)
            return "{$prefix}-{$scope}";
        return $scope;
    }

    public function loadFreeCoupon($dealId)
    {
        $this->getResource()->loadFreeCoupon($this, $dealId);
        return $this;
    }

    public function getDealPurchaseCouponsCount($dealPurchase)
    {
        return
            $this
                ->getCollection()
                ->addFieldToFilter('purchase_id', $dealPurchase->getId())
                ->addFieldToFilter('status', array(
                    "OR" => array(
                        "0" => self::STATUS_NOT_USED,
                        "1" => self::STATUS_USED,
                        "2" => self::STATUS_EXPIRED
                        )
                    ))
                ->getSize();

    }

    public function getCouponCountForDeal($deal)
    {
        return
            $this
                ->getCollection()
                ->addFieldToFilter('deal_id', $deal->getId())
                ->getSize();
    }

}
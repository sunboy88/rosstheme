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


class AW_Collpur_Model_Dealpurchases extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('collpur/dealpurchases');
    }

    public function connectWithFreeCoupons()
    {  
        $qtyWithCoupons = Mage::getModel('collpur/coupon')->getDealPurchaseCouponsCount($this);
 
        for ($i = 0; $i < $this->getData('qty_purchased') - $qtyWithCoupons; $i++)
        { 
            $freeCoupon = Mage::getModel('collpur/coupon')->loadFreeCoupon($this->getDealId());

            if ($freeCoupon->getId())
            {   
                $freeCoupon
                    ->setData('purchase_id', $this->getId())
                    ->setData('status', AW_Collpur_Model_Coupon::STATUS_NOT_USED)
                    ->save();

            }
        }

        $this
            ->setData('qty_with_coupons', Mage::getModel('collpur/coupon')->getDealPurchaseCouponsCount($this))
            ->save();
        
        return $this;
    }

    public function loadPurchaseWithoutCoupon($dealId)
    {
        $this->getResource()->loadPurchaseWithoutCoupon($this, $dealId);
        return $this;
    }

    public function loadByOrderItemId($orderItemId)
    {
        $this->getResource()->loadByOrderItemId($this, $orderItemId);
        return $this;
    }

    public function loadDeal()
    {
        return Mage::getModel('collpur/deal')->load($this->getData('deal_id'));
    }

    public function refund()
    {
        $order = Mage::getModel('sales/order')->load($this->getData('order_id'));
        $orderItem = $order->getItemById($this->getData('order_item_id'));

        $items = $order->getAllItems();
        foreach ($items as $item) {

            if ($item && $item->getId() == $orderItem->getId())
            {
                Mage::getModel('collpur/creditmemo')->processCreditMemo($this);
            }
        }
        
    }

}
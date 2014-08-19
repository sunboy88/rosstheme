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


class AW_Collpur_Model_Mysql4_Coupon_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('collpur/coupon');


        $this->_map['fields']['statuses'] = "
            
                   CASE
                        WHEN `main_table`.`status`= 'used' THEN 'Used'
                        WHEN `main_table`.`status` = 'pending' AND `deals`.`close_state`= " . AW_Collpur_Model_Deal::STATE_OPEN . " AND deals.available_to < UTC_TIMESTAMP() THEN 'Expired'
                        WHEN `main_table`.`status` = 'pending' AND `deals`.`close_state`= " . AW_Collpur_Model_Deal::STATE_CLOSED . " THEN 'Expired'
                        WHEN `main_table`.`status` = 'not_used' AND IF((`deals`.`coupon_expire_after_days` = 0 OR `deals`.`coupon_expire_after_days` is null) OR `deals`.`available_to` IS NULL,false, UTC_TIMESTAMP() > DATE_SUB(deals.available_to,INTERVAL -`deals`.`coupon_expire_after_days` DAY)) THEN 'Expired'
                        WHEN `main_table`.`status` = 'not_used' THEN 'Not used'
                        WHEN `main_table`.`status` = 'pending' THEN 'Pending'
                        WHEN `main_table`.`status` = 'expired' THEN 'Expired'
                    END
        ";
    }

    public function updateStatus($collection, $value) {
        foreach ($collection as $coupon) {
            $coupon->setStatus($value)
                    ->setCouponDateUpdated(AW_Collpur_Helper_Data::getGmtTimestamp(true,true,false,'toString'))
                    ->save();
        }
    }

    public function deleteCoupons($collection) {
        foreach ($collection as $coupon) {
            $coupon->delete();
        }
    }

    public function setAsExpired($couponId) {

        
       $this->getSelect()->where("deal_id = ?", $couponId)
                        ->where("status = ?",AW_Collpur_Model_Coupon::STATUS_NOT_USED);
 
        foreach ($this as $coupon) {
            $coupon->setStatus(AW_Collpur_Model_Coupon::STATUS_EXPIRED)->save();
        }

        return $this;
    }

    public function joinStatuses() {
        $this->getSelect()
                ->columns(array('statuses' => new Zend_Db_Expr($this->_getMappedField('statuses'))));
 
        return $this;
    }

}
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

class AW_Collpur_Model_Mysql4_Dealpurchases extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('collpur/dealpurchases', 'id');
    }

    public function loadPurchaseWithoutCoupon($dealPurchase, $dealId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('dealpurchases'))
            ->where('deal_id = ?', $dealId)
            ->where('qty_purchased > qty_with_coupons');
        if ($data = $this->_getReadAdapter()->fetchRow($select)) {
            $dealPurchase->addData($data);
        }
        $this->_afterLoad($dealPurchase);
        return $this;
    }

    public function loadByOrderItemId($dealPurchase, $orderItemId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('dealpurchases'))
            ->where('order_item_id = ?', $orderItemId);
        if ($data = $this->_getReadAdapter()->fetchRow($select)) {
            $dealPurchase->addData($data);
        }
        $this->_afterLoad($dealPurchase);
        return $this;
    }

}
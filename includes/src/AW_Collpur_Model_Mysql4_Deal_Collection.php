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


class AW_Collpur_Model_Mysql4_Deal_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public $isNativeCount = false;

    public function _construct()
    {
        parent::_construct();
        $this->_init('collpur/deal');
        $this->_map['fields']['progress'] = "
                case when `main_table`.`close_state`=" . AW_Collpur_Model_Deal::STATE_OPEN . " AND available_from > UTC_TIMESTAMP() then 'not_running'
                     when `main_table`.`close_state`=" . AW_Collpur_Model_Deal::STATE_OPEN . " AND if(available_to is null, true, available_to > UTC_TIMESTAMP()) AND if(available_from is null, true, available_from < UTC_TIMESTAMP()) then 'running'
                     when `main_table`.`close_state`=" . AW_Collpur_Model_Deal::STATE_OPEN . " AND available_to < UTC_TIMESTAMP() then 'expired'
                     when `main_table`.`close_state`=" . AW_Collpur_Model_Deal::STATE_CLOSED . " then 'closed'
                     when `main_table`.`close_state`=" . AW_Collpur_Model_Deal::STATE_ARCHIVED . " then 'archived' end";
    }

    public function joinProcesses()
    {
        $this->getSelect()
            ->columns(array('progress' => new Zend_Db_Expr($this->_getMappedField('progress'))));
        return $this;
    }

    public function joinMaxPurch()
    {
        $this->getSelect()
            ->columns(array('maxpurch' => new Zend_Db_Expr($this->_getMappedField('maximum'))));
        return $this;
    }

    public function addIsActiveFilter()
    {

        $this
            ->getSelect()
            ->where('main_table.is_active = 1')
            ->where('FIND_IN_SET(?, main_table.store_ids) or FIND_IN_SET(0, main_table.store_ids)', Mage::app()->getStore()->getId());
        /*
         * Add product-specific filters: 
         *  1. Product on the same website
         *  2. Product enabled
         */
        $statusTable = $this->getTable('catalog/product') . "_int";
        $statusAttrId = Mage::getResourceModel('catalog/product')->getAttribute('status')->getAttributeId();
        $websiteId = Mage::app()->getWebsite()->getId();
        $storeId = Mage::app()->getStore()->getId();

        $this->getSelect()
            ->joinLeft(array('products' => $this->getTable('catalog/product')), 'main_table.product_id = products.entity_id', array('*'))
            ->join(array('websiteTable' => $this->getTable('catalog/product_website')), "websiteTable.product_id = main_table.product_id AND websiteTable.website_id = '{$websiteId}'", array())
            ->joinLeft(array("statusTableDefault" => $statusTable), "main_table.product_id = statusTableDefault.entity_id AND statusTableDefault.attribute_id = '{$statusAttrId}' AND statusTableDefault.store_id = 0", array('status' => 'IF(statusTable.value_id > 0,statusTable.value,statusTableDefault.value)'))
            ->joinLeft(array("statusTable" => $statusTable), "main_table.product_id = statusTable.entity_id AND statusTable.attribute_id = '{$statusAttrId}' AND statusTable.store_id = '{$storeId}'", array())
            ->where("IF(statusTable.value_id > 0,statusTable.value,statusTableDefault.value) = '1'")
            ->group('main_table.id');

        return $this;
    }

    public function getExpiredFilter()
    {
        $this->getSelect()->where("`main_table`.`close_state`=" . AW_Collpur_Model_Deal::STATE_OPEN . " AND available_to < UTC_TIMESTAMP() AND available_to IS NOT NULL");
        return $this;
    }

    public function getExpiredAfterDaysFilter($daysToAdd)
    {
        $currentZendDate = AW_Collpur_Helper_Data::getGmtTimestamp(true, true, $daysToAdd, false);
        $dateAfterDays = $currentZendDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $this
            ->getSelect()
            ->where('available_to <= ?', $dateAfterDays)
            ->where('available_to IS NOT NULL')
            ->where('sent_before_flag = 0')
            ->order('id ASC');

        return $this;
    }

    /**
     * Successed deals are the onces which have
     * 1. successed flag
     * 2. If they have at least one invoiced purchase with not processed flag
     * 3. Purchase flag sttus 2 means that at the moment of purchase notifications were disabled for the purchase, so
     *    we join this status only once, in case administrator has not received email notifications i.e deal is_successed flag itself
     *    is set to 0
     * @return $obj
     */
    public function getSuccessedDeals()
    {
        $this->getSelect()
            ->where("`main_table`.`is_success` = 1")
            ->joinLeft(array("purchases" => $this->getTable('collpur/dealpurchases')), "main_table.id = purchases.deal_id", array())
            ->where("purchases.is_successed_flag = 0")
            ->orWhere("if(`main_table`.`is_successed_flag` = 0,purchases.is_successed_flag = 2,purchases.is_successed_flag IS NULL)")
            ->where("purchases.qty_purchased > 0")
            ->group("purchases.deal_id");

        return $this;
    }

    public function addNotProcessedFlag()
    {
        $this->getSelect()->where("`main_table`.`expired_flag` = 0");
        return $this;
    }

    public function addFeaturedFilter()
    {
        $this->getSelect()
            ->where('`main_table`.`is_featured`= 1');
        return $this;
    }

    public function getClosedDeals()
    {
        $this->getSelect()
            ->where("`main_table`.`close_state`=" . AW_Collpur_Model_Deal::STATE_CLOSED)
            ->order('id DESC');
        return $this;
    }

    public function getFutureDeals()
    {
        $this->getSelect()
            ->where("`main_table`.`close_state`=" . AW_Collpur_Model_Deal::STATE_OPEN . " AND available_from > UTC_TIMESTAMP()")
            ->order('id DESC');
        return $this;
    }

    public function getActiveDeals()
    {
        $websiteId = Mage::app()->getWebsite()->getId();

        $this->getSelect()
            ->joinLeft(array('purcht' => $this->getTable('collpur/dealpurchases')), 'main_table.id = purcht.deal_id', array('purchcount' => new Zend_Db_Expr('SUM(purcht.qty_purchased)')))
            ->join(array('stockt' => $this->getTable('cataloginventory/stock_status')), "main_table.product_id = stockt.product_id AND stockt.stock_status = '1' AND stockt.website_id = '{$websiteId}'", array('stock_status'))
            ->where("`main_table`.`close_state`=" . AW_Collpur_Model_Deal::STATE_OPEN . " AND if(main_table.available_to is null, true, main_table.available_to > UTC_TIMESTAMP()) AND if(main_table.available_from is null, true, main_table.available_from < UTC_TIMESTAMP())")
            ->group('purcht.deal_id')
            ->having('if(SUM(maximum_allowed_purchases) = 0, true,if(SUM(purcht.qty_purchased) IS NOT NULL, (SUM(maximum_allowed_purchases) / COUNT(purcht.qty_purchased)) > SUM(purcht.qty_purchased), true))')
            ->order('main_table.id DESC');

        return $this;
    }

    /*
     * 
     * This filter differs from frontend getActiveDeals
     * because we are not intrested if:
     * 1. Related to deal product is out of stock
     * 2. Maximum allowed purchases count has been reached
     * We just check that this deal is not only running i.e
     * but doesn't have available_to
     * set to null
     * 
     */

    public function getActiveDealsForCron()
    {
        $this->getSelect()
            ->where("`main_table`.`close_state`=" . AW_Collpur_Model_Deal::STATE_OPEN . " AND if(main_table.available_to is null, false, main_table.available_to > UTC_TIMESTAMP()) AND if(main_table.available_from is null, true, main_table.available_from < UTC_TIMESTAMP())")
            ->order('main_table.id DESC');
        return $this;
    }

    public function addEnabledFilter()
    {
        $this
            ->getSelect()
            ->where('main_table.is_active = 1');
        return $this;
    }

    public function addCustomerFilter($id)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $select = $connection->select()
            ->from(array('main' => $this->getTable('collpur/deal')), array('main.id'))
            ->join(array('purch' => $this->getTable('collpur/dealpurchases')), 'main.id = purch.deal_id', array())
            ->where('purch.customer_id = ?', $id)
            ->group('main.id')
            ->order('main.id DESC');

        $where = array_unique($connection->fetchCol($select));
        $this->getSelect()->where('main_table.id IN (?)', $where);
        return $this;
    }

    public function setExpiredFlag($collection, $condition = 'setExpiredFlag')
    {
        foreach ($collection as $item) {
            $item->{$condition}(1)->save();
        }
        return $this;
    }

    public function unsExpiredFlag($collection, $condition = 'setExpiredFlag')
    {
        foreach ($collection as $item) {
            $item->{$condition}(0)->save();
        }
        return $this;
    }

    public function getSize()
    {
        if ($this->isNativeCount) {
            return parent::getSize();
        }

        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelectCountSql();
            $this->_totalRecords = $this->getConnection()->fetchAll($sql, $this->_bindParams);
        }

        return count($this->_totalRecords);
    }

    public function addStoreFilter($stores = null, $breakOnAllStores = false)
    {
        $_stores = array(Mage::app()->getStore()->getId());
        if (is_string($stores)) $_stores = explode(',', $stores);
        if (is_array($stores)) $_stores = $stores;
        if (!in_array('0', $_stores))
            array_push($_stores, '0');
        if ($breakOnAllStores && $_stores == array(0)) return $this;
        $_sqlString = '(';
        $i = 0;
        foreach ($_stores as $_store) {
            $_sqlString .= sprintf('find_in_set(%s, store_ids)', $this->getConnection()->quote($_store));
            if (++$i < count($_stores))
                $_sqlString .= ' OR ';
        }
        $_sqlString .= ')';
        $this->getSelect()->where($_sqlString);

        return $this;
    }
}

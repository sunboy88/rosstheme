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
 * @package    AW_Popup
 * @version    1.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Popup_Model_Mysql4_Popup_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_includedCustomerStat = false;

    public function _construct()
    {
        parent::_construct();
        $this->_init('popup/popup');
    }

    public function addFilterByStoreId($id = null)
    {
        if (is_null($id)) {
            $id = Mage::app()->getStore()->getId();
        }
        $this->getSelect()->where('find_in_set(?, store_view) or find_in_set(0, store_view)', $id);
        return $this;
    }

    public function addShowAtFilter($id = null)
    {
        if (is_null($id)) {
            $id = Mage::app()->getStore()->getId();
        }
        $this->getSelect()->where('find_in_set(?, show_at)', $id);
        return $this;
    }

    public function includeCustomerStat()
    {
        if (!$this->_includedCustomerStat) {
            $conditions = '`main_table`.`popup_id`=`stat`.`popup_id`';
            $sessionData = Mage::getSingleton('customer/session');
            if ($customerId = $sessionData->getCustomerId()) {
                $conditions .= " AND `customer_id`='" . $customerId . "'";
            } else {
                $conditions .= " AND `session_id`='" . $sessionData->getEncryptedSessionId() . "'";
            }
            $this->getSelect()
                ->joinLeft(
                    array('stat' => $this->getTable('popup/stat')),
                    $conditions,
                    new Zend_Db_Expr('count(`stat`.`stat_id`) as `use_count_per_customer`')
                )
                ->where("(`show_count`='0' OR `show_count`>`use_count`)")
                ->having("(`show_count_per_customer`='0' OR `show_count_per_customer`>`use_count_per_customer`)")
                ->group('popup_id')
            ;

            $this->_includedCustomerStat = true;
        }
        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        // convert StoreView from string to Array
        foreach ($this->_items as $item) {
            $item->setStoreView(explode(',', $item->getStoreView()));
        }

        Mage::dispatchEvent('core_collection_abstract_load_after', array('collection' => $this));
        return $this;
    }
}
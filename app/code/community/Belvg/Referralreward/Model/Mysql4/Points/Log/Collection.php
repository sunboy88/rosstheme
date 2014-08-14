<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/******************************************
 *      MAGENTO EDITION USAGE NOTICE      *
 ******************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/******************************************
 *      DISCLAIMER                        *
 ******************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 ******************************************
 * @category   Belvg
 * @package    Belvg_Referralreward
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_Model_Mysql4_Points_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('referralreward/points_log');
    }

    public function getPointsSumm($customerId)
    {
        $this->addFieldToFilter('customer_id', $customerId)
             ->addExpressionFieldToSelect('points_sum', 'SUM({{points}})', 'points')
             ->getSelect()
                ->group('customer_id');

        return $this->getFirstItem()->getPointsSum();
    }

    public function clearPoints($customerId, $currentTime)
    {
        $resource        = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table           = $resource->getTableName('referralreward/points_log');
        $query           = "UPDATE {$table} SET points = 0 WHERE " . 
                                "customer_id = {$customerId} AND " .
                                "points > 0 AND " .
                                "end_at < '{$currentTime}'";

        $writeConnection->query($query);
    }
}
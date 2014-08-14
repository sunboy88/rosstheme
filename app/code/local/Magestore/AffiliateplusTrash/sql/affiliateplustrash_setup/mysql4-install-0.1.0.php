<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusTrash
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

// Add field is deleted for transaction
$installer->getConnection()->addColumn($this->getTable('affiliateplus_transaction'), 'transaction_is_deleted', "tinyint(1) NOT NULL default '0'");

// Add field is deleted for withdrawal
$installer->getConnection()->addColumn($this->getTable('affiliateplus_payment'), 'payment_is_deleted', "tinyint(1) NOT NULL default '0'");

$installer->endSetup();

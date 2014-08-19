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
 * @package     Magestore_Geoip
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create geoip table
 */
$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('onestepcheckout_delivery')};
    CREATE TABLE {$this->getTable('onestepcheckout_delivery')} (
            `delivery_id` int(11) unsigned NOT NULL auto_increment,
            `delivery_time_date` varchar(16) default '',
            `order_id` int(11) NOT NULL default '0',   			  		   
            PRIMARY KEY (`delivery_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
$installer->endSetup();


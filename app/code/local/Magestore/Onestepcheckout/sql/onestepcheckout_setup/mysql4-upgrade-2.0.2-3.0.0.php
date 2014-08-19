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
    DROP TABLE IF EXISTS {$this->getTable('onestepcheckout_config_data')};
    CREATE TABLE {$this->getTable('onestepcheckout_config_data')} (
            `config_id` int(11) unsigned NOT NULL auto_increment,
            `scope` varchar(8) default '',
            `scope_id` int(11) NOT NULL default '0',			 
            `path` varchar(255) default '',			 
            `value` text default '' ,		   			  		   
            PRIMARY KEY (`config_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
$installer->endSetup();


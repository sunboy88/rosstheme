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
 * @package     Magestore_AffiliateplusBanner
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();
$bannerTable = $installer->getTable('affiliateplus_banner');

/** Add columns for banner table */
$connection->addColumn($bannerTable, 'target', "varchar(31) NOT NULL default '_blank'");
$connection->addColumn($bannerTable, 'rel_nofollow', "smallint(6) NOT NULL default 0");

$connection->addColumn($bannerTable, 'peel_image', 'varchar(255) NULL');
$connection->addColumn($bannerTable, 'peel_width', 'int(10) default 0');
$connection->addColumn($bannerTable, 'peel_height', 'int(10) default 0');
$connection->addColumn($bannerTable, 'peel_direction', 'smallint(6) default 1');

/** Add table for banner rotator */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('affiliateplusbanner_rotator')};
CREATE TABLE {$this->getTable('affiliateplusbanner_rotator')}(
    `rotator_id` int(10) unsigned NOT NULL auto_increment,
    `parent_id` int(10) unsigned NOT NULL default '0',
    `banner_id` int(10) unsigned NOT NULL default '0',
    `position` int(10) unsigned NOT NULL default '1',
    PRIMARY KEY (`rotator_id`),
    CONSTRAINT `FK_AFFILIATEPLUSBANNER_PARENT` FOREIGN KEY (`parent_id`)
        REFERENCES `{$bannerTable}` (`banner_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_AFFILIATEPLUSBANNER_CHILD` FOREIGN KEY (`banner_id`)
        REFERENCES `{$bannerTable}` (`banner_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

/** End setup */
$installer->endSetup();

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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('popup/popup')};
    CREATE TABLE {$this->getTable('popup/popup')} (
        `popup_id` int(11) unsigned NOT NULL auto_increment,
        `name` varchar(255) NOT NULL default '',
        `title` varchar(255) NULL,
        `popup_content` text NOT NULL default '',
        `status` smallint(6) NOT NULL default '0',
        `show_at` varchar(255) NOT NULL default '',
        `store_view` varchar(255) NOT NULL default '0',
        `date_from` date NULL,
        `date_to` date NULL,
        `align` smallint(6) NOT NULL,
        `sort_order` smallint(6) NOT NULL,
        `width` smallint(6) NULL,
        `height` smallint(6) NULL,
        PRIMARY KEY (`popup_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
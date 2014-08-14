<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productquestions
 * @version    1.5.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


$installer = $this;
$installer->startSetup();

try {
    $installer->run("

ALTER TABLE {$this->getTable('productquestions/productquestions')}
    ADD `question_store_ids` VARCHAR( 255 ) NOT NULL DEFAULT '0' COMMENT 'displayed on' AFTER `question_store_id` ;


-- DROP TABLE IF EXISTS {$this->getTable('productquestions/helpfulness')};

CREATE TABLE {$this->getTable('productquestions/helpfulness')} (
 `question_id` int(10) unsigned NOT NULL default '0',
 `vote_count` int(10) unsigned NOT NULL default '0',
 `vote_sum` int(10) unsigned NOT NULL default '0',
 PRIMARY KEY  (`question_id`),
 CONSTRAINT `FK_helpfulness` FOREIGN KEY (`question_id`) REFERENCES `{$this->getTable('productquestions/productquestions')}` (`question_id`) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();

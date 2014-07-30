<?php
/**
 * @version   1.0 06.08.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

$installer = $this;
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('athlete/bannerslider_slides_group')}`;
CREATE TABLE `{$this->getTable('athlete/bannerslider_slides_group')}` (
  `group_id` int(11) unsigned NOT NULL auto_increment,
  `group_name` varchar(64) NOT NULL default '',
  `slide_width` smallint(6) NOT NULL ,
  `slide_height` smallint(6) NOT NULL ,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('athlete/bannerslider_slides_group')}` (`group_id`, `group_name`, `slide_width`, `slide_height`, `created_time`, `update_time`) VALUES (1, 'home_page', 320, 220, NOW(), NOW());
INSERT INTO `{$this->getTable('athlete/bannerslider_slides_group')}` (`group_id`, `group_name`, `slide_width`, `slide_height`, `created_time`, `update_time`) VALUES (2, 'sidebar', 232, 368, NOW(), NOW());
");

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('athlete/bannerslider_slides')}`;
CREATE TABLE `{$this->getTable('athlete/bannerslider_slides')}` (
  `slide_id` int(11) unsigned NOT NULL auto_increment,
  `slide_group` int(11) unsigned NOT NULL ,
  `slide_bg` varchar(16) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `imageX2` varchar(255) NOT NULL default '',
  `title_color` varchar(16) NOT NULL default '',
  `title_bg` varchar(16) NOT NULL default '',
  `title_position` varchar(16) NOT NULL default '',
  `title` text NOT NULL default '',
  `link_color` varchar(16) NOT NULL default '',
  `link_bg` varchar(16) NOT NULL default '',
  `link_text` varchar(255) NOT NULL default '',
  `link_href` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_id`, `slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (1, 1, 'olegnax/athlete/bannerslider/bs1.jpg', 'bottom-left', 'WHO\'S GOT\r\nYOUR BACK?', '#000000', '#ffe51e', 'shop awesome gear here', '//athlete.olegnax.com', 1, 10, NOW(), NOW());
INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_id`, `slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (3, 1, 'olegnax/athlete/bannerslider/bs2.jpg', 'top-left', 'RUN FREE\r\nWITH NIKE', '#000000', '#00d8f9', 'shop now', '//athlete.olegnax.com', 1, 20, NOW(), NOW());
INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_id`, `slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (4, 1, 'olegnax/athlete/bannerslider/bs3.jpg', 'center', 'A VICTORY LAP\r\n4800 MILES', '', '', '', '', 1, 30, NOW(), NOW());
INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_id`, `slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (5, 1, 'olegnax/athlete/bannerslider/bs4.jpg', 'bottom-left', 'WHO\'S GOT\r\nYOUR BACK?', '#000000', '#ffe51e', 'shop awesome gear here', '//athlete.olegnax.com', 1, 40, NOW(), NOW());
INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_id`, `slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (6, 1, 'olegnax/athlete/bannerslider/bs5.jpg', 'top-left', 'RUN FREE\r\nWITH NIKE', '#000000', '#00d8f9', 'shop now', '//athlete.olegnax.com', 1, 50, NOW(), NOW());
INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_id`, `slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (7, 1, 'olegnax/athlete/bannerslider/bs6.jpg', 'center', 'A VICTORY LAP\r\n4800 MILES', '', '', '', '', 1, 60, NOW(), NOW());

INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_id`, `slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (8, 2, 'olegnax/athlete/bannerslider/bs7.jpg', 'bottom-left', 'WHO\'S GOT\r\nYOUR BACK?', '#000000', '#ffe51e', 'shop awesome gear here', '//athlete.olegnax.com', 1, 10, NOW(), NOW());
INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_id`, `slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (9, 2, 'olegnax/athlete/bannerslider/bs8.jpg', 'top-left', 'RUN FREE\r\nWITH NIKE', '#000000', '#00d8f9', 'shop now', '//athlete.olegnax.com', 1, 20, NOW(), NOW());
INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_id`, `slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (10, 2, 'olegnax/athlete/bannerslider/bs9.jpg', 'center', 'A VICTORY\r\n4800 MILES', '', '', '', '', 1, 30, NOW(), NOW());

");

/**
 * Drop 'slides_store' table
 */
$conn = $installer->getConnection();
$conn->dropTable($installer->getTable('athlete/bannerslider_slides_store'));

/**
 * Create table for stores
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('athlete/bannerslider_slides_store'))
    ->addColumn('slide_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'nullable'  => false,
    'primary'   => true,
), 'Slide ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
), 'Store ID')
    ->addIndex($installer->getIdxName('athlete/bannerslider_slides_store', array('store_id')),
    array('store_id'))
    ->addForeignKey($installer->getFkName('athlete/bannerslider_slides_store', 'slide_id', 'athlete/bannerslider_slides', 'slide_id'),
    'slide_id', $installer->getTable('athlete/bannerslider_slides'), 'slide_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('athlete/bannerslider_slides_store', 'store_id', 'core/store', 'store_id'),
    'store_id', $installer->getTable('core/store'), 'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Slide To Store Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Assign 'all store views' to existing slides
 */
$installer->run("INSERT INTO {$this->getTable('athlete/bannerslider_slides_store')} (`slide_id`, `store_id`) SELECT `slide_id`, 0 FROM {$this->getTable('athlete/bannerslider_slides')};");

$installer->endSetup();
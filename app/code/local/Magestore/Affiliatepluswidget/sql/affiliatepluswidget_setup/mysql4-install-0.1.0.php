<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('affiliatepluswidget')};
CREATE TABLE {$this->getTable('affiliatepluswidget')} (
  `widget_id` int(10) unsigned NOT NULL auto_increment,
  `account_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `category_ids` text NOT NULL default '',
  `product_id` int(10) unsigned NOT NULL default '0',
  `is_image` tinyint(1) NOT NULL default '0',
  `is_price` tinyint(1) NOT NULL default '0',
  `is_rated` tinyint(1) NOT NULL default '0',
  `is_short_desc` tinyint(1) NOT NULL default '0',
  `widget_size` varchar(63) NOT NULL default '',
  `height` smallint(5) NOT NULL default '0',
  `width` smallint(5) NOT NULL default '0',
  `rows` tinyint(2) NOT NULL default '0',
  `columns` tinyint(2) NOT NULL default '0',
  `search` varchar(255) NOT NULL default '',
  `background` varchar(15) NOT NULL default '',
  `border` varchar(15) NOT NULL default '',
  `textheader` varchar(15) NOT NULL default '',
  `textlink` varchar(15) NOT NULL default '',
  `textbody` varchar(15) NOT NULL default '',
  INDEX (`account_id`),
  FOREIGN KEY (`account_id`) REFERENCES {$this->getTable('affiliateplus_account')} (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`widget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
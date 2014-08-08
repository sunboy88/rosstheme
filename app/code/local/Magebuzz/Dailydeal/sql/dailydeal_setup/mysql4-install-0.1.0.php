<?php
/*
* Copyright (c) 2013 www.magebuzz.com
*/
$installer = $this;
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('dailydeal_deal')};
CREATE TABLE {$this->getTable('dailydeal_deal')} (
  `deal_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `product_id` int(10) NOT NULL default 0,
  `deal_price` varchar(20) NOT NULL default 0,
  `quantity` varchar(20) NOT NULL default 0,
  `start_time` datetime NULL,
  `end_time` datetime NULL,
  `status` smallint(6) NOT NULL default '1',
  PRIMARY KEY (`deal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
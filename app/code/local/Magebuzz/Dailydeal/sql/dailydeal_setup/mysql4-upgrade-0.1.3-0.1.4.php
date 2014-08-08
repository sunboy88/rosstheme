<?php
/*
* Copyright (c) 2013 www.magebuzz.com
*/
$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('dailydeal_deal_store')};
CREATE TABLE {$this->getTable('dailydeal_deal_store')} (
  `deal_store_id` int(11) unsigned NOT NULL auto_increment,
  `deal_id` int(11) NOT NULL,
	`store_id` int(11) NOT NULL default '0',
   PRIMARY KEY (`deal_store_id`)    
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 ");
$installer->endSetup(); 
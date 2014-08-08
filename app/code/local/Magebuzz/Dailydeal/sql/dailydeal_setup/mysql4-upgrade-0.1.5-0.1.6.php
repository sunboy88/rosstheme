<?php
/*
* Copyright (c) 2013 www.magebuzz.com
*/
$installer = $this;
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('dailydeal_deal_store')};
CREATE TABLE {$this->getTable('dailydeal_deal_store')} (
  `deal_id` int(11) unsigned NOT NULL,
	`store_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`deal_id`,`store_id`),
	CONSTRAINT `FK_DAILYDEAL_DAILYDEAL_STORE` FOREIGN KEY (`deal_id`) REFERENCES `{$this->getTable('dailydeal_deal')}` (`deal_id`) ON DELETE CASCADE	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 ");
$installer->endSetup(); 
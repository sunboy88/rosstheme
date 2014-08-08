<?php
/*
* Copyright (c) 2013 www.magebuzz.com
*/
$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('dailydeal_deal_mail')};
CREATE TABLE {$this->getTable('dailydeal_deal_mail')} (
  `deal_email_id` int(11) unsigned NOT NULL auto_increment,
	`customer_name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '1',
	`subscriber_confirm_code` varchar(255) NULL default '',
  PRIMARY KEY (`deal_email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 ");
$installer->endSetup(); 
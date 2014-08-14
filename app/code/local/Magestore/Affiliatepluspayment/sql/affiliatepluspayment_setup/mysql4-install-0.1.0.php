<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('affiliatepluspayment_offline')};
DROP TABLE IF EXISTS {$this->getTable('affiliatepluspayment_bank')};
DROP TABLE IF EXISTS {$this->getTable('affiliatepluspayment_bankaccount')};

CREATE TABLE {$this->getTable('affiliatepluspayment_bankaccount')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `account_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `address` varchar(255) NOT NULL default '',
  `account_name` varchar(255) NOT NULL default '',
  `account_number` varchar(255) NOT NULL default '',
  `routing_code` varchar(255) NOT NULL default '',
  INDEX(`account_id`),
  FOREIGN KEY (`account_id`) REFERENCES {$this->getTable('affiliateplus_account')} (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('affiliatepluspayment_bank')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `payment_id` int(10) unsigned NOT NULL,
  `bankaccount_id` int(10) unsigned NOT NULL,
  `bankaccount_html` text NOT NULL default '',
  `invoice_number` varchar(255) NOT NULL default '',
  `message` text NOT NULL default '',
  INDEX(`payment_id`),
  FOREIGN KEY (`payment_id`) REFERENCES {$this->getTable('affiliateplus_payment')} (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('affiliatepluspayment_offline')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `payment_id` int(10) unsigned NOT NULL,
  `address_id` int(10) unsigned NOT NULL default '0',
  `address_html` text NOT NULL default '',
  `transfer_info` varchar(255) NOT NULL default '',
  `message` text NOT NULL default '',
  INDEX(`payment_id`),
  FOREIGN KEY (`payment_id`) REFERENCES {$this->getTable('affiliateplus_payment')} (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
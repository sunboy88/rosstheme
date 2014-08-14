<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('affiliatepluspayment_bankaccount')}
    ADD COLUMN `swift_code` varchar(100) NOT NULL default '';

ALTER TABLE {$this->getTable('affiliateplus_account')}
    ADD COLUMN `recurring_payment` tinyint(1) NOT NULL default '1',
    ADD COLUMN `last_received_date` date NULL,
    ADD COLUMN `recurring_method` varchar(100) NOT NULL default 'paypal',
    ADD COLUMN `moneybooker_email` varchar(255) NOT NULL default '';


CREATE TABLE {$this->getTable('affiliatepluspayment_moneybooker')}(
    `payment_moneybooker_id` int(10) unsigned NOT NULL auto_increment,
    `payment_id` int(10) unsigned NOT NULL,
    `email` varchar(255) NOT NULL default '',
    `transaction_id` varchar(255) NOT NULL default '',
    `description` text NOT NULL default '',
    INDEX(`payment_id`),
    FOREIGN KEY (`payment_id`) REFERENCES {$this->getTable('affiliateplus_payment')} (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`payment_moneybooker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE {$this->getTable('affiliateplus_payment')}
    ADD COLUMN `is_recurring` tinyint(1) NOT NULL default '0';

    ");

$installer->endSetup(); 
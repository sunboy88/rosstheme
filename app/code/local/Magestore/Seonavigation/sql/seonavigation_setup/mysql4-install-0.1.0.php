<?php

$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('seonavigation')};
CREATE TABLE {$this->getTable('seonavigation')} (
  `seonavigation_id` int(11) unsigned NOT NULL auto_increment,
  `rewrite_id` int(10) unsigned NOT NULL,
  `request_path` varchar(255) default '',
  `url_key` varchar(255) default '',
  `clear_url` varchar(255) default '',
  `query_params` text NULL,
  `meta_title` varchar(255) NOT NULL default '',
  `meta_keywords` varchar(255) NOT NULL default '',
  `meta_description` text NULL default '',
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`seonavigation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();

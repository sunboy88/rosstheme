<?php
/*
 hainh add upgrade for adding Refefrring website
22-04-2014
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('affiliateplus_account'), 'referring_website', 'VARCHAR(255) NULL DEFAULT NULL');

$installer->endSetup();
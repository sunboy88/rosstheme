<?php

$installer = $this;

$installer->startSetup();

$installer->setConfigData('occ/configuration/enabled', 			0);
$installer->setConfigData('occ/configuration/customer_groups',  null);
$installer->setConfigData('occ/configuration/payment_methods',  null);

$installer->endSetup(); 
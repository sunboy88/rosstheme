<?php
$installer = $this;
$installer->startSetup();

Mage::getConfig()->reinit();
//generate css
Mage::getSingleton('athlete/css')->regenerate();

$installer->endSetup();

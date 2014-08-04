<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Shopby
*/
$this->startSetup();

$this->run("
ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `include_in` VARCHAR(256) NOT NULL;
");

$this->endSetup();
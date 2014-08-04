<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Shopby
*/
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amshopby/value')}` 
    ADD COLUMN `featured_order` TINYINT UNSIGNED DEFAULT 0
"); 
$this->endSetup();
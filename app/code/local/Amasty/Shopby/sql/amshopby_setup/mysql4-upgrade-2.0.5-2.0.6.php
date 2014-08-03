<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `backend_type` VARCHAR(45) NOT NULL DEFAULT '';
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `slider_type` TINYINT(1) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `from_to_widget` TINYINT(1) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `value_label` VARCHAR(16) NOT NULL;
"); 

$this->endSetup();
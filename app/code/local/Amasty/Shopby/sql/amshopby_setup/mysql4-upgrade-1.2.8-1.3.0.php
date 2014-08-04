<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `single_choice` TINYINT(1) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `collapsed` TINYINT(1) NOT NULL;
"); 

$this->endSetup();
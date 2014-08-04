<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amshopby/value')}` ADD `cms_block` VARCHAR(255);
"); 

$this->endSetup();
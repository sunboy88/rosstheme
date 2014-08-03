<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `max_options` SMALLINT NOT NULL AFTER `attribute_id` 
");

$this->endSetup();
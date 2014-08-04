<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `depend_on`  VARCHAR(255) NOT NULL;
"); 

$this->endSetup();
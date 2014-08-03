<?php

/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amshopby/page')}` ADD COLUMN `meta_kw` varchar(255) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/value')}` ADD COLUMN `meta_kw` varchar(255) NOT NULL;
");

$this->endSetup();
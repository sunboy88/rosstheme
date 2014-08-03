<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Shopby
*/
$this->startSetup();

$this->run("

ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `show_search` TINYINT( 1 ) NOT NULL ,
ADD `slider_decimal` TINYINT( 1 ) NOT NULL ;


");
 
$this->endSetup();
<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Shopby
*/
$this->startSetup();

$this->run("
ALTER TABLE `{$this->getTable('amshopby/value')}` ADD  `url_alias` VARCHAR( 255 ) NULL DEFAULT NULL ,
ADD INDEX (  `url_alias` )
");
 
$this->endSetup();
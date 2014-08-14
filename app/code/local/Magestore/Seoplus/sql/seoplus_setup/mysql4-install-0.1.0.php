<?php

$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$connection->addColumn($this->getTable('catalogsearch/search_query'),'meta_title','text null');
$connection->addColumn($this->getTable('catalogsearch/search_query'),'meta_keywords','text null');
$connection->addColumn($this->getTable('catalogsearch/search_query'),'meta_description','text null');
$connection->addColumn($this->getTable('catalogsearch/search_query'),'url_key','text null');

$installer->endSetup();
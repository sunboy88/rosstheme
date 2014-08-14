<?php

$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$connection->addColumn($this->getTable('tag/tag'), 'meta_title', 'text null');
$connection->addColumn($this->getTable('tag/tag'), 'meta_keywords', 'text null');
$connection->addColumn($this->getTable('tag/tag'), 'meta_description', 'text null');
$connection->addColumn($this->getTable('tag/tag'), 'url_key', 'text null');

$installer->endSetup();
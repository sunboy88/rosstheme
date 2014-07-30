<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();

$installer->setConfigData('athlete/layout/responsive', '1');
$installer->setConfigData('athlete/layout/max_width', '1024');
$installer->setConfigData('athlete/layout/fluid', '0');

$installer->setConfigData('athlete/listing/product_img_width', '296');
$installer->setConfigData('athlete/listing/grid_cart', '0');

$installer->setConfigData('athlete/header/logo_padding', '30');
$installer->setConfigData('athlete/header/sticky', '0');

$installer->setConfigData('athlete/sliders/banner_scroll_items', 'item');
$installer->setConfigData('athlete/sliders/banner_rewind', '0');
$installer->setConfigData('athlete/sliders/product_scroll_items', 'item');
$installer->setConfigData('athlete/sliders/product_rewind', '0');
$installer->setConfigData('athlete_brands/slider/product_scroll_items', 'page');
$installer->setConfigData('athlete_brands/slider/product_rewind', '0');

$installer->endSetup();
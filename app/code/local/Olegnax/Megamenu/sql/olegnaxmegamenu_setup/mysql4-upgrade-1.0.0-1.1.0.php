<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$this->startSetup();

$this->addAttribute('catalog_category', 'olegnaxmegamenu_layout', array(
	'group' => 'Olegnax Megamenu',
	'type' => 'varchar',
	'input' => 'select',
	'source' => 'olegnaxmegamenu/category_attribute_source_layout',
	'label' => 'Dropdown Layout',
	'backend' => '',
	'visible' => true,
	'required' => false,
	'visible_on_front' => true,
	'wysiwyg_enabled' => true,
	'is_html_allowed_on_front'	=> true,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'position' => 120,
));

$this->addAttribute('catalog_category', 'olegnaxmegamenu_menu', array(
	'group' => 'Olegnax Megamenu',
	'type' => 'int',
	'input' => 'select',
	'source' => 'olegnaxmegamenu/category_attribute_source_yesno',
	'label' => 'Show subcategories',
	'note' => "Show/hide subcategories list",
	'backend' => '',
	'visible' => true,
	'required' => false,
	'visible_on_front' => true,
	'wysiwyg_enabled' => true,
	'is_html_allowed_on_front'	=> true,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'position' => 140,
));


$this->endSetup();
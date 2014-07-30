<?php
$this->startSetup();

$this->addAttribute('catalog_category', 'olegnaxmegamenu_type', array(
	'group' => 'Olegnax Megamenu',
	'type' => 'varchar',
	'input' => 'select',
	'source' => 'olegnaxmegamenu/category_attribute_source_type',
	'label' => 'Dropdown type',
	'note' => "For top-level categories only",
	'backend' => '',
	'visible' => true,
	'required' => false,
	'visible_on_front' => true,
	'wysiwyg_enabled' => true,
	'is_html_allowed_on_front'	=> true,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'position' => 100,
));

$this->addAttribute('catalog_category', 'olegnaxmegamenu_top', array(
	'group' => 'Olegnax Megamenu',
	'input' => 'textarea',
	'type' => 'text',
	'label' => 'Top block',
	'note' => "For top-level categories only",
	'backend' => '',
	'visible' => true,
	'required' => false,
	'visible_on_front' => true,
	'wysiwyg_enabled' => true,
	'is_html_allowed_on_front'	=> true,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'position' => 200,
));

$this->addAttribute('catalog_category', 'olegnaxmegamenu_bottom', array(
	'group' => 'Olegnax Megamenu',
	'input' => 'textarea',
	'type' => 'text',
	'label' => 'Bottom block',
	'note' => "For top-level categories only",
	'backend' => '',
	'visible' => true,
	'required' => false,
	'visible_on_front' => true,
	'wysiwyg_enabled' => true,
	'is_html_allowed_on_front'	=> true,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'position' => 300,
));

$this->addAttribute('catalog_category', 'olegnaxmegamenu_right', array(
	'group' => 'Olegnax Megamenu',
	'input' => 'textarea',
	'type' => 'text',
	'label' => 'Right block',
	'note' => "For top-level categories only",
	'backend' => '',
	'visible' => true,
	'required' => false,
	'visible_on_front' => true,
	'wysiwyg_enabled' => true,
	'is_html_allowed_on_front'	=> true,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'position' => 400,
));

$this->addAttribute('catalog_category', 'olegnaxmegamenu_right_percent', array(
	'group' => 'Olegnax Megamenu',
	'type' => 'varchar',
	'input' => 'select',
	'source' => 'olegnaxmegamenu/category_attribute_source_percent',
	'label' => 'Right block width',
	'note' => "Width of right block in percents",
	'backend' => '',
	'visible' => true,
	'required' => false,
	'visible_on_front' => true,
	'wysiwyg_enabled' => true,
	'is_html_allowed_on_front'	=> true,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'position' => 500,
));

$this->endSetup();
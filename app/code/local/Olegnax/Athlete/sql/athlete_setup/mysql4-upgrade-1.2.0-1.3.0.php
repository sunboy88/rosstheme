<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();
$installer->setConfigData('athlete/header/menu', '1');

$installer->run("
INSERT INTO `{$this->getTable('athlete/bannerslider_slides_group')}` ( `group_name`, `slide_width`, `slide_height`, `created_time`, `update_time`) VALUES ( 'blog', 232, 232, NOW(), NOW());
SET @blog_group = LAST_INSERT_ID();

INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (@blog_group, 'olegnax/athlete/bannerslider/blog_01.jpg', 'bottom-left', 'WHO\'S GOT\r\nYOUR BACK?', '#000000', '#ffe51e', 'shop awesome gear here', '//athlete.olegnax.com', 1, 10, NOW(), NOW());
SET @blog_slide1 = LAST_INSERT_ID();
INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (@blog_group, 'olegnax/athlete/bannerslider/blog_02.jpg', 'top-left', 'RUN FREE\r\nWITH NIKE', '#000000', '#00d8f9', 'shop now', '//athlete.olegnax.com', 1, 20, NOW(), NOW());
SET @blog_slide2 = LAST_INSERT_ID();
INSERT INTO `{$this->getTable('athlete/bannerslider_slides')}` (`slide_group`, `image`, `title_position`, `title`, `link_color`, `link_bg`, `link_text`, `link_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (@blog_group, 'olegnax/athlete/bannerslider/blog_03.jpg', 'center', 'A VICTORY\r\n4800 MILES', '', '', '', '', 1, 30, NOW(), NOW());
SET @blog_slide3 = LAST_INSERT_ID();

INSERT INTO {$this->getTable('athlete/bannerslider_slides_store')} (`slide_id`, `store_id`) VALUES (@blog_slide1, 0);
INSERT INTO {$this->getTable('athlete/bannerslider_slides_store')} (`slide_id`, `store_id`) VALUES (@blog_slide2, 0);
INSERT INTO {$this->getTable('athlete/bannerslider_slides_store')} (`slide_id`, `store_id`) VALUES (@blog_slide3, 0);

");

try {
	//create right col blog banner block
	$is_exist = Mage::getModel('cms/block')->getCollection()
		->addFieldToFilter('identifier', 'athlete_sideblock_blog_banners')
		->load();

	if ( !count($is_exist) ) {
		$data = array(
			'title' => 'Athlete Sideblock blog block',
			'identifier' => 'athlete_sideblock_blog_banners',
			'content' => '{{block type="athlete/bannerslider" slide_group="blog" template="olegnax/bannerslider.phtml" }}',
			'is_active' => 1,
			'sort_order' => 0,
			'stores' => array(0),
		);
		Mage::getModel('cms/block')->setData($data)->save();
	}

	//create header text block
	$is_exist = Mage::getModel('cms/block')->getCollection()
		->addFieldToFilter('identifier', 'athlete_header_text')
		->load();

	if ( !count($is_exist) ) {
		$data = array(
			'title' => 'Athlete Header text',
			'identifier' => 'athlete_header_text',
			'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quae, optio, delectus eligendi deleniti consequatur repudiandae soluta et ad quam obcaecati. ipsum dolor sit amet, consectetur adipisicing elit. <a href="#">Sapiente, dicta.</a>',
			'is_active' => 1,
			'sort_order' => 0,
			'stores' => array(0),
		);
		Mage::getModel('cms/block')->setData($data)->save();
	}

}
catch (Exception $e) {
	Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error occurred while updating athlete cms blocks.'));
}

$installer->endSetup();
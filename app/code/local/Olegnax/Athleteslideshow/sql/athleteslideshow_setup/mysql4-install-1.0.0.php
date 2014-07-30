<?php
/**
 * @version   1.0 06.08.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

$installer = $this;
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('athleteslideshow/slides')}`;
CREATE TABLE `{$this->getTable('athleteslideshow/slides')}` (
  `slide_id` int(11) unsigned NOT NULL auto_increment,
  `image` varchar(255) NOT NULL default '',
  `title_color` varchar(255) NOT NULL default '',
  `title_bg` varchar(255) NOT NULL default '',
  `title` text NOT NULL default '',
  `link_color` varchar(255) NOT NULL default '',
  `link_bg` varchar(255) NOT NULL default '',
  `link_hover_color` varchar(255) NOT NULL default '',
  `link_hover_bg` varchar(255) NOT NULL default '',
  `link_text` varchar(255) NOT NULL default '',
  `link_href` varchar(255) NOT NULL default '',
  `banner_1_img` varchar(255) NOT NULL default '',
  `banner_1_imgX2` varchar(255) NOT NULL default '',
  `banner_1_href` varchar(255) NOT NULL default '',
  `banner_2_img` varchar(255) NOT NULL default '',
  `banner_2_imgX2` varchar(255) NOT NULL default '',
  `banner_2_href` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('athleteslideshow/slides')}` (`slide_id`, `image`, `title`, `link_text`, `link_href`, `banner_1_img`, `banner_1_href`, `banner_2_img`, `banner_2_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (1, 'olegnax/athlete/slideshow/slide1.png', 'Join the\r\nRevolution', 'shop now', '//athlete.olegnax.com', 'olegnax/athlete/slideshow/slide_banner1.png', '//athlete.olegnax.com','olegnax/athlete/slideshow/slide_banner2.png', '//athlete.olegnax.com', 1, 10, NOW(), NOW() );
INSERT INTO `{$this->getTable('athleteslideshow/slides')}` (`slide_id`, `image`, `title`, `link_text`, `link_href`, `banner_1_img`, `banner_1_href`, `banner_2_img`, `banner_2_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (2, 'olegnax/athlete/slideshow/slide2.jpg', 'Lorem ipsum\r\ndolor sit amen', 'shop now', '//athlete.olegnax.com', 'olegnax/athlete/slideshow/slide_banner1.png', '//athlete.olegnax.com','olegnax/athlete/slideshow/slide_banner2.png', '//athlete.olegnax.com', 1, 10, NOW(), NOW() );
INSERT INTO `{$this->getTable('athleteslideshow/slides')}` (`slide_id`, `image`, `title`, `link_text`, `link_href`, `banner_1_img`, `banner_1_href`, `banner_2_img`, `banner_2_href`, `status`, `sort_order`, `created_time`, `update_time`) VALUES (3, 'olegnax/athlete/slideshow/slide3.jpg', 'due tocse\r\nentel lerge', 'shop now', '//athlete.olegnax.com', 'olegnax/athlete/slideshow/slide_banner1.png', '//athlete.olegnax.com','olegnax/athlete/slideshow/slide_banner2.png', '//athlete.olegnax.com', 1, 10, NOW(), NOW() );

");

/**
 * Drop 'slides_store' table
 */
$conn = $installer->getConnection();
$conn->dropTable($installer->getTable('athleteslideshow/slides_store'));

/**
 * Create table for stores
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('athleteslideshow/slides_store'))
    ->addColumn('slide_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'nullable'  => false,
    'primary'   => true,
), 'Slide ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
), 'Store ID')
    ->addIndex($installer->getIdxName('athleteslideshow/slides_store', array('store_id')),
    array('store_id'))
    ->addForeignKey($installer->getFkName('athleteslideshow/slides_store', 'slide_id', 'athleteslideshow/slides', 'slide_id'),
    'slide_id', $installer->getTable('athleteslideshow/slides'), 'slide_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('athleteslideshow/slides_store', 'store_id', 'core/store', 'store_id'),
    'store_id', $installer->getTable('core/store'), 'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Slide To Store Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Assign 'all store views' to existing slides
 */
$installer->run("INSERT INTO {$this->getTable('athleteslideshow/slides_store')} (`slide_id`, `store_id`) SELECT `slide_id`, 0 FROM {$this->getTable('athleteslideshow/slides')};");

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('athleteslideshow/revolution_slides')}`;
CREATE TABLE `{$this->getTable('athleteslideshow/revolution_slides')}` (
  `slide_id` int(11) unsigned NOT NULL auto_increment,
  `transition` text NOT NULL default '',
  `masterspeed` text NOT NULL default '',
  `slotamount` text NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `thumb` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `text` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('athleteslideshow/revolution_slides')}` (`slide_id`, `transition`, `masterspeed`, `slotamount`, `link`, `thumb`, `image`, `text`, `status`, `sort_order`, `created_time`, `update_time`) VALUES
	(1, 'papercut', '460', '1', '', '', 'olegnax/athlete/revolution/slide1.jpg', '', 1, 2, '2013-01-05 16:16:16', '2013-01-05 16:16:16'),
	(2, 'fade', '300', '1', '', '', 'olegnax/athlete/revolution/slide2.jpg', '', 1, 1, '2013-01-05 16:17:06', '2013-01-05 16:17:06'),
	(3, 'slideleft', '300', '1', '', '', 'olegnax/athlete/revolution/slide3.jpg', '', 1, 2, '2013-01-05 16:18:06', '2013-01-05 16:18:06'),
	(4, 'slidedown', '300', '7', '', '', 'olegnax/athlete/revolution/slide4.jpg', '', 1, 3, '2013-01-05 16:21:20', '2013-01-05 16:21:20');

");

/**
 * Drop 'slides_store' table
 */
$conn = $installer->getConnection();
$conn->dropTable($installer->getTable('athleteslideshow/revolution_slides_store'));

/**
 * Create table for stores
 */
$table = $installer->getConnection()
	->newTable($installer->getTable('athleteslideshow/revolution_slides_store'))
	->addColumn('slide_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable'  => false,
		'primary'   => true,
	), 'Slide ID')
	->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
	), 'Store ID')
	->addIndex($installer->getIdxName('athleteslideshow/revolution_slides_store', array('store_id')),
		array('store_id'))
	->addForeignKey($installer->getFkName('athleteslideshow/revolution_slides_store', 'slide_id', 'athleteslideshow/revolution_slides', 'slide_id'),
		'slide_id', $installer->getTable('athleteslideshow/revolution_slides'), 'slide_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->addForeignKey($installer->getFkName('athleteslideshow/revolution_slides_store', 'store_id', 'core/store', 'store_id'),
		'store_id', $installer->getTable('core/store'), 'store_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Slide To Store Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Assign 'all store views' to existing slides
 */
$installer->run("INSERT INTO {$this->getTable('athleteslideshow/revolution_slides_store')} (`slide_id`, `store_id`) SELECT `slide_id`, 0 FROM {$this->getTable('athleteslideshow/revolution_slides')};");

$installer->endSetup();

/**
 * add slide data
 */
$data = array(
	1 => '
<div class="caption sfr athlete_caption_light"  data-x="380" data-y="90" data-speed="500" data-start="500" data-easing="easeOutBack">LOREM & IPSUM</div>

<div class="caption sfl athlete_caption_light"  data-x="486" data-y="150" data-speed="500" data-start="500" data-easing="easeOutBack">DOLOR SIT AMEN</div>

<div class="caption fade athlete_medium_text_light" style="color:#706f71;" data-x="450" data-y="286" data-speed="500" data-start="1100" data-easing="easeOutExpo">Lorem ipsum dolor sit amen utrack!</div>

<div class="caption fade athlete_medium_text_light" style="color:#706f71;" data-x="459" data-y="316" data-speed="500" data-start="1100" data-easing="easeOutExpo">ullamco laboris nisi ut aliquip ex ea commodo consequat</div>

<div class="caption fade athlete_medium_text_light" style="color:#706f71;" data-x="562" data-y="346" data-speed="500" data-start="1100" data-easing="easeOutExpo">Excepteur sint occaecat cupidatat.</div>\r\n\r\n<div class="caption sfb athlete_white_bg_bold"  data-x="434" data-y="230" data-speed="500" data-start="800" data-easing="easeOutExpo">EXCERPUT  & CUPIDADAT</div>',
	'
<div class="caption fade athlete_caption_underline_light"  data-x="550" data-y="138" data-start="100" data-speed="800"  data-easing="easeOutExpo">IPSUM DOLOR</div>

<div class="caption fade athlete_small_text_light" data-x="589" data-y="222" data-speed="800" data-start="300" data-easing="easeOutExpo">ullamco laboris nisi ut aliquip ex ea commodo consequat</div>

<div class="caption fade athlete_small_text_light" data-x="602" data-y="242" data-speed="800" data-start="600" data-easing="easeOutExpo">ullamco laboris nisi ut aliquip ex ea commodo </div>

<div class="caption fade athlete_small_text_light" data-x="668" data-y="275" data-speed="800" data-start="800" data-easing="easeOutExpo"><a href="//olegnax.com">BUY NOW</a></div>',
	'
<div class="caption athlete_large_caption_bold sft" data-x="461" data-y="80" data-speed="500" data-start="600" data-easing="easeOutExpo"  >LOREM</div>

<div class="caption large_black_text sft"  data-x="462" data-y="161" data-speed="500" data-start="700" data-easing="easeOutExpo"  >IPSUM DOLOR</div>

<div class="caption sfb"  data-x="460" data-y="223" data-speed="350" data-start="1000" data-easing="easeOutBack"  ><img src="{{media url="olegnax/athlete/samples/video.jpg"}}" alt="image"></div>

<div class="caption sfb"  data-x="700" data-y="223" data-speed="350" data-start="1100" data-easing="easeOutBack"  ><img src="{{media url="olegnax/athlete/samples/video.jpg"}}" alt="image"></div>',
	'
<div class="caption fade fullscreenvideo" data-autoplay="false" data-x="0" data-y="0" data-speed="500" data-start="10" data-easing="easeOutBack"><iframe src="http://player.vimeo.com/video/22775048?title=0&amp;byline=0&amp;portrait=0;api=1" width="50" height="50"></iframe></div>

<div class="caption big_white sft stt" data-x="327" data-y="25" data-speed="300" data-start="500" data-easing="easeOutExpo" data-end="4000" data-endspeed="300" data-endeasing="easeInSine" >Excepteur sint occaecat cupidatat</div>',
);

$model = Mage::getModel('athleteslideshow/athleterevolution');
foreach ( $data as $k => $v ) {
	$model->load($k)
		->setText($v)
		->save();
}
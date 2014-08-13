<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table dsresponsiveslider(slide_id int not null auto_increment, title varchar(255), image_name varchar(255), image_url varchar(1000), content text,is_active varchar(255), primary key(slide_id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
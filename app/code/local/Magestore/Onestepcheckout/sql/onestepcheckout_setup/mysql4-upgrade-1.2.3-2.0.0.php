<?php
$installer = $this;
$installer->startSetup();

	$installer->run("
		ALTER TABLE {$this->getTable('sales_flat_order')} 
			ADD `onestepcheckout_giftwrap_amount` DECIMAL( 12, 4 );      			     
			
		CREATE TABLE {$this->getTable('onestepcheckout_survey')}(
			`survey_id` int(11) unsigned NOT NULL auto_increment,
			`question` varchar(255) default '',			 
			`answer` varchar(255) default '',			 
		    `order_id` int(10) unsigned NOT NULL,		   			  		   
		    PRIMARY KEY (`survey_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
$installer->endSetup(); 
<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE newsletter_subscriber
ADD full_name VARCHAR(255)
SQLTEXT;

$installer->run($sql);
$installer->endSetup();

<?php
$this->startSetup();

Mage::getModel('core/config_data')
        ->setScope('default')
        ->setPath('belvgall/feed/installed')
        ->setValue(time())
        ->save(); 

Mage::getModel('core/config_data')
        ->setScope('default')
        ->setPath('belvgall/feed/check_frequency')
        ->setValue(3600*12)
        ->save(); 

Mage::getModel('core/config_data')
        ->setScope('default')
        ->setPath('belvgall/feed/last_update')
        ->setValue(0)
        ->save(); 

Mage::getModel('core/config_data')
        ->setScope('default')
        ->setPath('belvgall/feed/interests')
        ->setValue('INSTALLED_UPDATE,UPDATE_RELEASE,NEW_RELEASE,PROMO,INFO')
        ->save();

$feedData = array();
$feedData[] = array(
    'severity'      => 4,
    'date_added'    => gmdate('Y-m-d H:i:s', time()),
    'title'         => "Belvg's extension has been installed. Check the Admin > Configuration > Belvg Extensions.",
    'description'   => 'You can see versions of the installed extensions right in the admin, as well as configure notifications about major updates.',
    'url'           => 'http://store.belvg.com/blog/'
);
Mage::getModel('adminnotification/inbox')->parse($feedData);

$this->endSetup();
<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Mail_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
  public function __construct(){
    parent::__construct();
    $this->setId('dailydealemail_tabs');
    $this->setDestElementId('edit_form');
    $this->setTitle(Mage::helper('dailydeal')->__('Email Information'));
  }

  protected function _beforeToHtml(){	
    $this->addTab('form_section', array(
    'label'	 => Mage::helper('dailydeal')->__('Email Infomation'),
    'title'	 => Mage::helper('dailydeal')->__('Email Infomation'),
    'content'	 => $this->getLayout()->createBlock('dailydeal/adminhtml_mail_edit_tab_form')->toHtml(),
    ));
    return parent::_beforeToHtml();
  }
}
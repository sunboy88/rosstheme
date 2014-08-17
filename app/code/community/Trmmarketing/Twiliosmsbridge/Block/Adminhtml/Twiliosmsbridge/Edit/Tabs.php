<?php

class Trmmarketing_Twiliosmsbridge_Block_Adminhtml_Twiliosmsbridge_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('twiliosmsbridge_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('twiliosmsbridge')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('twiliosmsbridge')->__('Item Information'),
          'title'     => Mage::helper('twiliosmsbridge')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('twiliosmsbridge/adminhtml_twiliosmsbridge_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
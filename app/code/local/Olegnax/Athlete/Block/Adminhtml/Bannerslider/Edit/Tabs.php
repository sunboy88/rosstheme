<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Block_Adminhtml_Bannerslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('bannerslider_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('athlete')->__('Slide Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('athlete')->__('Slide Information'),
          'title'     => Mage::helper('athlete')->__('Slide Information'),
          'content'   => $this->getLayout()->createBlock('athlete/adminhtml_bannerslider_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
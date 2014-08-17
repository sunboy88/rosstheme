<?php

class Trmmarketing_Twiliosmsbridge_Block_Adminhtml_Twiliosmsbridge_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('twiliosmsbridge_form', array('legend'=>Mage::helper('twiliosmsbridge')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('twiliosmsbridge')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('twiliosmsbridge')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('twiliosmsbridge')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('twiliosmsbridge')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('twiliosmsbridge')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('twiliosmsbridge')->__('Content'),
          'title'     => Mage::helper('twiliosmsbridge')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getTwiliosmsbridgeData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTwiliosmsbridgeData());
          Mage::getSingleton('adminhtml/session')->setTwiliosmsbridgeData(null);
      } elseif ( Mage::registry('twiliosmsbridge_data') ) {
          $form->setValues(Mage::registry('twiliosmsbridge_data')->getData());
      }
      return parent::_prepareForm();
  }
}
<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Mail_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
  $form = new Varien_Data_Form();
  $form->setHtmlIdPrefix('dailydeal_');
  $this->setForm($form);
  $fieldset = $form->addFieldset('dailydeal_form', array('legend'=>Mage::helper('dailydeal_')->__('Email information')));

  if ( Mage::getSingleton('adminhtml/session')->getdailydealData() )
  {
    $data = Mage::getSingleton('adminhtml/session')->getDailydealData();
    Mage::getSingleton('adminhtml/session')->setdailydealData(null);
  } elseif ( Mage::registry('dailydeal_data') ) {
    $data = Mage::registry('dailydeal_data')->getData();
  }

  $fieldset->addField('email', 'text', array(
  'label'     => Mage::helper('dailydeal')->__('Email'),
  'required'  => false,
  'name'      => 'email',
  'width'       => '500px'
  ));

  $fieldset->addField('status', 'select', array(
  'label'     => Mage::helper('dailydeal')->__('Status'),
  'name'      => 'status',
  'values'    => array(
  array(
  'value'     => 1,
  'label'     => Mage::helper('dailydeal')->__('Enabled'),
  ),

  array(
  'value'     => 2,
  'label'     => Mage::helper('dailydeal')->__('Disabled'),
  ),
  ),
  ));

  $form->setValues($data);
  return parent::_prepareForm();
  }
}
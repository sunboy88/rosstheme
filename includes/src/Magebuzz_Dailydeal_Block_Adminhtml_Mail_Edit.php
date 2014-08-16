<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Mail_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
  public function __construct(){
  parent::__construct();

  $this->_objectId = 'id';
  $this->_blockGroup = 'dailydeal_mail';
  $this->_controller = 'adminhtml_dailydeal_mail';    
  $this->_updateButton('save', 'label', Mage::helper('dailydeal')->__('Save Email'));
  $this->_updateButton('delete', 'label', Mage::helper('dailydeal')->__('Delete Email'));

  $this->_addButton('saveandcontinue', array(
  'label'   => Mage::helper('adminhtml')->__('Save And Continue Edit'),
  'onclick' => 'saveAndContinueEdit()',
  'class'   => 'save',
  ), -100);

  $this->_formScripts[] = "
  function toggleEditor() {
  if (tinyMCE.getInstanceById('dailydeal_content') == null)
  tinyMCE.execCommand('mceAddControl', false, 'dailydeal_content');
  else
  tinyMCE.execCommand('mceRemoveControl', false, 'dailydeal_content');
  }
  function saveAndContinueEdit(){
  editForm.submit($('edit_form').action+'back/edit/');
  }
  ";
  }

  public function getHeaderText(){
  if(Mage::registry('dailydeal_data') && Mage::registry('dailydeal_data')->getId())
    return Mage::helper('dailydeal')->__("Edit Email '%s'", $this->htmlEscape(Mage::registry('dailydeal_data')->getEmail()));
  return Mage::helper('dailydeal')->__('Add Email');
  }
}
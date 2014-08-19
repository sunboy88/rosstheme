<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Import_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
    $csvFileExample = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'dailydeal/subscriber/subscribers.csv';
    $form = new Varien_Data_Form(array(
        'id' => 'edit_form',
        'action' => $this->getUrl('*/adminhtml_mail/saveImport'),
        'method' => 'post',
        'enctype' => 'multipart/form-data'
      )
    );
  $fieldset = $form->addFieldset('edit_form', array('legend'=>Mage::helper('dailydeal')->__('Import subscribers from a CSV file')));
    $fieldset->addField('subscriber_csv_file', 'file', array(
            'name'  => 'subscriber_csv_file',
            'label' => Mage::helper('dailydeal')->__('Choose CSV file to import'),
            'after_element_html' => Mage::helper('dailydeal')->__('<br/>A CSV file include some subscribers for example (<a href="%s">Download</a>)', $csvFileExample)
          )
    );
    $form->setUseContainer(true);
    $this->setForm($form);;
    return parent::_prepareForm();
  }
}
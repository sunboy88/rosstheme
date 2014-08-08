<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Dailydeal_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm(){
    $form = new Varien_Data_Form();
    $this->setForm($form);
    if(Mage::registry('dailydeal_data')){
      $product = Mage::getModel('catalog/product')->load(Mage::registry('dailydeal_data')->getProductId());
      Mage::registry('dailydeal_data')->setData('product_name',$product->getName());
      Mage::registry('dailydeal_data')->setData('product_price',$product->getPrice());
    }
    if (Mage::getSingleton('adminhtml/session')->getDailydealData()){
      $data = Mage::getSingleton('adminhtml/session')->getDailydealData();
      Mage::getSingleton('adminhtml/session')->setDailydealData(null);
    } elseif(Mage::registry('dailydeal_data')) {
      $data = Mage::registry('dailydeal_data')->getData();
    }
    
    $fieldset = $form->addFieldset('dailydeal_form', array('legend'=>Mage::helper('dailydeal')->__('Deal information')));

    $fieldset->addField('product_name', 'text', array(
      'label'     => Mage::helper('dailydeal')->__('Product Name'),
      'class'     => 'required-entry',
      'readonly'  => 'readonly',
      'name'      =>  'product_name',
      'note'      => '<input type="hidden" name="product_id" id="product_id" value="'. Mage::registry('dailydeal_data')->getProductId() .'">',    
  ));
    
    $fieldset->addField('product_price', 'text', array(
      'label'     => Mage::helper('dailydeal')->__('Product Price'),
      'text'  => Mage::registry('dailydeal_data')->getProductPrice(),
      'readonly'  =>  'readonly',
      'product_price' => 'product_price'
    ));
    
    $fieldset->addField('product_quantity', 'text', array(
      'label'     => Mage::helper('dailydeal')->__('Quantity in Stock'),      
      'text'  => Mage::registry('dailydeal_data')->getProductQuantity(),
      'readonly'  =>  'readonly',
      'product_price' => 'product_quantity'
    ));
    
    $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    $fieldset->addField('start_time', 'date', array(
      'label'   => Mage::helper('dailydeal')->__('Start Time'),
      'class'   => 'required-entry',
      'required'  => true,
      'name'    => 'start_time',
      'time'    => true,
      'format' => $outputFormat, 
      'image'  => $this->getSkinUrl('images/grid-cal.gif'),
      'style' => 'width: 140px;'
    ));

    $fieldset->addField('end_time', 'date', array(
      'label'   => Mage::helper('dailydeal')->__('End Time'),
      'class'   => 'required-entry',
      'required'  => true,
      'name'    => 'end_time',
      'time'    => true,
      'format' => $outputFormat, 
      'image'  => $this->getSkinUrl('images/grid-cal.gif'),
      'style' => 'width: 140px;'
    ));

    if (isset($data['start_time'])) {
      $data['start_time'] = Mage::app()->getLocale()->date($data['start_time'], Varien_Date::DATETIME_INTERNAL_FORMAT);     
    }
    if (isset($data['end_time'])) {
      $data['end_time'] = Mage::app()->getLocale()->date($data['end_time'], Varien_Date::DATETIME_INTERNAL_FORMAT);     
    }
    
    $fieldset->addField('deal_price','text',
      array(
        'label' => 'Deal Price',
        'name'  =>  'deal_price'
      )
    );
    
    $fieldset->addField('quantity','text',
      array(
        'label' => 'Quantity for sale via Daily Deal',
        'name'  =>  'quantity'
      )
    );
  
   $fieldset->addField('store_id','multiselect',array(
          'name'      => 'stores[]',
          'label'     => Mage::helper('dailydeal')->__('Store View'),
          'title'     => Mage::helper('dailydeal')->__('Store View'),
          'required'  => true,
          'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
      ));
  
    $fieldset->addField('status', 'select', array(
      'label'   => Mage::helper('dailydeal')->__('Status'),
      'name'    => 'status',
      'values'  => Mage::getSingleton('dailydeal/status')->getSettingStatusList(),
    ));
    
    $form->setValues($data);
    return parent::_prepareForm();
  }
}
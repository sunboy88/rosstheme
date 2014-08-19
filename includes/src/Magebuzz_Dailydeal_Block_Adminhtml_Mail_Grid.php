<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Mail_Grid extends Mage_Adminhtml_Block_Widget_Grid {
  public function __construct() {
  $this->setId('dailydealmailGrid');
  $this->setDefaultSort('deal_email_id');
  $this->setDefaultDir('ASC');
  $this->setSaveParametersInSession(true);
  $this->setUseAjax(true);    
  parent::__construct();
  }

  protected function _prepareCollection() {
  $collection = Mage::getModel('dailydeal/mail')->getCollection();
  $this->setCollection($collection);
  return parent::_prepareCollection();
  }

  protected function _prepareColumns() {
  $this->addColumn('deal_email_id', array(
  'header'  => Mage::helper('dailydeal')->__('ID'),
  'align'  =>'right',
  'width'  => '50px',
  'index'  => 'deal_email_id',
  ));

  $this->addColumn('customer_name', array(
  'header'  => Mage::helper('dailydeal')->__('Name'),
  'align'  =>'left',
  'index'  => 'customer_name',
  ));

  $this->addColumn('email', array(
  'header'  => Mage::helper('dailydeal')->__('Email'),
  'align'  =>'left',
  'index'  => 'email',
  ));

  $this->addColumn('status', array(
  'header'  => Mage::helper('dailydeal')->__('Status'),
  'align'  => 'left',
  'width'  => '80px',
  'index'  => 'status',
  'type'    => 'options',
  'options'  => array(
    1 => $this->__('Enabled'),
    2 => $this->__('Disabled')
  ),
  ));

  $this->addExportType('*/*/exportCsv', Mage::helper('dailydeal')->__('CSV'));
  $this->addExportType('*/*/exportXml', Mage::helper('dailydeal')->__('XML'));
  return parent::_prepareColumns();
  }

  protected function _prepareMassaction() {
  $this->setMassactionIdField('deal_email_id');
  $this->getMassactionBlock()->setFormFieldName('dailydeal');
  $this->getMassactionBlock()->addItem('delete', array(
  'label'    => Mage::helper('dailydeal')->__('Delete'),
  'url'      => $this->getUrl('*/*/massDelete'),
  'confirm'  => Mage::helper('dailydeal')->__('Are you sure?')
  ));

  $options = array(
    array('label' => $this->__('Enabled'), 'value' => 1),
    array('label' => $this->__('Disabled'), 'value' => 2),
  );
  
  $this->getMassactionBlock()->addItem('status', array(
  'label'=> Mage::helper('dailydeal')->__('Change status'),
  'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
  'additional' => array(
  'visibility' => array(
  'name' => 'status',
  'type' => 'select',
  'class' => 'required-entry',
  'label' => Mage::helper('dailydeal')->__('Status'),
  'values' => $options
  )
  )
  ));
  return $this;
  } 
}
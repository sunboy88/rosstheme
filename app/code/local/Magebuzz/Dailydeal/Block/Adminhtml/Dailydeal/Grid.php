<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Dailydeal_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct(){
    parent::__construct();
    $this->setId('dailydealGrid');
    $this->setDefaultSort('deal_id');
    $this->setDefaultDir('ASC');
    $this->setSaveParametersInSession(true);
    $this->setUseAjax(true);
  }

  protected function _prepareCollection(){
    $collection = Mage::getModel('dailydeal/deal')->getCollection();
    
    //update deal status everytime when admin user view the deal
    Mage::helper('dailydeal')->updateAllDealStatus();
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }

  protected function _prepareColumns(){
    $this->addColumn('deal_id', array(
      'header'  => Mage::helper('dailydeal')->__('ID'),
      'align'  =>'right',
      'width'  => '50px',
      'index'  => 'deal_id',
    ));

    $this->addColumn('title', array(
      'header'  => Mage::helper('dailydeal')->__('Title'),
      'align'  =>'left',
      'index'  => 'title',
    ));
    $this->addColumn('deal_price', array(
      'header'  => Mage::helper('dailydeal')->__('Deal Price'),
      'align'  =>'left',
      'index'  => 'deal_price',
      'type'  =>'number'
    ));
    $this->addColumn('quantity', array(
      'header'  => Mage::helper('dailydeal')->__('Quantity'),
      'align'  =>'left',
      'index'  => 'quantity',
      'type'  =>'number'
    ));
    $this->addColumn('start_time', array(
      'header'  => Mage::helper('dailydeal')->__('Start Time'),
      'width'  => '150px',
      'gmtoffset' => true,
      'type'  =>  'datetime',
      'index'  => 'start_time',
    ));

    $this->addColumn('end_time', array(
      'header'  => Mage::helper('dailydeal')->__('End Time'),
      'width'  => '150px',
      'type'  =>  'datetime',
      'gmtoffset' => true,
      'index'  => 'end_time',
    ));

    if (!Mage::app()->isSingleStoreMode()) {
      $this->addColumn('store_id', array(
        'header'        => Mage::helper('dailydeal')->__('Store View'),
        'index'         => 'store_id',
        'type'          => 'store',
        'store_all'     => true,
        'store_view'    => true,
        'sortable'      => false,
        'filter_condition_callback' => array($this, '_filterStoreCondition'),
      ));
    }

    $this->addColumn('status', array(
      'header' => Mage::helper('dailydeal')->__('Status'),
      'align'  => 'left',
      'width'  => '80px',
      'index'  => 'status',
      'type'   => 'options',
      'options'=>  Mage::getSingleton('dailydeal/status')->getStatusList()
    ));

    $this->addColumn('action',
      array(
      'header'  =>  Mage::helper('dailydeal')->__('Action'),
      'width'   => '100',
      'type'    => 'action',
      'getter'  => 'getId',
      'actions' => array(
        array(
          'caption' => Mage::helper('dailydeal')->__('Edit'),
          'url'   => array('base'=> '*/*/edit'),
          'field'   => 'id'
        )
      ),
      'filter'  => false,
      'sortable'  => false,
      'index'   => 'stores',
      'is_system' => true,
    ));

    $this->addColumn('report',
      array(
      'header'  =>  Mage::helper('dailydeal')->__('Report'),
      'width'   => '100',
      'type'    => 'action',
      'getter'  => 'getId',
      'actions' => array(
        array(
          'caption' => Mage::helper('dailydeal')->__('Report'),
          'url'   => array('base'=> '*/*/report'),
          'field'   => 'id'
        )
      ),
      'filter'  => false,
      'sortable'  => false,
      'index'   => 'stores',
      'is_system' => true,
    ));

    $this->addExportType('*/*/exportCsv', Mage::helper('dailydeal')->__('CSV'));
    $this->addExportType('*/*/exportXml', Mage::helper('dailydeal')->__('XML'));

    return parent::_prepareColumns();
  }

  protected function _prepareMassaction(){
    $this->setMassactionIdField('dailydeal_id');
    $this->getMassactionBlock()->setFormFieldName('dailydeal');

    $this->getMassactionBlock()->addItem('delete', array(
      'label'   => Mage::helper('dailydeal')->__('Delete'),
      'url'   => $this->getUrl('*/*/massDelete'),
      'confirm' => Mage::helper('dailydeal')->__('Are you sure?')
    ));

    $statuses = Mage::getSingleton('dailydeal/status')->getSettingStatusList();
    $options = array();
    foreach ($statuses as $key => $value) {
      # code...
      $item = array('label' => $value, 'value' => $key);
      $options[] = $item;
    }
    // array_unshift($statuses, array('label'=>'', 'value'=>''));
    $this->getMassactionBlock()->addItem('status', 
      array(
        'label'=> Mage::helper('dailydeal')->__('Change status'),
        'url' => $this->getUrl('*/*/massStatus', array('_current'=>true)),
        'additional' => array(
        'visibility' => array(
          'name'  => 'status',
          'type'  => 'select',
          'class' => 'required-entry',
          'label' => Mage::helper('dailydeal')->__('Status'),
          'values'=> $options
        )
      )
    ));
    return $this;
  }

  public function getRowUrl($row){
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

  public function getGridUrl()
  {
    return $this->getUrl('*/*/grid', array('_current'=>true));
  }
}
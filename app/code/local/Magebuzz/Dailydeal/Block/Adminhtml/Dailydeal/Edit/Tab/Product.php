<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Dailydeal_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('list_product_grid');
    $this->setDefaultSort('entity_id');
    $this->setUseAjax(true);
    if ($this->getTopic()) {
      $this->setDefaultFilter(array('in_products'=>1));
    }
  }

  protected function _addColumnFilterToCollection($column)
  {
    if ($this->getCollection()) {
      if ($column->getId() == 'websites') {
        $this->getCollection()->joinField('websites',
          'catalog/product_website',
          'website_id',
          'product_id=entity_id',
          null,
          'left');
      }
    }

    // Set custom filter for in product flag
    if ($column->getId() == 'in_products') {
      $productIds = $this->_getSelectedProducts();
      if (empty($productIds)) {
        $productIds = 0;
      }
      if ($column->getFilter()->getValue()) {
        $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
      } else {
        if($productIds) {
          $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
        }
      }
    } else {
      parent::_addColumnFilterToCollection($column);
    }
    return $this;
  }


  protected function _prepareCollection()
  {
    $collection = Mage::getResourceModel('catalog/product_collection')
    ->addAttributeToSelect('*');
    $this->setCollection($collection);
    parent::_prepareCollection();
    $this->getCollection()->addWebsiteNamesToResult();
    return $this;
  }

  protected function _prepareColumns()
  {    	
    $this->addColumn('deal_product', array(
    'header_css_class'  => 'a-center',
    'type'              => 'radio',
    'html_name'         => 'deal_product_ids[]',
    'values'            => $this->_getSelectedProducts(),
    'align'             => 'center',	
    'index'             => 'entity_id'
    ));

    $this->addColumn('entity_id', array(
    'header'    => Mage::helper('dailydeal')->__('ID'),
    'sortable'  => true,
    'width'     => 60,
    'index'     => 'entity_id'
    ));
    $this->addColumn('name', array(
    'header'    => Mage::helper('dailydeal')->__('Name'),
    'index'     => 'name'
    ));

    $this->addColumn('type', array(
    'header'    => Mage::helper('catalog')->__('Type'),
    'width'     => 100,
    'index'     => 'type_id',
    'type'      => 'options',
    'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
    ));

    $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
    ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
    ->load()
    ->toOptionHash();

    $this->addColumn('set_name', array(
    'header'    => Mage::helper('catalog')->__('Attrib. Set Name'),
    'width'     => 130,
    'index'     => 'attribute_set_id',
    'type'      => 'options',
    'options'   => $sets,
    ));

    $this->addColumn('status', array(
    'header'    => Mage::helper('catalog')->__('Status'),
    'width'     => 90,
    'index'     => 'status',
    'type'      => 'options',
    'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
    ));

    $this->addColumn('visibility', array(
    'header'    => Mage::helper('catalog')->__('Visibility'),
    'width'     => 90,
    'index'     => 'visibility',
    'type'      => 'options',
    'options'   => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
    ));

    $this->addColumn('sku', array(
    'header'    => Mage::helper('catalog')->__('SKU'),
    'width'     => 80,
    'index'     => 'sku'
    ));

    $this->addColumn('price', array(
    'header'        => Mage::helper('catalog')->__('Price'),
    'type'          => 'currency',
    'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
    'index'         => 'price'
    ));

    $this->addColumn('position', array(
    'header'            => Mage::helper('catalog')->__('Position'),
    'name'              => 'position',
    'type'              => 'number',
    'validate_class'    => 'validate-number',
    'index'             => 'position',
    'width'             => 60,
    'editable'          => true,
    'edit_only'         => true
    ));

    if (!Mage::app()->isSingleStoreMode()) {
      $this->addColumn('websites',
        array(
          'header'=> Mage::helper('catalog')->__('Websites'),
          'width' => '100px',
          'sortable'  => false,
          'index'     => 'websites',
          'type'      => 'options',
          'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
        ));
    }

    return parent::_prepareColumns();
  }
 /*  public function getRowUrl(){
    return;
  } */
  public function getGridUrl()
  {
    return $this->getData('grid_url')
    ? $this->getData('grid_url')
    : $this->getUrl('*/*/productgrid', array('_current'=>true,'id'=>$this->getRequest()->getParam('id')));
  }

  protected function _getSelectedProducts()
  {
    $products = $this->getProduct();
    if (!is_array($products)) {
      $products = array_keys($this->getSelectedRelatedProducts());
    }
    return $products;
  }

  public function getSelectedRelatedProducts()
  {
    $products = array();

    $productId = $this->getDeal()->getProductId();
    if($productId)
      $products[$productId] = array('position' => 0);
    return $products;
  }

  public function getDeal()
  {    
    return Mage::getModel('dailydeal/deal')
    ->load($this->getRequest()->getParam('id'));
  }
}
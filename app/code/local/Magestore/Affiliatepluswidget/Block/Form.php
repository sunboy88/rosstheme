<?php
class Magestore_Affiliatepluswidget_Block_Form extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}
	
	public function getWidget(){
		if (!$this->hasData('widget_model')){
			if (Mage::getSingleton('core/session')->getWidgetModel()){
				$widget = Mage::getSingleton('core/session')->getWidgetModel();
				Mage::getSingleton('core/session')->setWidgetModel(null);
			}else 
				$widget = Mage::registry('widget_model');
			if ($widget instanceof Varien_Object) $this->setData('widget_model',$widget);
			else $this->setData('widget_model',new Varien_Object($widget));
		}
		return $this->getData('widget_model');
	}
	
	public function getFormPostUrl(){
		return $this->getUrl('affiliatepluswidget/index/save',array('id' => $this->getWidget()->getId()));
	}
	
	public function getProductId(){
		if (!$this->hasData('product_id')){
			$productId = $this->getRequest()->getParam('product');
			if (!$productId) $productId = $this->getWidget()->getProductId();
			$product = Mage::getModel('catalog/product')
				->setStoreId(Mage::app()->getStore()->getId())
				->load($productId);
			$this->setData('product_id',$product->getId());
			$this->setData('product_name',$product->getName());
			$this->setData('product_url',$product->getProductUrl());
		}
		return $this->getData('product_id');
	}
	
	public function getProductName(){
		if (!$this->hasData('product_name')) $this->getProductId();
		return $this->getData('product_name');
	}
	
	public function getProductUrl(){
		if (!$this->hasData('product_url')) $this->getProductId();
		return $this->getData('product_url');
	}
	
	public function getStoreCategories(){
		return Mage::helper('catalog/category')->getStoreCategories(false,true);
	}
	
	public function getSizesOption(){
		return Mage::getSingleton('affiliatepluswidget/size')->getOptionArray();
	}
	
	public function getConfig($code, $store = null){
		return Mage::getStoreConfig("affiliateplus/widget/$code",$store);
	}
}
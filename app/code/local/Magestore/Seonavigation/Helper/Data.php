<?php

class Magestore_Seonavigation_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_params = array();
	protected $_attParams = array();
	protected $_filterVar = '';
	protected $_isRemove = false;
	
	public function getConfig($code, $store = null){
		return Mage::getStoreConfig("seoplus/seonavigation/$code",$store);
	}
	
	public function setParams($params, $filterVar = null, $isRemove = false){
		$this->_params = $params;
		$this->_attParams = array();
		$this->_filterVar = $filterVar;
		$this->_isRemove = $isRemove;
		return $this;
	}
	
	public function getUrlKey(){
		$params = $this->_params;
		$storeId = isset($params['store']) ? $params['store'] : Mage::app()->getStore()->getId();
		if (isset($params['q'])) unset($params['q']);
		if (isset($params['id'])) unset($params['id']);
		if (isset($params['store'])) unset($params['store']);
		
		$urlKeys = array();
		foreach ($params as $att => $val){
			if ($att == 'cat'){
				if ($catUrl = $this->getCategoryUrlPattern($val,$storeId)) $urlKeys[] = $catUrl;
			} else {
				$attribute = Mage::getModel('eav/entity_attribute')->load($att,'attribute_code');
				if (!$attribute->getId()) continue;
				if ($attribute->getFrontendInput() == 'price'){
					if ($priceUrl = $this->getPriceUrlPattern($attribute,$val,$storeId)) $urlKeys[] = $priceUrl;
				} else {
					if ($attUrl = $this->getAttributeUrlPattern($attribute,$val,$storeId)) $urlKeys[] = $attUrl;
				}
			}
		}
		$urlKey = implode($this->getConfig('separator',$storeId),$urlKeys);
		$urlKey = Mage::helper('seoplus/url')->format(trim($urlKey));
		$urlKey = str_replace(' ','-',strtolower($urlKey));
		
		return $this->getConfig('separator',$storeId).$urlKey;
	}
	
	public function getAttributeUrlPattern($attribute,$value,$storeId = null){
		$urlPattern = $this->getConfig('option',$storeId);
		if (!$urlPattern) return false;
		
		$valueLabel = $this->getValueLabel($value,$storeId);
		if (!$valueLabel) $valueLabel = $value;
		$this->_attParams[$attribute->getFrontendLabel()] = $valueLabel;
		
		return str_replace(array('{code}','{valueid}','{text}'),array($attribute->getAttributeCode(),$value,$valueLabel),$urlPattern);
	}
	
	public function getValueLabel($value,$storeId){
		try {
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('core_read');
			$select = $read->select()
				->from($resource->getTableName('eav/attribute_option_value'),array('value'))
				->where("option_id=?",$value)
				->where("store_id IN ('0','$storeId')")
				->order('store_id DESC');
			return $read->fetchOne($select);
		} catch (Exception $e){
			return false;
		}
	}
	
	public function getPriceUrlPattern($attribute,$value,$storeId = null){
		$urlPattern = $this->getConfig('price',$storeId);
		if (!$value || !$urlPattern) return false;
		list($from,$to) = explode('-',$value);
		if (!$from) $from = '0';
		if (!$to) $to = $this->__('infinity');
		$this->_attParams[$attribute->getFrontendLabel()] = $from . ',' . $to;
		return str_replace(array('{code}','{from}','{to}'),array($attribute->getAttributeCode(),$from,$to),$urlPattern);
	}
	
	public function getCategoryUrlPattern($catId,$storeId = null){
		$urlPattern = $this->getConfig('category',$storeId);
		$category = Mage::getModel('catalog/category')->load($catId);
		if (!$category->getId() || !$urlPattern) return false;
		$category->setStoreId($storeId);
		$this->_attParams[$this->__('Category')] = $category->getName();
		return str_replace(array('{name}','{id}'),array($category->getName(),$category->getId()),$urlPattern);
	}
	
	public function getClearUrl(){
		$params = $this->_params;
		foreach ($params as $key => $val) $params[$key] = null;
		if ($this->_filterVar) $params[$this->_filterVar] = null;
		$params['___store'] = null; $params['___from_store'] = null;
		$url = Mage::getUrl('*/*/*',array('_current'=>true,'_use_rewrite'=>true,'_escape'=>true,'_query'=>$params,'_nosid'=>true));
		$baseUrl = Mage::getBaseUrl();
		$url = ltrim(ltrim($url,'https'),'http');
		$baseUrl = ltrim(ltrim($baseUrl,'https'),'http');
		$clearUrl = substr($url,strlen($baseUrl));
		$seoNavigation = Mage::getModel('seonavigation/seonavigation')->load($clearUrl,'request_path');
		if ($seoNavigation->getId()) $clearUrl = $seoNavigation->getClearUrl();
		return $clearUrl;
	}
	
	public function getMetaTitle(){
		$params = $this->_params;
		$storeId = isset($params['store']) ? $params['store'] : Mage::app()->getStore()->getId();
		
		if (!isset($params['id'])) return '';
		$category = Mage::getModel('catalog/category')->load($params['id']);
		if (!$category->getId()) return '';
		
		$metaPattern = $this->getConfig('meta_title',$storeId);
		if (!$metaPattern) return '';
		
		$defaultTitle = $category->getMetaTitle();
		if (!$defaultTitle){
			$path = Mage::helper('catalog')->getBreadcrumbPath();
			$title = array();
			foreach ($path as $breadcrumb) $title[] = $breadcrumb['label'];
			$separator = ' ' . (string)Mage::getStoreConfig('catalog/seo/title_separator',$storeId) . ' ';
			$defaultTitle = join($separator,array_reverse($title));
		}
		if (!$defaultTitle && ($headBlock = Mage::app()->getLayout()->getBlock('head'))){
			if ($this->_isRemove)
				$defaultTitle = $headBlock->getDefaultTitle();
			else
				$defaultTitle = $headBlock->getTitle();
		}
		
		$attribute = '';
		$value = '';
		foreach ($this->_attParams as $att => $val){
			$attribute = $att;
			$value = $val;
		}
		return str_replace(array('{default}','{attribute}','{value}'),array($defaultTitle,$attribute,$value),$metaPattern);
	}
	
	public function getMetaKeywords(){
		$params = $this->_params;
		$storeId = isset($params['store']) ? $params['store'] : Mage::app()->getStore()->getId();
		
		if (!isset($params['id'])) return '';
		$category = Mage::getModel('catalog/category')->load($params['id']);
		if (!$category->getId()) return '';
		
		$metaPattern = $this->getConfig('meta_keywords',$storeId);
		if (!$metaPattern) return '';
		
		$defaultKeywords = $category->getMetaKeywords();
		if (!$defaultKeywords && ($headBlock = Mage::app()->getLayout()->getBlock('head'))){
			if ($this->_isRemove)
				$defaultKeywords = Mage::getStoreConfig('design/head/default_keywords',$storeId);
			else
				$defaultKeywords = $headBlock->getKeywords();
		}
		
		$attribute = '';
		$value = '';
		foreach ($this->_attParams as $att => $val){
			$attribute = $att;
			$value = $val;
		}
		//$attributes = array_filter(explode(' ',$attribute));
		//$values = array_filter(explode(' ',$value));
		//return str_replace(array('{default}','{attribute}','{value}'),array($defaultKeywords,implode(',',$attributes),implode(',',$values)),$metaPattern);
		return str_replace(array('{default}','{attribute}','{value}'),array($defaultKeywords,$attribute,$value),$metaPattern);
	}
	
	public function getMetaDescription(){
		$params = $this->_params;
		$storeId = isset($params['store']) ? $params['store'] : Mage::app()->getStore()->getId();
		
		if (!isset($params['id'])) return '';
		$category = Mage::getModel('catalog/category')->load($params['id']);
		if (!$category->getId()) return '';
		
		$metaPattern = $this->getConfig('meta_description',$storeId);
		if (!$metaPattern) return '';
		
		$defaultDescription = $category->getMetaDescription();
		if (!$defaultDescription && ($headBlock = Mage::app()->getLayout()->getBlock('head'))){
			if ($this->_isRemove)
				$defaultDescription = Mage::getStoreConfig('design/head/default_description',$storeId);
			else
				$defaultDescription = $headBlock->getDescription();
		}
		
		$attribute = '';
		$value = '';
		foreach ($this->_attParams as $att => $val){
			$attribute = $att;
			$value = $val;
		}
		return str_replace(array('{default}','{attribute}','{value}'),array($defaultDescription,$attribute,$value),$metaPattern);
	}
}
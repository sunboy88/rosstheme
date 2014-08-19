<?php

class Magestore_Seonavigation_Model_Seonavigation extends Mage_Core_Model_Abstract
{
	/**
	 * get module helper
	 * 
	 * @return Magestore_Seonavigation_Helper_Data
	 */
	public function getSeoHelper(){
		return Mage::helper('seonavigation');
	}
	
	public function _construct(){
		parent::_construct();
		$this->_init('seonavigation/seonavigation');
	}
	
	protected function _afterSave(){
		if ($this->getData('rewrite_generated')) return parent::_afterSave();
		$rewrite = Mage::getModel('core/url_rewrite')->load($this->getRewriteId());
		$rewrite->setStoreId($this->getStoreId())
			->setData('is_system',0)
			->setIdPath('seonavigation/'.$this->getId())
			->setTargetPath($this->prepareTargetPath());
		
		$urlSuffix = '';
		$params = unserialize($this->getQueryParams());
		$storeId = isset($params['store']) ? $params['store'] : Mage::app()->getStore()->getId();
		if ($params['id']){
			$urlSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix',$storeId);
		}
		if ($params['q']){
			$urlSuffix = Mage::helper('seoplus')->getConfig('url_suffix');
		}
		
		$length = strrpos($this->getClearUrl(),$urlSuffix);
		if ($urlSuffix && $length && $length + strlen($urlSuffix) == strlen($this->getClearUrl()))
			$requestPath = substr($this->getClearUrl(),0,$length);
		else
			$requestPath = $this->getClearUrl();
		$requestPath .= $this->getUrlKey();
		
		$continue = true;
		while ($continue){
			$existedRewrite = Mage::getResourceModel('core/url_rewrite_collection')
				->addFieldToFilter('request_path',$requestPath.$urlSuffix)
				->addFieldToFilter('store_id',$rewrite->getStoreId())
				->getFirstItem();
			//Fix CMS
			$cmsRewrite = Mage::getResourceModel('cms/page_collection')
				->addFieldToFilter('identifier',$requestPath.$urlSuffix)
				->getFirstItem()->getPageId();
			if (($existedRewrite->getId() && $existedRewrite->getId() != $rewrite->getId())||$cmsRewrite){
				$requestPath .= '-'.$this->getId();
			} else {
				$continue = false;
			}
		}
		
		$requestPath .= $urlSuffix;
		$rewrite->setData('request_path',$requestPath);
		try {
			$rewrite->save();
			$this->setRequestPath($requestPath)
				->setRewriteId($rewrite->getId())
				->setData('rewrite_generated',true)
				->save();
		} catch (Exception $e){}
		return parent::_afterSave();
	}
	
	public function prepareTargetPath(){
		$targetPath = $this->getClearUrl();
		$rewriteCollection = Mage::getResourceModel('core/url_rewrite_collection')
			->addFieldToFilter('request_path',$targetPath);
		$rewriteCollection->getSelect()
			->where("store_id IN ('0','{$this->getStoreId()}')")
			->order('store_id DESC');
		$rewrite = $rewriteCollection->getFirstItem();
		if ($rewrite && $rewrite->getId()) $targetPath = $rewrite->getTargetPath();
		
		$queryParams = unserialize($this->getQueryParams());
		if (isset($queryParams['q'])) unset($queryParams['q']);
		if (isset($queryParams['id'])) unset($queryParams['id']);
		if (isset($queryParams['store'])) unset($queryParams['store']);
		
		$suffixPath = '';
		$separator = strpos($targetPath,'?');
		if ($separator !== false){
			$suffixPath = substr($targetPath,$separator);
			$targetPath = substr($targetPath,0,$separator);
		}
		$targetPath = rtrim($targetPath,'/');
		if (count(explode('/',$targetPath))%2 == 0) $targetPath .= '/0';
		foreach ($queryParams as $key => $value)
			$targetPath .= '/' . $key . '/' . $value;
		return $targetPath . $suffixPath;
	}
}
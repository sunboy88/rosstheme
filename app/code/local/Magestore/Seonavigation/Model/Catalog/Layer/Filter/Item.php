<?php

class Magestore_Seonavigation_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
	/**
	 * get module helper
	 * 
	 * @return Magestore_Seonavigation_Helper_Data
	 */
	public function getSeoHelper(){
		return Mage::helper('seonavigation');
	}
	
	public function getUrl(){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seonavigation')){
			return parent::getUrl();
		}
		if ($this->notUseRewrite()) return parent::getUrl();
		try {
			$requestPath = trim(Mage::app()->getRequest()->getRequestString(),'/');
			$model = Mage::getModel('seonavigation/seonavigation')->load($requestPath,'request_path');
			$params = array();
			if ($model->getQueryParams()){
				$params = array_filter(unserialize($model->getQueryParams()));
			}
			$value = $this->getValue();
			if ($value == 0 && $value !== false) $value = '00';
			$params[$this->getFilter()->getRequestVar()] = $value;
			
			$rewrite = $this->createRewriteUrl($params);
			if ($rewrite && $rewrite->getId()){
				return Mage::getUrl(null,array('_direct' => $rewrite->getRequestPath()));
			}
		} catch (Exception $e){
			
		}
		return parent::getUrl();
	}
	
	public function getRemoveUrl(){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seonavigation')){
			return parent::getRemoveUrl();
		}
		if ($this->notUseRewrite()) return parent::getRemoveUrl();
		try {
			$requestPath = trim(Mage::app()->getRequest()->getRequestString(),'/');
			$model = Mage::getModel('seonavigation/seonavigation')->load($requestPath,'request_path');
			if (!$model->getId()) return parent::getRemoveUrl();
			
			$params = array();
			if ($model->getQueryParams()){
				$params = array_filter(unserialize($model->getQueryParams()));
			}
			if (isset($params[$this->getFilter()->getRequestVar()])) unset($params[$this->getFilter()->getRequestVar()]);
			if (isset($params['q'])) unset($params['q']);
			if (isset($params['id'])) unset($params['id']);
			if (isset($params['store'])) unset($params['store']);
			
			if (count($params)){
				$rewrite = $this->createRewriteUrl($params,true);
				if ($rewrite && $rewrite->getId()){
					return Mage::getUrl(null,array('_direct' => $rewrite->getRequestPath()));
				}
			} elseif ($model->getClearUrl()) {
				return Mage::getUrl(null,array('_direct' => $model->getClearUrl()));
			}
		} catch (Exception $e){
			
		}
		return parent::getRemoveUrl();
	}
	
	public function notUseRewrite(){
		return (!$this->getSeoHelper()->getConfig('enable'))
			|| (Mage::app()->getRequest()->getRequestedRouteName() == 'catalogsearch'
				&& !Mage::getStoreConfig('seoplus/general/enable'));
	}
	
	public function createRewriteUrl($params, $isRemove = false){
		$storeId = Mage::app()->getStore()->getId();
		
		$queryParams = $params;
		$queryParams['store'] = (int)$storeId;
		if (Mage::app()->getRequest()->getParam('q'))
			$queryParams['q'] = Mage::app()->getRequest()->getParam('q');
		elseif ($category = Mage::getSingleton('catalog/layer')->getCurrentCategory())
			$queryParams['id'] = $category->getId();
		
		$this->getSeoHelper()->setParams($queryParams,$this->getFilter()->getRequestVar(),$isRemove);
		$queryParams = serialize($queryParams);
		$seoNavigation = Mage::getModel('seonavigation/seonavigation')->load($queryParams,'query_params');
		if ($seoNavigation->getId() && $this->getSeoHelper()->getConfig('cache'))
			return $seoNavigation;
		
		$seoNavigation->setUrlKey($this->getSeoHelper()->getUrlKey())
			->setClearUrl($this->getSeoHelper()->getClearUrl())
			->setQueryParams($queryParams)
			->setStoreId($storeId);
		if (!$isRemove || (!$seoNavigation->getMetaTitle() && !$seoNavigation->getMetaKeywords() && !$seoNavigation->getMetaDescription()))
			$seoNavigation->setMetaTitle($this->getSeoHelper()->getMetaTitle())
				->setMetaKeywords($this->getSeoHelper()->getMetaKeywords())
				->setMetaDescription($this->getSeoHelper()->getMetaDescription());
		$seoNavigation->save();
		
		return $seoNavigation;
	}
}

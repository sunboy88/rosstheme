<?php

class Magestore_Seobytag_Model_Tag_Tag extends Mage_Tag_Model_Tag
{
	/**
	 * get seobytag module helper
	 * 
	 * @return Magestore_Seobytag_Helper_Data
	 */
    public function getSeoHelper(){
        return Mage::helper('seobytag');
    }
    
    public function getTaggedProductsUrl(){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seobytag')){
			return parent::getTaggedProductsUrl();
		}
        $tagId = $this->getTagId();
        $storeId = Mage::app()->getStore()->getId();
        if (!$storeId) $storeId = $this->getFirstStoreId();
    	if (!$this->getSeoHelper()->getConfig('enable'))
			return parent::getTaggedProductsUrl();
    	
        $rewrite = Mage::getModel('core/url_rewrite')->loadByIdPath("seobytag/$storeId/$tagId");
        
        if ($rewrite->getId() && $this->getSeoHelper()->getConfig('cache'))
        	return Mage::getUrl(null,array('_direct' => $rewrite->getRequestPath()));
        
        try {
        	$this->generateUrlKey()->save();
        	$rewrite = $this->createRewrite();
        	if ($rewrite && $rewrite->getId())
        		return Mage::getUrl(null,array('_direct' => $rewrite->getRequestPath()));
        } catch (Exception $e){}
        return parent::getTaggedProductsUrl();
    }
    
    public function generateUrlKey(){
    	$urlKey = Mage::helper('seoplus/url')->format($this->getName());
		$urlKey = str_replace(' ','-',strtolower($urlKey));
		$this->setUrlKey($urlKey);
    	return $this;
    }
    
    public function generateMetaData(){
    	$urlKey = Mage::helper('seoplus/url')->format($this->getName());
		$urlKey = str_replace(' ','-',strtolower($urlKey));
		$this->setUrlKey($urlKey);
		
		$storeId = Mage::app()->getStore()->getId();
        if (!$storeId) $storeId = $this->getFirstStoreId();
		
		$title = $this->getName();
		$ntitle = Mage::helper('seoplus/url')->format($this->getName());
		
		$this->setMetaTitle(str_replace(array('{tag}','{ftag}'),array($title,$ntitle),$this->getSeoHelper()->getConfig('meta_title',$storeId)));
		
		$words = str_replace(' ',',',$title);
		$nwords = str_replace(' ',',',$ntitle);
		$this->setMetaKeywords(str_replace(array('{tag}','{ftag}','{words}','{fwords}'),array($title,$ntitle,$words,$nwords),$this->getSeoHelper()->getConfig('meta_keywords',$storeId)));
		
		$this->setMetaDescription(str_replace(array('{tag}','{ftag}'),array($title,$ntitle),$this->getSeoHelper()->getConfig('meta_description',$storeId)));
		return $this;
    }
    
    public function createRewrite(){
    	$tagId = $this->getTagId();
    	$storeId = Mage::app()->getStore()->getId();
        if (!$storeId) $storeId = $this->getFirstStoreId();
    	$urlRewrite = Mage::getModel('core/url_rewrite')->loadByIdPath("seobytag/$storeId/$tagId");
    	$urlRewrite->addData(array(
			'store_id'	=> $storeId,
			'id_path'	=> "seobytag/$storeId/$tagId",
			'target_path'	=> $this->getSeoHelper()->prepareTargetPath($tagId),
			'is_system'		=> 0,
		));
		$requestPath = $this->getSeoHelper()->getConfig('url_key_prefix',$storeId).$this->getUrlKey();
		$requestSuffix = $this->getSeoHelper()->getConfig('url_suffix',$storeId);
		$continue = true;
		while ($continue){
			$existedRewrite = Mage::getResourceModel('core/url_rewrite_collection')
				->addFieldToFilter('request_path',$requestPath.$requestSuffix)
				->addFieldToFilter('store_id',$urlRewrite->getStoreId())
				->getFirstItem();
			// fix CMS
			$cmsRewrite = Mage::getResourceModel('cms/page_collection')
			->addFieldToFilter('identifier',$requestPath.$requestSuffix)
			->getFirstItem()->getPageId();
			if (($existedRewrite->getId() && $existedRewrite->getId() != $urlRewrite->getId())||$cmsRewrite){
				$requestPath .= '-'.$tagId;
			} else {
				$continue = false;
			}
		}
		$requestPath .= $requestSuffix;
		$urlRewrite->setData('request_path',$requestPath)->save();
		return $urlRewrite;
    }
}

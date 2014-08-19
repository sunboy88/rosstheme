<?php

class Magestore_Seoplus_Model_Observer
{
	/**
	 * get module helper
	 * 
	 * @return Magestore_Seoplus_Helper_Data 
	 */
	public function getHelper(){
		return Mage::helper('seoplus');
	}
	
	public function catalogsearchQuerySaveBefore($observer){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seoplus')){return;}
		$query = $observer->getEvent()->getDataObject();
		$urlKey = Mage::helper('seoplus/url')->format($query->getQueryText());
		$urlKey = str_replace(' ','-',strtolower($urlKey));
		$query->setUrlKey($urlKey);
		
		$title = $query->getQueryText();
		$ntitle = Mage::helper('seoplus/url')->format($title);
		
		$query->setMetaTitle(str_replace(array('{query}','{fquery}'),array($title,$ntitle),$this->getHelper()->getConfig('meta_title',$query->getStoreId())));
		
		$words = str_replace(' ',',',$title);
		$nwords = str_replace(' ',',',$ntitle);
		$query->setMetaKeywords(str_replace(array('{query}','{fquery}','{words}','{fwords}'),array($title,$ntitle,$words,$nwords),$this->getHelper()->getConfig('meta_keywords',$query->getStoreId())));
		
		$query->setMetaDescription(str_replace(array('{query}','{fquery}'),array($title,$ntitle),$this->getHelper()->getConfig('meta_description',$query->getStoreId())));
	}
	
	public function catalogsearchQuerySaveAfter($observer){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seoplus')){return;}
		$query = $observer->getEvent()->getDataObject();
		$urlRewrite = Mage::getModel('core/url_rewrite')->loadByIdPath("seoplus/{$query->getId()}");
		$urlRewrite->addData(array(
			'store_id'	=> $query->getData('store_id'),
			'id_path'	=> 'seoplus/'.$query->getId(),
			'target_path'	=> $this->getHelper()->prepareTargetPath($this->getQueryText($query)),
			'is_system'		=> 0,
		));
		$requestPath = $this->getHelper()->getConfig('url_key_prefix',$query->getStoreId()).$query->getUrlKey();
		$requestSuffix = $this->getHelper()->getConfig('url_suffix',$query->getStoreId());
		$continue = true;
		while ($continue){
			$existedRewrite = Mage::getResourceModel('core/url_rewrite_collection')
				->addFieldToFilter('request_path',$requestPath.$requestSuffix)
				->addFieldToFilter('store_id',$urlRewrite->getStoreId())
				->getFirstItem();
			//Fix CMS
			$cmsRewrite = Mage::getResourceModel('cms/page_collection')
				->addFieldToFilter('identifier',$requestPath.$requestSuffix)
				->getFirstItem()->getPageId();
			if (($existedRewrite->getId() && $existedRewrite->getId() != $urlRewrite->getId())||$cmsRewrite){
				$requestPath .= '-'.$query->getId();
			} else {
				$continue = false;
			}
		}
		$requestPath .= $requestSuffix;
		$urlRewrite->setData('request_path',$requestPath);
		try {
			$urlRewrite->save();
		} catch (Exception $e) {}
	}
	
	public function getQueryText($query){
		if ($queryText = Mage::app()->getRequest()->getParam('q'))
			return $queryText;
		return $query->getQueryText();
	}
	
	public function catalogsearchResultIndex($observer){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seoplus')){return;}
		if (!$this->getHelper()->getConfig('enable')) return $this;
		$action = $observer->getEvent()->getControllerAction();
		
		$query = Mage::helper('catalogsearch')->getQuery();
		if (Mage::helper('catalogsearch')->isMinQueryLength()) return $this;
		if ($queryText = $query->getQueryText()){
			$query->setStoreId(Mage::app()->getStore()->getId());
			try {
				$query->save();
			} catch (Exception $e){}
			$rewrite = Mage::getModel('core/url_rewrite')->loadByIdPath("seoplus/{$query->getId()}");
			if ($rewrite->getId() && strpos($action->getRequest()->getRequestString(),'catalogsearch/result') !== false){
				$url = Mage::getUrl(null,array('_direct' => $rewrite->getRequestPath()));
				$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
				$action->getResponse()->setRedirect($url);
			}
		}
	}
	
	public function cmsIndexNoroute($observer){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seoplus')){return;}
		if (!$this->getHelper()->getConfig('enable')) return $this;
		$action = $observer->getEvent()->getControllerAction();
		$requestPath = trim($action->getRequest()->getRequestString(),'/');
		
		$urlRewrite = Mage::getResourceModel('core/url_rewrite_collection')
			->addFieldToFilter('request_path',$requestPath)
			->addFieldToFilter('id_path',array('like' => 'seoplus/%'))
			->getFirstItem();
		if ($urlRewrite && $urlRewrite->getId()){
			$url = Mage::getUrl(null,array('_direct' => $urlRewrite->getTargetPath()));
			$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
			$action->getResponse()->setRedirect($url);
		}
	}
}
<?php

class Magestore_Seoplus_Block_CatalogSearch_Term extends Mage_CatalogSearch_Block_Term
{
	protected function _loadTerms(){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seoplus')){
			return parent::_loadTerms();
		}
		if (empty($this->_terms)){
			$this->_terms = array();
			$terms = Mage::getResourceModel('catalogsearch/query_collection');
			$terms->getSelect()->reset(Zend_Db_Select::FROM)->distinct(true)
				->from(
					array('main_table'=>$terms->getTable('catalogsearch/search_query')),
					array('name'=>"if(ifnull(synonym_for,'')<>'', synonym_for, query_text)",'num_results','query_id','popularity')
				);
			$terms->addStoreFilter(Mage::app()->getStore()->getId());
			$terms->getSelect()->where('num_results > 0')->order(array('popularity desc','name'));
			
			$terms = $terms->setOrder('popularity', 'DESC')
				->setPageSize(100)
				->load()
				->getItems();
			
			if( count($terms) == 0 ) {
				return $this;
			}
			$this->_maxPopularity = reset($terms)->getPopularity();
			$this->_minPopularity = end($terms)->getPopularity();
			$range = $this->_maxPopularity - $this->_minPopularity;
			$range = ( $range == 0 ) ? 1 : $range;
			foreach ($terms as $term) {
				if( !$term->getPopularity() ) continue;
				$term->setRatio(($term->getPopularity()-$this->_minPopularity)/$range);
				$temp[$term->getName()] = $term;
				$termKeys[] = $term->getName();
			}
			natcasesort($termKeys);
			foreach ($termKeys as $termKey) {
				$this->_terms[$termKey] = $temp[$termKey];
			}
		}
		return $this;
	}
	
	/**
	 * get module helper
	 * 
	 * @return Magestore_Seoplus_Helper_Data 
	 */
	public function getHelper(){
		return Mage::helper('seoplus');
	}
	
	public function getSearchUrl($obj){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seoplus')){
			return parent::getSearchUrl($obj);
		}
		$url = Mage::getModel('core/url');
		if ($this->getHelper()->getConfig('enable')){
			$rewrite = Mage::getModel('core/url_rewrite')->loadByIdPath("seoplus/{$obj->getQueryId()}");				
			if ((!$this->getHelper()->getConfig('cache') || !$rewrite->getId()) && $obj->getQueryId()){
				try {
					$model = Mage::getModel('catalogsearch/query')->load($obj->getQueryId())
						->setId($obj->getQueryId())->save();
					$rewrite->loadByIdPath("seoplus/{$model->getId()}");
				} catch (Exception $e){
					$url->setQueryParam('q',$obj->getName());
					return $url->getUrl('catalogsearch/result');
				}
			}
			if ($rewrite->getTargetPath() != $this->getHelper()->prepareTargetPath($obj->getName())){
				try {
					$rewrite->setTargetPath($this->getHelper()->prepareTargetPath($obj->getName()))
						->save();
				} catch (Exception $e){
					$url->setQueryParam('q',$obj->getName());
					return $url->getUrl('catalogsearch/result');
				}
			}
			return $url->getUrl(null,array('_direct' => $rewrite->getRequestPath()));
		}
		$url->setQueryParam('q',$obj->getName());
		return $url->getUrl('catalogsearch/result');
	}
}
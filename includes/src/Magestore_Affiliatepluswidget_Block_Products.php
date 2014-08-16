<?php
class Magestore_Affiliatepluswidget_Block_Products extends Mage_Catalog_Block_Product_Abstract
{
	protected $_widget_data = array();
	protected $_collection = null;
	
	public function getCollection(){
		if (is_null($this->_collection)){
			$this->_collection = Mage::getResourceModel('catalog/product_collection')
				->setStoreId(Mage::app()->getStore()->getId())
				->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
				->addAttributeToFilter('entity_id',array('in' => $this->getProductIds()))
				->addMinimalPrice()
				->addTaxPercents()
				->addStoreFilter();
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_collection);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($this->_collection);
			if (isset($this->_widget_data['search']) && $this->_widget_data['search'])
				$this->_collection->addAttributeToFilter('name',array('like' => "%{$this->_widget_data['search']}%"));
			$size = $this->_widget_data['rows'] * $this->_widget_data['columns'];
			$this->_collection->setPageSize($size)->setCurPage(1);
		}
		return $this->_collection;
	}
	
	public function getProductIds(){
		if (isset($this->_widget_data['product_id']) && $this->_widget_data['product_id'])
			return array($this->_widget_data['product_id']);
		$categories = isset($this->_widget_data['category_ids']) ? $this->_widget_data['category_ids'] : array();
		$productIds = array();
		$category = Mage::getModel('catalog/category');
		foreach ($categories as $categoryId){
			$category->load($categoryId);
			$productIds = array_merge($productIds,$category->getProductCollection()->getAllIds());
		}
		return $productIds;
	}
	
	public function getWidgetData(){
		return $this->_widget_data;
	}
	
	public function getAccount(){
		if (!$this->hasData('affiliateplus_account')){
			if (isset($this->_widget_data['account_id']) && $this->_widget_data['account_id'])
				$account = Mage::getModel('affiliateplus/account')->load($this->_widget_data['account_id']);
			else
				$account = Mage::getSingleton('affiliateplus/session')->getAccount();
			$this->setData('affiliateplus_account',$account);
		}
		return $this->getData('affiliateplus_account');
	}
	
	protected function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate("affiliatepluswidget/products.phtml");
		$this->_prepareWidgetData();
		return $this;
    }
    
    protected function _prepareWidgetData(){
    	$widgetData = array_merge(array(
            'columns'   => '',
            'rows'      => '',
            'width'     => '',
            'height'    => '',
            'is_image'  => '',
            'textlink'  => '',
            'is_price'  => '',
            'textbody'  => '',
            'is_rated'  => '',
            'is_short_desc' => '',
        ), $this->getRequest()->getParams());
    	
    	if (isset($widgetData['category_ids']) && is_array($widgetData['category_ids'])) $widgetData['category_ids'] = $widgetData['category_ids'][0];
    	if (isset($widgetData['category_ids']) && is_string($widgetData['category_ids'])) $widgetData['category_ids'] = explode(',',$widgetData['category_ids']);
    	
    	if (isset($widgetData['widget_size']) && $widgetData['widget_size']){
    		$sizes = Mage::getSingleton('affiliatepluswidget/size')->getSize($widgetData['widget_size']);
    		$widgetData['height'] = $sizes['height'];
    		$widgetData['width'] = $sizes['width'];
    		$widgetData['rows'] = $sizes['rows'];
    		$widgetData['columns'] = $sizes['columns'];
    	}
    	
    	$this->_widget_data = $widgetData;
    	return $this;
    }
    
	public function getConfig($code, $store = null){
		return Mage::getStoreConfig("affiliateplus/widget/$code",$store);
	}
	
	protected function _getUrlSuffix(){
		if (!$this->hasData('url_suffix')){
			$params['acc'] = $this->getAccount()->getIdentifyCode();
			if (Mage::app()->getStore()->getId() != Mage::app()->getDefaultStoreView()->getId())
				$params['___store'] = Mage::app()->getStore()->getCode();
			$this->setData('url_suffix',http_build_query($params));
		}
		return $this->getData('url_suffix');
	}
	
	protected function _initReviewsHelperBlock(){
		parent::_initReviewsHelperBlock();
		if ($this->_reviewsHelperBlock)
			$this->_reviewsHelperBlock->addTemplate('widget','affiliatepluswidget/summary.phtml');
		return $this->_reviewsHelperBlock;
	}
	
	public function getProductUrl($product){
		return $product->getProductUrl() . '?' . $this->_getUrlSuffix();
	}
}
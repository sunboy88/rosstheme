<?php
class Magestore_Affiliatepluswidget_Block_Link extends Mage_Core_Block_Template
{
	public function getWidgetUrl(){
		return $this->getUrl('affiliatepluswidget/index/product',array('id' => $this->getRequest()->getParam('id')));
	}
	
	public function getCustomizeUrl(){
		return $this->getUrl('affiliatepluswidget/index/new',array('product' => $this->getRequest()->getParam('id')));
	}
	
	public function getDefaultParams(){
		if (!$this->hasData('default_params')){
			$this->setData('default_params',array(
				'product_id'	=> $this->getRequest()->getParam('id'),
				'is_image'		=> 1,
				'is_price'		=> 1,
				'is_rated'		=> 1,
				'widget_size'	=> '168x145',
				'background'	=> $this->getConfig('background'),
				'border'		=> $this->getConfig('border'),
				'textheader'	=> $this->getConfig('textheader'),
				'textlink'		=> $this->getConfig('textlink'),
				'textbody'		=> $this->getConfig('textbody'),
			));
		}
		return $this->getData('default_params');
	}
	
	public function getHtmlCode(){
		$params = $this->getDefaultParams();
		return Mage::helper('affiliatepluswidget')->getWidgetCode($params);
	}
	
	public function getConfig($code, $store = null){
		return Mage::getStoreConfig("affiliateplus/widget/$code",$store);
	}
	
	protected function _toHtml(){
		if (Mage::helper('affiliatepluswidget')->disableMenu())
			return '';
		return parent::_toHtml();
	}
}

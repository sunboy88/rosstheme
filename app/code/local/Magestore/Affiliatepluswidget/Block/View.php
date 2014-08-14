<?php
class Magestore_Affiliatepluswidget_Block_View extends Mage_Core_Block_Template
{
	protected $_widget_data = array();
	
	public function getWidgetData(){
		return $this->_widget_data;
	}
	
	public function getAccount(){
		if (isset($this->_widget_data['account_id']) && $this->_widget_data['account_id'])
			return Mage::getModel('affiliateplus/account')->load($this->_widget_data['account_id']);
		return Mage::getSingleton('affiliateplus/session')->getAccount();
	}
	
	protected function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate("affiliatepluswidget/view.phtml");
		$this->_prepareWidgetData();
		$productsBlock = $this->getLayout()->createBlock('affiliatepluswidget/products','affiliatepluswidget_products');
		$this->setChild('affiliatepluswidget_products',$productsBlock);
		return $this;
    }
    
    protected function _prepareWidgetData(){
    	$widgetData = array_merge(array(
            'border'    => '',
            'textbody'  => '',
            'search'    => '',
            'background'    => '',
            'textlink'  => '',
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
}
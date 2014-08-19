<?php
class Magestore_Affiliatepluswidget_Block_Widget extends Mage_Core_Block_Template
{
	protected $_widget_data = array();
	
	public function getWidgetData(){
		return $this->_widget_data;
	}
	
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate("affiliatepluswidget/widget.phtml");
		$this->_prepareWidgetData();
		return $this;
    }
    
    protected function _prepareWidgetData(){
    	$widgetData = array_merge(array(
            'category_ids'  => '',
            'widget_size'   => '',
        ), $this->getRequest()->getParams());
    	
    	if (is_array($widgetData['category_ids'])) $widgetData['category_ids'] = $widgetData['category_ids'][0];
    	if (is_string($widgetData['category_ids'])) $widgetData['category_ids'] = explode(',',$widgetData['category_ids']);
    	
    	if ($widgetData['widget_size']){
    		$sizes = Mage::getSingleton('affiliatepluswidget/size')->getSize($widgetData['widget_size']);
    		$widgetData['height'] = $sizes['height'];
    		$widgetData['width'] = $sizes['width'];
    		$widgetData['rows'] = $sizes['rows'];
    		$widgetData['columns'] = $sizes['columns'];
    	}
    	
    	$this->_widget_data = $widgetData;
    	return $this;
    }
    
    public function getJsonUrl(){
    	return Mage::helper('core')->jsonEncode(array(
    		'url'	=> $this->getUrl('affiliatepluswidget/index/widgetview',array('_query' => $this->getRequest()->getParams()))
    	));
    }
}
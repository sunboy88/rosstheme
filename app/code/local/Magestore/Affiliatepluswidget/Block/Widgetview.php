<?php
class Magestore_Affiliatepluswidget_Block_Widgetview extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate("affiliatepluswidget/widgetview.phtml");
		
		$widgetBlock = $this->getLayout()->createBlock('affiliatepluswidget/view','widget_view');
		$this->setChild('widget_view',$widgetBlock);
		
		return $this;
    }
    
    public function getWidgetHtml(){
    	return $this->getChildHtml('widget_view');
    }
}
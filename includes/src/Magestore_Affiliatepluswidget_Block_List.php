<?php
class Magestore_Affiliatepluswidget_Block_List extends Mage_Core_Block_Template
{
	protected function _construct(){
		parent::_construct();
		$account = Mage::getSingleton('affiliateplus/session')->getAccount();
		$collection = Mage::getResourceModel('affiliatepluswidget/widget_collection')
			->addFieldToFilter('account_id',$account->getId());
		$this->setCollection($collection);
	}
	
	public function _prepareLayout(){
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('page/html_pager','widget_pager')->setCollection($this->getCollection());
		$this->setChild('widget_pager',$pager);
		
		$grid = $this->getLayout()->createBlock('affiliateplus/grid','widget_grid');
		
		// prepare column
		$grid->addColumn('id',array(
			'header'	=> $this->__('No.'),
			'align'		=> 'left',
			'render'	=> 'getNoNumber',
            'width'     => '51px'
		));
		
		$grid->addColumn('name',array(
			'header'	=> $this->__('Name'),
			'align'		=> 'left',
			'render'	=> 'getNameEdit',
		));
		
		$grid->addColumn('widgetcode',array(
			'header'	=> $this->__('Widget Code'),
			'align'		=> 'left',
			'width'		=> '310px',
			'render'	=> 'getWidgetCode',
		));
		
		$grid->addColumn('delete',array(
			'header'	=> $this->__('Action'),
			'align'		=> 'left',
			'type'		=> 'action',
			'action'	=> array(
				'label'	=> $this->__('Delete'),
				'url'	=> 'affiliatepluswidget/index/delete',
				'name'	=> 'id',
				'field'	=> 'widget_id',
			),
            'render'    => 'getActionLink',
            'width'     => '88px'
		));
		
		$this->setChild('widget_grid',$grid);
		return $this;
    }
    
    public function getActionLink($row) {
        $html  = '<a href="' . $this->getUrl('affiliatepluswidget/index/edit',array('id' => $row->getId()));
        $html .= '" title="'. $this->__('Edit') .'">'. $this->__('Edit') . '</a> | ';
        $html .= '<a href="' . $this->getUrl('affiliatepluswidget/index/delete', array('id' => $row->getWidgetId()));
        $html .= '" title="'. $this->__('Delete') .'" onclick="return deleteWidget();">'. $this->__('Delete') .'</a>';
        return $html;
    }
    
    public function getNoNumber($row){
    	return sprintf('#%d',$row->getId());
    }
    
    public function getNameEdit($row){
    	return sprintf('<a href="%s">%s</a>'
    		,$this->getUrl('affiliatepluswidget/index/edit',array('id' => $row->getId()))
    		,$row->getName());
    }
    
    public function getWidgetCode($row){
    	return sprintf('<textarea onclick="this.select()" cols="55" rows="5" readonly>%s</textarea>',$row->getWidgetCode());
    }
    
    public function getPagerHtml(){
    	return $this->getChildHtml('widget_pager');
    }
    
    public function getGridHtml(){
    	return $this->getChildHtml('widget_grid');
    }
    
    protected function _toHtml(){
    	$this->getChild('widget_grid')->setCollection($this->getCollection());
    	return parent::_toHtml();
    }
}
<?php
class Magestore_Onestepcheckout_Block_Adminhtml_Countrygrid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_country';
		$this->_blockGroup = 'onestepcheckout';
		$this->_headerText = Mage::helper('onestepcheckout')->__('Update Country Postcode Database');

		$this->_addButton('importcountry', array(
			'label'		=> Mage::helper('onestepcheckout')->__('Update Selected Database'),
			'onclick'	=> 'getLinkImport(\''.$this->linkImport().'\')',
			'class'		=> 'save',
		),-100);		
		
		$this->_addButtonLabel = Mage::helper('onestepcheckout')->__('Upload New Database Version');
		parent::__construct();
		// $this->_removeButton('add');						
	}
    public function linkImport()
    {
		$link = Mage::getSingleton('adminhtml/url')->getUrl('onestepcheckoutadmin/adminhtml_country/showCountryIp',array(
			'website' => $this->getRequest()->getParam('website'),		
			// '_query'  => array('isAjax'  => 'true')
		));
		return $link;
	}
}
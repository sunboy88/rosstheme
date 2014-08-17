<?php
class Magestore_Onestepcheckout_Block_Adminhtml_Geoipgrid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {  
		$this->_controller = 'adminhtml_geoip';
		$this->_blockGroup = 'onestepcheckout';
		$this->_headerText = Mage::helper('onestepcheckout')->__('Update GeoIP Database');
		
		// $this->_addButton('importcountry', array(
				// 'label'		=> Mage::helper('onestepcheckout')->__('Import Geoip Database Version1.0'),				
				// 'onclick'	=> 'importCountryIp(\''.$this->linkImportGeoip().'\')',
				// 'class'		=> 'save',
			// ),-100);		
			
		$this->_addButtonLabel = Mage::helper('onestepcheckout')->__('Upload New GeoIP Database Version');
		
		parent::__construct();				
    }
	
	public function linkImportGeoip()
	{
		$link = Mage::getSingleton('adminhtml/url')->getUrl('onestepcheckoutadmin/adminhtml_geoip/showGeoip',array(
			'website' => $this->getRequest()->getParam('website'),		
			'_query'  => array('isAjax'  => 'false'),
			'version'  => '1',
		));
		return $link;
	}
	
}
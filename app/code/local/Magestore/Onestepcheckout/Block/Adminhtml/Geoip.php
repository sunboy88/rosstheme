<?php
class Magestore_Onestepcheckout_Block_Adminhtml_Geoip extends Mage_Core_Block_Template
{
	protected $_website_id = null;
	
	protected function _construct()
	{
		parent::_construct();
		$this->_website_id = $this->getRequest()->getParam('website');
	}
	
	public function _prepareLayout() 
	{
		parent::_prepareLayout();		
	}
	
	public function getGeoipVersion()
	{		
		return $this->getRequest()->getParam('version');
	}
	
	public function getWebsiteId()
	{
		return $this->_website_id;
	}
	
	public function getImageLink()
	{
		return $this->getSkinUrl('images/transfer-ajax-loader.gif');
	}
	
	public function getImageLinkStop()
	{
		return $this->getSkinUrl('images/ajax-loader.jpg');
	}
	
	public function checkDataImport()
	{				
		 
		$geoip = Mage::getModel('onestepcheckout/countrylist')->load(1, 'type');
		$lastVersion = $geoip->getLastVersion();		
		if($lastVersion == '1.0')
			$fileUrl = 'version1.0'.DS.'geoip.csv';
		else
			$fileUrl = 'nextversion'.DS.'geoip_'.$lastVersion.'.csv';
		$fileImport = Mage::getBaseDir().DS.'app'.DS.'locale'.DS.'magestore_geoip'.DS.'geoip'.DS.$fileUrl;				
		// return $fileImport;
		if(file_exists($fileImport))
			return file_exists($fileImport);	
			
		return false;
	}
	
	public function actionImport()
	{
		$link = Mage::getSingleton('adminhtml/url')->getUrl('onestepcheckoutadmin/adminhtml_geoip/importGeoip',array(
			'website'	=> $this->getWebsiteId(),			
			'version'	=> $this->getGeoipVersion(),			
		));
		return $link;
	}	
	

}
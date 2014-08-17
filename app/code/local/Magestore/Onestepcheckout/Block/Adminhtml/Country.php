<?php
class Magestore_Onestepcheckout_Block_Adminhtml_Country extends Mage_Core_Block_Template
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
	
	public function getCountryid()
	{		
		return $this->getRequest()->getParam('id');
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
		$parameter = $this->getCountryid();
		$countryids = explode(',', $parameter);	
		foreach($countryids as $countryid){
			$countryList = Mage::getModel('onestepcheckout/countrylist')->load($countryid);
			$countryCode = $countryList->getCountryCode();
			$lastVersion = $countryList->getLastVersion();			
			if($lastVersion == '1.0')
				$fileUrl = 'version1.0'.DS.$countryCode.'.csv';
			else
				$fileUrl = 'nextversion'.DS.$countryCode.'_'.$lastVersion.'.csv';
			$fileImport = Mage::getBaseDir().DS.'app'.DS.'locale'.DS.'magestore_geoip'.DS.'countrypostcode'.DS.$fileUrl;				
			// return $fileImport;
			if(file_exists($fileImport))
				return file_exists($fileImport);
		}
		return false;
	}
	
	public function actionImport()
	{
		$link = Mage::getSingleton('adminhtml/url')->getUrl('onestepcheckoutadmin/adminhtml_country/importCountryip',array(
			'website'	=> $this->getWebsiteId(),
			'countryid'	=> $this->getCountryid()
		));
		return $link;
	}	
}
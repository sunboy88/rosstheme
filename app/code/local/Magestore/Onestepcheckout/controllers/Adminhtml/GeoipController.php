<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Onestepcheckout
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Onestepcheckout Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Onestepcheckout
 * @author      Magestore Developer
 */
class Magestore_Onestepcheckout_Adminhtml_GeoipController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Onestepcheckout_Adminhtml_GeoipController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('onestepcheckout/geoip')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Update Geoip Database'),
                Mage::helper('adminhtml')->__('Update Geoip Database')
            );
        return $this;
    }
		
    public function indexAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }		
		$this->_title($this->__('Update Geoip Database'))
			->_title($this->__('Update Geoip Database'));
		$this->_initAction()->renderLayout();
    }      
		
	public function newAction()
    {
        $this->_forward('edit');
    }
	
	public function editAction() 
	{					
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);			

		$this->loadLayout();
		$this->_setActiveMenu('onestepcheckout/countrylist');

		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Rule Manager'), Mage::helper('adminhtml')->__('Upload New Database Version'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Rule News'), Mage::helper('adminhtml')->__('Upload New Database Version'));

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('onestepcheckout/adminhtml_geoip_edit'))				
			 ->_addLeft($this->getLayout()->createBlock('onestepcheckout/adminhtml_geoip_edit_tabs'));
		$this->renderLayout();		
	}
	
	public function saveAction()
	{		
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
		if(isset($_FILES['csv_geoip']['name']) && $_FILES['csv_geoip']['name'] != ''){		
			try {
				$uploader = new Varien_File_Uploader('csv_geoip');
				$uploader->setAllowedExtensions(array('csv'));
				$uploader->setAllowRenameFiles(false);
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('app').DS.'locale'.DS.'magestore_geoip'.DS.'geoip'.DS.'nextversion';
				$uploader->save($path, $_FILES['csv_geoip']['name']);
				
				Mage::getSingleton('core/session')->addSuccess('CSV file was uploaded successfully!');					
				$fileNameFull = $_FILES['csv_geoip']['name'];
				$fileName = explode('.', $fileNameFull);
				$geoipVersion = explode('_', $fileName[0]);				
				$version = $geoipVersion[1].'.0';
				
				$total = 0;
				$oFile = new Varien_File_Csv();				
				$url = $path.DS.$_FILES['csv_geoip']['name'];			
				try{
					$data = $oFile->getData($url);
					$total = count($data);
				}catch(Exception $e){
				}
				$geoipVersion = Mage::getModel('onestepcheckout/countrylist')->load(1, 'type');
				if($version != $geoipVersion->getLastVersion()){
					try{
						$geoipVersion->setLastVersion($version)
									->setStatus(0)
									->setTotalRecords($geoipVersion->getTotalRecords() + $total-1)
									->save();
					}catch(Exception $e){}
				}
			 } catch (Exception $e) {
				Mage::getSingleton('core/session')->addError('Invalid format of file! ');
				$this->_redirect('*/*/new');
			 }			
		}else{
			Mage::getSingleton('core/session')->addError('Not selected file!');
			$this->_redirect('*/*/');
			return;
		}
		if ($this->getRequest()->getParam('back')) {
			$this->_redirect('*/*/new');
			return;
		}
		$this->_redirect('*/*');
	}	
	
	 public function showGeoipAction()
	{	
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }						
		$this->loadLayout();
		$html = $this->getLayout()->getBlock('head')->toHtml();
		$html .= $this->getLayout()->getBlock('geoip')->toHtml();
		$this->getResponse()->setBody($html);
	}
	
	public function importGeoipAction()
	{
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }					
		$filename = 'geoip';
		$helper = Mage::helper('onestepcheckout');
		$times = $helper->getMaxItemsEachImport();
		$timeImported = 0;
		$geoipVersion = Mage::getModel('onestepcheckout/countrylist')->load(1, 'type');
		$versionParam = $this->getRequest()->getParam('version');
		if(isset($versionParam) && $versionParam == '1'){
			$fileUrl = 'version1.0'.DS.'geoip.csv';
			$lastVersion = '1.0';
		}else{
			$lastVersion = $geoipVersion->getLastVersion();
			if($lastVersion == '1.0')
				$fileUrl = 'version1.0'.DS.'geoip.csv';
			else
				$fileUrl = 'nextversion'.DS.'geoip_'.$lastVersion.'.csv';
		}	
		$errors = 0;
		$size = 0;
		$total = 0;
		// foreach($versions as $version){
			$oFile = new Varien_File_Csv();
			// $url = Mage::getBaseDir().DS.'app'.DS.'locale'.DS.'Magestore_Geoip_SortbyCountry'.DS.'GeoIPCountry_'.$version.'.csv';	
			$url = Mage::getBaseDir().DS.'app'.DS.'locale'.DS.'magestore_geoip'.DS.'geoip'.DS.$fileUrl;	
			try{
				$data = $oFile->getData($url);			
			}catch(Exception $e){
			}
			if(isset($data)){
				$numberRows = 0;
				$total += count($data);
				$geoip = Mage::getModel('onestepcheckout/country');
				$storeData = array();								
				foreach($data as $col=>$row)
				{		
					$size ++;
					if($col == 0)
					{
						$index_row = $row;
					} else {						
						for($i=0;$i<count($row);$i++)
						{
							$storeData[$index_row[$i]] = $row[$i];
						}
						$geoip->setData($storeData);
						$geoip->setId(null);						
						try{
							if($geoip->import()){
								$timeImported++;
								$numberRows++;
							}
						}catch(Exception $e){
							$errors++;
						}
					}					
					if(isset($times) && $times > 0){
						if ($timeImported == $times) { 												
							break;
						}
					}
				}
				if($numberRows>0){
					$currentRecords = $geoipVersion->getCurrentRecords();
					$geoipVersion->setData('current_version', $lastVersion)
								 ->setData('current_records', $currentRecords + $timeImported)
									;
					try{
						$geoipVersion->save();
					}catch(Exception $e){
					}
				}else{
					$geoipVersion->setData('status', 1);							
					try{
						$geoipVersion->save();
					}catch(Exception $e){
					}
				}
			}			
		// }
		if ($errors && $errors>0){
			$result = $this->__('row(s) have been updated. And %s row(s) was failed.',$errors);
			$action = 'failed';
		} else {			
			$result = $this->__('row(s) have been updated successfully!');
			$action = 'success';
		}	
		if($timeImported == 0 && $size == $total) {
			$result .= '-complete';
		}else{
			$result .= '-'.$timeImported;			
		}
		$result .= '-'.$action;
		$this->getResponse()->setBody($result);
	}
 
   public function gridAction()
   {
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $this->getResponse()->setBody($this->getLayout()->createBlock('onestepcheckout/adminhtml_geoip_edit_tab_form')->toHtml());
    }
}
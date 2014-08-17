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
class Magestore_Onestepcheckout_Adminhtml_CountryController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Onestepcheckout_Adminhtml_CountryController
     */
   protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('onestepcheckout/geoip')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Update Country Postcode Database'),
                Mage::helper('adminhtml')->__('Update Country Postcode Database')
            );
        return $this;
    }
		
    public function indexAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }		
		$this->_title($this->__('Update Country Postcode Database'))
			->_title($this->__('Update Country Postcode Database'));
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
		$this->_addContent($this->getLayout()->createBlock('onestepcheckout/adminhtml_country_edit'))				
			 ->_addLeft($this->getLayout()->createBlock('onestepcheckout/adminhtml_country_edit_tabs'));
		$this->renderLayout();		
	}
	
	public function saveAction()
	{		
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
		if(isset($_FILES['csv_country']['name']) && $_FILES['csv_country']['name'] != ''){			
			try {
				$uploader = new Varien_File_Uploader('csv_country');
				$uploader->setAllowedExtensions(array('csv'));
				$uploader->setAllowRenameFiles(false);
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('app').DS.'locale'.DS.'magestore_geoip'.DS.'countrypostcode'.DS.'nextversion';
				$uploader->save($path, $_FILES['csv_country']['name']);
				Mage::getSingleton('core/session')->addSuccess('CSV file was uploaded successfully!');	
				
				$fileNameFull = $_FILES['csv_country']['name'];
				$fileName = explode('.', $fileNameFull);
				$countryVersion = explode('_', $fileName[0]);
				$countryCode = $countryVersion[0];
				$version = $countryVersion[1].'.0';
				
				$total = 0;
				$oFile = new Varien_File_Csv();				
				$url = $path.DS.$_FILES['csv_country']['name'];			
				try{
					$data = $oFile->getData($url);
					$total = count($data);
				}catch(Exception $e){
				}
				$country = Mage::getModel('onestepcheckout/countrylist')->load($countryCode, 'country_code');
				if($version != $country->getLastVersion()){
					try{
						$country->setLastVersion($version)
								->setStatus(0)
								->setTotalRecords($country->getTotalRecords() + $total-1)
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
		$this->_redirect('*/*', array('up_version'=>'1'));
	}

	/* public function checkDataImport($countryCode)
	{
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
		$countryCode = $countryList->load($countryid)->getCountryCode();
		$lastVersion = $countryList->load($countryid,'country_code')->getLastVersion();
		if($lastVersion == '1.0')
			$fileUrl = 'version1.0'.DS.$countryCode.'.csv';
		else
			$fileUrl = 'nextversion'.DS.$countryCode.$lastVersion.'.csv';				
		// $fileImport = Mage::getBaseDir().DS.'app'.DS.'locale'.DS.'Magestore_Geoip_SortbyCountry'.DS.$countryid.'.csv';	
		$fileImport = Mage::getBaseDir().DS.'app'.DS.'locale'.DS.'magestore_geoip'.DS.'countrypostcode'.DS.$fileUrl;	
		if(file_exists($fileImport))
			return file_exists($fileImport);
		return false;
	}	 */
 
   public function showCountryIpAction()
	{	
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }		
		$this->loadLayout();
		$html = $this->getLayout()->getBlock('head')->toHtml();
		$html .= $this->getLayout()->getBlock('country')->toHtml();
		$this->getResponse()->setBody($html);
	}
	
	public function importCountryipAction()
	{
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }		
		$parameter = $this->getRequest()->getParam('countryid');		
		$countryids = explode(',', $parameter);			
		$helper = Mage::helper('onestepcheckout');
		$times = $helper->getMaxItemsEachImport();		
		$timeImported = 0;				
		$errors = 0;		
		$countryList = Mage::getModel('onestepcheckout/countrylist');
		$timeStamp = date("Y-m-d H:i:s");
		$size = 0;
		$total = 0;
		// $timeStamp = Mage::getModel('core/date')->timestamp($timeNow);		
		foreach($countryids as $countryid){
			$countryCode = $countryList->load($countryid)->getCountryCode();
			$lastVersion = $countryList->load($countryid)->getLastVersion();
			if($lastVersion == '1.0')
				$fileUrl = 'version1.0'.DS.$countryCode.'.csv';
			else
				$fileUrl = 'nextversion'.DS.$countryCode.'_'.$lastVersion.'.csv';				
			$oFile = new Varien_File_Csv();
			$url = Mage::getBaseDir().DS.'app'.DS.'locale'.DS.'magestore_geoip'.DS.'countrypostcode'.DS.$fileUrl;			
			try{
				$data = $oFile->getData($url);					
			}catch(Exception $e){
			}
			if(isset($data)){
				$numberRows = 0;
				$geoip = Mage::getModel('onestepcheckout/geoip');
				$storeData = array();	
				$total += count($data);
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
					$currentRecords = $countryList->load($countryCode)->getCurrentRecords();
					$countryList->load($countryCode)
							->setData('update_date', $timeStamp)
							->setData('current_version', $lastVersion)
							->setData('current_records', $currentRecords + $numberRows)
							;
					try{
						$countryList->save();
					}catch(Exception $e){
					}
				}else{
					$countryList->load($countryCode)
							->setData('status', 1);							
					try{
						$countryList->save();
					}catch(Exception $e){
					}
				}
			}			
		}
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
			// $result .= '-'.$size;			
		}
		$result .= '-'.$action;
		$this->getResponse()->setBody($result);
	}   
	
	public function gridAction()
	{
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $this->getResponse()->setBody($this->getLayout()->createBlock('onestepcheckout/adminhtml_country_grid')->toHtml());
    }
}
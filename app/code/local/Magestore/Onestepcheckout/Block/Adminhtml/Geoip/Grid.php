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
 * @package     Magestore_Geoip
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Onestepcheckout Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Onestepcheckout
 * @author      Magestore Developer
 */
class Magestore_Onestepcheckout_Block_Adminhtml_Geoip_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Geoip_Block_Adminhtml_Geoip_Edit_Tab_Form
     */
    public function __construct()
    {	
        parent::__construct();
        $this->setUseAjax(false);
    }    
	
	public function getGeoip()
	{
            return Mage::getModel('onestepcheckout/countrylist')->load(1, 'type');
	}
		
	
	public function _prepareLayout()
	{
            $this->setTemplate('onestepcheckout/geoipgrid.phtml');
	}	

	public function linkUpdateGeoip()
	{
		$geoIp = Mage::getModel('onestepcheckout/countrylist')->load(1, 'type');
		if($geoIp->getCurrentVersion() == $geoIp->getLastVersion() && $geoIp->getStatus()=='1'){
			return false;
		}
		else{
			$link = Mage::getSingleton('adminhtml/url')->getUrl('onestepcheckoutadmin/adminhtml_geoip/showGeoip',array(
				'website' => $this->getRequest()->getParam('website'),		
				'_query'  => array('isAjax'  => 'false'),			
			));
		return $link;
		}
	}
		
}
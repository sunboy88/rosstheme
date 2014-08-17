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
 * Onestepcheckout Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Onestepcheckout
 * @author      Magestore Developer
 */
class Magestore_Onestepcheckout_Block_Adminhtml_Country_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'onestepcheckout';
        $this->_controller = 'adminhtml_country';
        
		$this->_updateButton('save', 'label', Mage::helper('onestepcheckout')->__('Upload'));
		
		$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Upload and Continue'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('geoip_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'geoip_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'geoip_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
			//edit back button in import
			function backEdit()
			{
				window.history.back();
			}			
        ";		
    }
    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {        
        return Mage::helper('onestepcheckout')->__('Upload New Country Postcode Database');
    }
}

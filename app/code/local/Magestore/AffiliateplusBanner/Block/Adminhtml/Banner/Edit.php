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
 * @package     Magestore_AffiliateplusBanner
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusbanner Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Block_Adminhtml_Banner_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'affiliateplusbanner';
        $this->_controller = 'adminhtml_banner';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliateplus')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('affiliateplus')->__('Delete Banner'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
            function changeBannerType(el) {
                if ($('banner_tabs_banner_section') == null) return;
                var rotatorGrid = $($('banner_tabs_banner_section').up('li'));
                if (rotatorGrid == null) return;
                if (el.value == '" . Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_ROTATOR . "') {
                    rotatorGrid.show();
                    $('type_id_rotator_banners').show();
                } else {
                    rotatorGrid.hide();
                    $('type_id_rotator_banners').hide();
                }
            }
            Event.observe(window, 'load', function(){changeBannerType($('type_id'));});
            function showBannerRotatorTab() {
                banner_tabsJsTabs.showTabContent($('banner_tabs_banner_section'));
            }
        ";
    }
    
    public function getHeaderText()
    {
        if (Mage::registry('banner_data') && Mage::registry('banner_data')->getId()) {
            return Mage::helper('affiliateplus')->__("Edit Banner '%s'",
                    $this->htmlEscape(Mage::registry('banner_data')->getTitle()));
        } else {
            return Mage::helper('affiliateplus')->__('Add Banner');
        }
    }
}
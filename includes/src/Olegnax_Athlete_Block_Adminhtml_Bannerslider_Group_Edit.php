<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Block_Adminhtml_Bannerslider_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'athlete';
        $this->_controller = 'adminhtml_bannerslider_group';
        
        $this->_updateButton('save', 'label', Mage::helper('athlete')->__('Save Group'));
        $this->_updateButton('delete', 'label', Mage::helper('athlete')->__('Delete Group'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('athlete_bannerslider_group_data') && Mage::registry('athlete_bannerslider_group_data')->getId() ) {
            return Mage::helper('athlete')->__("Edit Group");
        } else {
            return Mage::helper('athlete')->__('Add Group');
        }
    }
}
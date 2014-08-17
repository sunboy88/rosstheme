<?php

class Trmmarketing_Twiliosmsbridge_Block_Adminhtml_Twiliosmsbridge_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'twiliosmsbridge';
        $this->_controller = 'adminhtml_twiliosmsbridge';
        
        $this->_updateButton('save', 'label', Mage::helper('twiliosmsbridge')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('twiliosmsbridge')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('twiliosmsbridge_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'twiliosmsbridge_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'twiliosmsbridge_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('twiliosmsbridge_data') && Mage::registry('twiliosmsbridge_data')->getId() ) {
            return Mage::helper('twiliosmsbridge')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('twiliosmsbridge_data')->getTitle()));
        } else {
            return Mage::helper('twiliosmsbridge')->__('Add Item');
        }
    }
}
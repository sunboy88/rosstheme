<?php
class Amasty_Shopby_Block_Adminhtml_Value_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'amshopby';
        $this->_controller = 'adminhtml_value';
        
        $this->_removeButton('back'); 
        $this->_removeButton('reset'); 
        $this->_removeButton('delete'); 
        
		$this->_formScripts[] = " function featured(sel) {
			
            if (sel.value ==  1) {
            	sel.up('tr').next('tr').show();
            } else {
            	sel.up('tr').next('tr').hide();
            }
            
        }featured($('is_featured'));";  
    }

    public function getHeaderText()
    {
        return Mage::helper('amshopby')->__('Option Properties');
    }
}
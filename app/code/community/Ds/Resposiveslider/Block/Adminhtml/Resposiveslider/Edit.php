<?php
	
class Ds_Resposiveslider_Block_Adminhtml_Resposiveslider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "slide_id";
				$this->_blockGroup = "resposiveslider";
				$this->_controller = "adminhtml_resposiveslider";
				$this->_updateButton("save", "label", Mage::helper("resposiveslider")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("resposiveslider")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("resposiveslider")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("resposiveslider_data") && Mage::registry("resposiveslider_data")->getId() ){

				    return Mage::helper("resposiveslider")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("resposiveslider_data")->getId()));

				} 
				else{

				     return Mage::helper("resposiveslider")->__("Add Item");

				}
		}
}
<?php
class Ds_Resposiveslider_Block_Adminhtml_Resposiveslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("resposiveslider_form", array("legend"=>Mage::helper("resposiveslider")->__("Item information")));

				
						$fieldset->addField("title", "text", array(
						"label" => Mage::helper("resposiveslider")->__("Title"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "title",
						));
									
						$fieldset->addField('image_name', 'image', array(
						'label' => Mage::helper('resposiveslider')->__('Image'),
						'name' => 'image_name',
						"class" => "required-entry",
						"required" => true,
						'note' => '(*.jpg, *.png, *.gif)',
						));
						$fieldset->addField("image_url", "text", array(
						"label" => Mage::helper("resposiveslider")->__("Image Link Url"),
						"name" => "image_url",
						));
						$fieldset->addField("content", "textarea", array(
						"label" => Mage::helper("resposiveslider")->__("Content"),
						"name" => "content",
						));
									
						 $fieldset->addField('is_active', 'select', array(
						'label'     => Mage::helper('resposiveslider')->__('Active'),
						'values'   => Ds_Resposiveslider_Block_Adminhtml_Resposiveslider_Grid::getValueArray3(),
						'name' => 'is_active',					
						"class" => "required-entry",
						"required" => true,
						));

				if (Mage::getSingleton("adminhtml/session")->getResposivesliderData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getResposivesliderData());
					Mage::getSingleton("adminhtml/session")->setResposivesliderData(null);
				} 
				elseif(Mage::registry("resposiveslider_data")) {
				    $form->setValues(Mage::registry("resposiveslider_data")->getData());
				}
				return parent::_prepareForm();
		}
}

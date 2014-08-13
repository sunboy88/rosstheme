<?php
class Ds_Resposiveslider_Block_Adminhtml_Resposiveslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("resposiveslider_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("resposiveslider")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("resposiveslider")->__("Item Information"),
				"title" => Mage::helper("resposiveslider")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("resposiveslider/adminhtml_resposiveslider_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

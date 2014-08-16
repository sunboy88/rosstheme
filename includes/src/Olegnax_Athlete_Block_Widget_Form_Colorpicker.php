<?php

class Olegnax_Athlete_Block_Widget_Form_Colorpicker extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
	/**
	 * Render element.
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$after_element_html = '<script> jQuery(function(){ jQuery("#'.$element->getHtmlId().'").attr("style", "width: 200px !important").attr("data-hex", true).mColorPicker({ imageFolder: "'.$this->getJsUrl('olegnax/mColorPicker/').'" }); });</script>';
		$element->setData('after_element_html', $after_element_html );

		$this->_element = $element;
		return $this->toHtml();
	}
}
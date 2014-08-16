<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Block_Adminhtml_System_Config_Form_Field_Color extends
	Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Override field method to add js
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        // Get the default HTML for this option
        $html = parent::_getElementHtml($element);
		$html .= '<script>jQuery(function(){ jQuery("#'.$element->getHtmlId().'").attr("style", "width: 200px !important").attr("data-hex", true).mColorPicker({ imageFolder: "'.$this->getJsUrl('olegnax/mColorPicker/').'" }); });</script>';
        return $html;
    }
}
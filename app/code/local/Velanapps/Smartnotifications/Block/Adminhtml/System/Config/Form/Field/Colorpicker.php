<?php
 /*
 * Velan Info Services India Pvt Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.velanapps.com/License.txt
 *
  /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 * *************************************** */
/* This package designed for Magento COMMUNITY edition
 * Velan Info Services does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Velan Info Services does not provide extension support in case of
 * incorrect edition usage.
  /***************************************
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 * ****************************************************
 * @category   Velanapps
 * @package    Smartnotifications
 * @author     Velan Team 
 * @copyright  Copyright (c) 2013 Velan Info Services India Pvt Ltd. (http://www.velanapps.com)
 * @license    http://store.velanapps.com/License.txt
 */ 
 
class Velanapps_Smartnotifications_Block_Adminhtml_System_Config_Form_Field_Colorpicker extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<script type="text/javascript" src="' . Mage::getBaseUrl('js') . 'smartnotifications/systemcolorpicker/procolor-1.0/procolor.compressed.js' .'"></script>';
        $input = new Varien_Data_Form_Element_Text();
        $input->setForm($element->getForm())
            ->setElement($element)
            ->setValue($element->getValue())
            ->setHtmlId($element->getHtmlId())
            ->setName($element->getName())
            ->setStyle('width: 100px')
            ->addClass('validate-hex'); 
        $html .= $input->getHtml();
        $html .= $this->_getProcolorJs($element->getHtmlId());
        $html .= $this->_addHexValidator();
        return $html;
    }

    protected function _getProcolorJs($htmlId)
    {
        return '<script type="text/javascript">ProColor.prototype.attachButton(\'' . $htmlId . '\', { imgPath:\'' . Mage::getBaseUrl('js') . 'smartnotifications/systemcolorpicker/procolor-1.0/' . 'img/procolor_win_\', showInField: true });</script>';
    }
	
    protected function _addHexValidator()
    {
        return '<script type="text/javascript">Validation.add(\'validate-hex\', \'' . Mage::helper('smartnotifications')->__('Please enter a valid hex color code') . '\', function(v) {
        return /^#(?:[0-9a-fA-F]{3}){1,2}$/.test(v);
        });</script>';
    }
}
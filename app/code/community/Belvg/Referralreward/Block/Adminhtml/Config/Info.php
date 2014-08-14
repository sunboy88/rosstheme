<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 *******************************************************************
 * @category   Belvg
 * @package    Belvg_Referralreward
 * @copyright  Copyright (c) 2010 - 2014 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */
class Belvg_Referralreward_Block_Adminhtml_Config_Info extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const MODULE_NAME = 'Belvg_Referralreward';

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $helper = Mage::helper('referralreward');
        $id     = $element->getHtmlId();

        $html  = '<tr id="row_' . $id . '">';
        $html .= '<td class="label" colspan="1">' . $helper->__('Module Version') . '</td><td class="value" colspan="4">' . $this->getModuleVersion() . '</td>';
        $html .= '</tr>';
        if ($tmp = $this->getModuleUpdate()) {
            $html .= '<tr id="row">';
            $html .= '<td class="label" colspan="1">' . $helper->__('Last Update') . '</td><td class="value" colspan="4">' . $tmp . '</td>';
            $html .= '</tr>';
        }

        if ($tmp = $this->getModuleDesc()) {
            $html .= '<tr id="row">';
            $html .= '<td class="label" colspan="1">' . $helper->__('Description') . '</td><td class="value" colspan="4">' . $tmp . '</td>';
            $html .= '</tr>';
        }

        return $html;
    }

    public function getModuleVersion()
    {
        return (string)Mage::getConfig()->getNode('modules/' . self::MODULE_NAME . '/version');
    }

    public function getModuleUpdate()
    {
        return (string)Mage::getConfig()->getNode('modules/' . self::MODULE_NAME . '/update');
    }

    public function getModuleDesc()
    {
        return (string)Mage::getConfig()->getNode('modules/' . self::MODULE_NAME . '/desc');
    }
}
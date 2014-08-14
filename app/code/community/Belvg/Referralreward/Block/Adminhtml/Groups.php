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
/**********************************************
 *        MAGENTO EDITION USAGE NOTICE        *
 **********************************************/
/* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/**********************************************
 *        DISCLAIMER                          *
 **********************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 **********************************************
 * @category   Belvg
 * @package    Belvg_Referralreward
 * @copyright  Copyright (c) 2010 - 2013 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_Block_Adminhtml_Groups extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
{
    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('belvg/referralreward/field/groups.phtml');
    }

    public function getValues()
    {
        $values = array();
        $data   = $this->getElement()->getValue();

        return Mage::helper('referralreward')->decodeStoreConfigMoveGroupTo($data);
    }

    public function getTooltip()
    {
        $element = $this->getElement();
        if ($element->getTooltip()) {
            return '<div class="field-tooltip"><div>' . $element->getTooltip() . '</div></div>';
        }

        return '';
    }

    public function getComment()
    {
        $element = $this->getElement();
        if ($element->getComment()) {
            return '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }

        return '';
    }

    public function getScope()
    {
        $element = $this->getElement();
        if ($element->getScope()) {
            return $element->getScopeLabel();
        }

        return '';
    }

    public function getHint()
    {
        $element = $this->getElement();
        if ($element->getHint()) {
            return '<div class="hint">' .
                       '<div style="display:none">' . $element->getHint() . '</div>' .
                   '</div>';
        }

        return '';
    }

    public function getDefaultHtml()
    {
        $element    = $this->getElement();
        $id         = $element->getHtmlId();
        $html       = '';

        //$isDefault = !$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store');
        $isMultiple = $element->getExtType() === 'multiple';

        // replace [value] with [inherit]
        $namePrefix = preg_replace('#\[value\](\[\])?$#', '', $element->getName());
        $options    = $element->getValues();

        $addInheritCheckbox     = FALSE;
        if ($element->getCanUseWebsiteValue()) {
            $addInheritCheckbox = TRUE;
            $checkboxLabel      = Mage::helper('adminhtml')->__('Use Website');
        } elseif ($element->getCanUseDefaultValue()) {
            $addInheritCheckbox = TRUE;
            $checkboxLabel      = Mage::helper('adminhtml')->__('Use Default');
        }

        if ($addInheritCheckbox) {
            $inherit = $element->getInherit() == 1 ? 'checked="checked"' : '';
            if ($inherit) {
                $element->setDisabled(TRUE);
            }
        }

        if ($addInheritCheckbox) {
            $defText = $element->getDefaultValue();
            if ($options) {
                $defTextArr = array();
                foreach ($options as $k=>$v) {
                    if ($isMultiple) {
                        if (is_array($v['value']) && in_array($k, $v['value'])) {
                            $defTextArr[] = $v['label'];
                        }
                    } elseif ($v['value'] == $defText) {
                        $defTextArr[] = $v['label'];
                        break;
                    }
                }

                $defText = join(', ', $defTextArr);
            }

            // default value
            $html .= '<input id="' . $id . '_inherit" name="'
                . $namePrefix . '[inherit]" type="checkbox" value="1" class="checkbox config-inherit" '
                . $inherit . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" /> ';
            $html .= '<label for="' . $id . '_inherit" class="inherit" title="' . htmlspecialchars($defText) . '">' . $checkboxLabel . '</label>';
            $html .= '<script>'
                . '     Event.observe(window, "load", function() {'
                . '         var thisCheckbox = $("' . $id . '_inherit");'
                . '         toggleValueElements(thisCheckbox, Element.previous(thisCheckbox.parentNode))'
                . '     });'
                . '   </script>';
        }

        return $html;
    }

    /**
     * Sort values
     *
     * @param array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        usort($data, array($this, '_sortgradualgroupsPrices'));

        return $data;
    }

    /**
     * Sort gradual_groups price values callback method
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortgradualgroupsPrices($a, $b)
    {
        if ($a['invited'] != $b['invited']) {
            return $a['invited'] < $b['invited'] ? -1 : 1;
        }

        return 0;
    }

    /**
     * Prepare global layout
     * Add "Add gradual_groups" button to layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_gradualgroups
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'   => Mage::helper('catalog')->__('Add Invited Range'),
                'onclick' => 'return gradual_groupsPriceControl.addItem()',
                'class'   => 'add'
            ));
        $button->setName('add_gradual_groups_item_button');

        $this->setChild('add_gradual_groups_button', $button);

        return parent::_prepareLayout();
    }
}
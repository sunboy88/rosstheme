<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Popup
 * @version    1.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Popup_Block_Adminhtml_Popup_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('popup_form', array('legend' => Mage::helper('popup')->__('General')));
        $fieldset->addType('label', 'AW_Popup_Block_Adminhtml_Renderer_Label');
        $fieldset->addField(
            'name',
            'text',
            array(
                 'label'    => Mage::helper('popup')->__('Name'),
                 'class'    => 'required-entry',
                 'required' => true,
                 'name'     => 'name',
            )
        );

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField(
            'date_from',
            'date',
            array(
                 'label'    => Mage::helper('popup')->__('Date from'),
                 'required' => false,
                 'name'     => 'date_from',
                 'image'    => $this->getSkinUrl('images/grid-cal.gif'),
                 'format'   => $dateFormatIso,
            )
        );

        $fieldset->addField(
            'date_to',
            'date',
            array(
                 'label'    => Mage::helper('popup')->__('Date to'),
                 'required' => false,
                 'name'     => 'date_to',
                 'image'    => $this->getSkinUrl('images/grid-cal.gif'),
                 'format'   => $dateFormatIso,
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_view',
                'multiselect',
                array(
                     'label'    => Mage::helper('popup')->__('Store view'),
                     'class'    => 'required-entry',
                     'required' => true,
                     'name'     => 'store_view',
                     'values'   => Mage::getSingleton('adminhtml/system_store')
                             ->getStoreValuesForForm(false, true),
                )
            );
        }

        $fieldset->addField(
            'show_at',
            'multiselect',
            array(
                 'label'    => Mage::helper('popup')->__('Show At'),
                 'class'    => 'required-entry',
                 'required' => true,
                 'name'     => 'show_at',
                 'values'   => Mage::getModel('popup/source_page')->toOptionArray(),
            )
        );

        $fieldset->addField(
            'align',
            'select',
            array(
                 'label'    => Mage::helper('popup')->__('Align'),
                 'name'     => 'align',
                 'class'    => 'required-entry',
                 'required' => true,
                 'values'   => Mage::getModel('popup/source_position')->toOptionArray(),
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                 'label'    => Mage::helper('popup')->__('Status'),
                 'name'     => 'status',
                 'class'    => 'required-entry',
                 'required' => true,
                 'values'   => Mage::getModel('popup/source_status')->toOptionArray(),
            )
        );

        $fieldset->addField(
            'show_count',
            'text',
            array(
                 'label'    => Mage::helper('popup')->__('General number of shows'),
                 'required' => true,
                 'name'     => 'show_count',
                 'note'     => $this->__('0 - unlimited usage'),
            )
        );

        $fieldset->addField(
            'show_count_per_customer',
            'text',
            array(
                 'label'    => Mage::helper('popup')->__('Number of shows per customer'),
                 'required' => true,
                 'name'     => 'show_count_per_customer',
                 'note'     => $this->__('0 - unlimited usage'),
            )
        );

        $fieldset->addField(
            'use_count',
            'label',
            array(
                 'label'    => Mage::helper('popup')->__('Number of times popup was showed'),
                 'required' => false,
                 'name'     => 'use_count',
            )
        );

        $fieldset->addField(
            'width',
            'text',
            array(
                 'label'    => Mage::helper('popup')->__('Width, px'),
                 'required' => false,
                 'name'     => 'width',
                 'note'     => $this->__('Minimum value is 200'),
            )
        );

        $fieldset->addField(
            'height',
            'text',
            array(
                 'label'    => Mage::helper('popup')->__('Height, px'),
                 'required' => false,
                 'name'     => 'height',
                 'note'     => $this->__('Minimum value is 300'),
            )
        );

        $fieldset->addField(
            'sort_order',
            'text',
            array(
                 'label'    => Mage::helper('popup')->__('Sort order'),
                 'required' => false,
                 'name'     => 'sort_order',
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getPopupData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPopupData());
            Mage::getSingleton('adminhtml/session')->setPopupData(null);
        } elseif (Mage::registry('popup_data')) {
            $form->setValues(Mage::registry('popup_data')->getData());
        }
        if (
            Mage::getSingleton('adminhtml/session')->getPopupData() == null
            && Mage::registry('popup_data')->getData() == null
        ) {
            $form->setValues(
                array(
                     'show_count'              => '0',
                     'show_count_per_customer' => '0',
                     'align'                   => Mage::helper('popup')->getDefaultPosition(),
                )
            );
        }
        return parent::_prepareForm();
    }
}
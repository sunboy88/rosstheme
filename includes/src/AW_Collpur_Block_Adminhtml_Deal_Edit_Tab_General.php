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
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Collpur_Block_Adminhtml_Deal_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        
       
        $deal = Mage::registry('collpur_deal');
       
        if (!$deal->getProductId()) {
            $deal->setData('product_id', $this->getRequest()->getParam('product_id'));
            $deal->setData('product_visibility', $this->getRequest()->getParam('product_visibility'));
        }

        $product = Mage::getModel('catalog/product')->load($deal->getProductId());
        if ($product->getId()) {
            $deal->setData('product_name', $product->getName());
            $deal->setData('product_name_label', $product->getName());
        } else {
            throw new Exception('Incorrect product');
        }

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('deal_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('collpur')->__('General Information')));

        if ($deal->getId()) {
            
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
       
        }

        $fieldset->addField('product_id', 'hidden', array(
            'name' => 'product_id',
        ));

        $fieldset->addField('product_visibility', 'hidden', array(
            'name' => 'product_visibility',
        ));

        $fieldset->addField('product_name', 'hidden', array(
            'name' => 'product_name',
        ));

        $fieldset->addField('product_name_label', 'label', array(
            'name' => 'product_name_label',
            'label' => Mage::helper('collpur')->__('Product name'),
            'title' => Mage::helper('collpur')->__('Product name'),
        ));

        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'hidden', array(
                'name' => 'store_ids[]',
                'value' => Mage::app()->getStore()->getId(),
            ));
        } else {
            $fieldset->addField('store_ids', 'multiselect', array(
                'name' => 'store_ids[]',
                'label' => $this->__('Store view'),
                'title' => $this->__('Store view'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }

        $fieldset->addField('is_active', 'select', array(
            'label' => $this->__('Is active'),
            'title' => $this->__('Is active'),
            'name' => 'is_active',
            'options' => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
        ));

        $fieldset->addField('is_featured', 'select', array(
            'label' => $this->__('Is featured'),
            'title' => $this->__('Is featured'),
            'name' => 'is_featured',
            'options' => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
        ));

        $fieldset->addField('qty_to_reach_deal', 'text', array(
            'name' => 'qty_to_reach_deal',
            'label' => $this->__('Qty to reach deal'),
            'title' => $this->__('Qty to reach deal'),
        ));

        $fieldset->addField('maximum_allowed_purchases', 'text', array(
            'name' => 'maximum_allowed_purchases',
            'label' => $this->__('Maximum allowed purchases'),
            'title' => $this->__('Maximum allowed purchases'),
            'after_element_html' => 'Leave empty for unlimited',
        ));

        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        $fieldset->addField('available_from', 'date', array(
            'name' => 'available_from',
            'label' => $this->__('Available from'),
            'title' => $this->__('Available from'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
            'time' => true,
        ));

        $fieldset->addField('available_to', 'date', array(
            'name' => 'available_to',
            'label' => $this->__('Available to'),
            'title' => $this->__('Available to'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
            'time' => true,
        ));

        $fieldset->addField('price', 'text', array(
            'name' => 'price',
            'label' => $this->__('Deal price'),
            'title' => $this->__('Deal price'),
        ));

        $fieldset->addField('auto_close', 'select', array(
            'label' => $this->__('Automatically close deal on success'),
            'title' => $this->__('Automatically close deal on success'),
            'name' => 'auto_close',
            'options' => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
        ));
        
        
        $fieldset->addField('url_key', 'text', array(
            'name' => 'url_key',
            'label' => $this->__('Url key'),
            'title' => $this->__('Url key'), 
            'after_element_html' => 'Leave empty to use the URL of the related product. Note, only Latin symbols, numbers, hyphens and underscores are allowed in the url key',
        ));
        
        $form->setValues($deal->getData());

        if ($deal->getData('available_from')) {
            $form->getElement('available_from')->setValue(
                    Mage::app()->getLocale()->date($deal->getData('available_from'), Varien_Date::DATETIME_INTERNAL_FORMAT)
            );
        }

        if ($deal->getData('available_to')) {
            $form->getElement('available_to')->setValue(
                    Mage::app()->getLocale()->date($deal->getData('available_to'), Varien_Date::DATETIME_INTERNAL_FORMAT)
            );
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

}

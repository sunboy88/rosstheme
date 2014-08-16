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


class AW_Collpur_Block_Adminhtml_Deal_Edit_Tab_Info extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $deal = Mage::registry('collpur_deal');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('deal_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('collpur')->__('Details')));

        $product = Mage::getModel('catalog/product')->load($deal->getProductId());
        if ($product->getId()) {
            $progressOptions = Mage::getModel('collpur/source_progress')->toOptionArray();


            $deal->setData('current_product_price', Mage::helper('core')->currency($product->getFinalPrice(), true, false));
            $deal->setData('product_name_label_info', $product->getName());
            $deal->setData('condition', $progressOptions[$deal->getProgress()]);
            if ($deal->getData('available_to')) {
                $deal->setData('time_to_finish', Mage::helper('collpur/deals')->getTimeLeftToBuy($deal, 'available_to'));
            }
            if ($deal->getData('available_from')) {
                $deal->setData('time_to_start', Mage::helper('collpur/deals')->getTimeLeftToBuy($deal, 'available_from'));
            }
        }

        $fieldset->addField('product_name_label_info', 'label', array(
            'name' => 'product_name_label_info',
            'label' => Mage::helper('collpur')->__('Product name'),
            'title' => Mage::helper('collpur')->__('Product name'),
        ));

        $fieldset->addField('current_product_price', 'label', array(
            'name' => 'purchases_left',
            'label' => Mage::helper('collpur')->__('Product price'),
            'title' => Mage::helper('collpur')->__('Product price'),
        ));

        $fieldset->addField('condition', 'label', array(
            'name' => 'condition',
            'label' => Mage::helper('collpur')->__('Condition'),
            'title' => Mage::helper('collpur')->__('Condition'),
        ));

        if ($deal->getProgress() == AW_Collpur_Model_Source_Progress::PROGRESS_NOT_RUNNING) {
            $fieldset->addField('time_to_start', 'label', array(
                'name' => 'time_to_start',
                'label' => Mage::helper('collpur')->__('Time to start'),
                'title' => Mage::helper('collpur')->__('Time to start'),
            ));
        }

        if ($deal->getProgress() == AW_Collpur_Model_Source_Progress::PROGRESS_RUNNING) {
            $fieldset->addField('time_to_finish', 'label', array(
                'name' => 'time_to_finish',
                'label' => Mage::helper('collpur')->__('Time to finish'),
                'title' => Mage::helper('collpur')->__('Time to finish'),
            ));
        }

        $fieldset->addField('purchases_left', 'label', array(
            'name' => 'purchases_left',
            'label' => Mage::helper('collpur')->__('Purchases to complete'),
            'title' => Mage::helper('collpur')->__('Purchases to complete'),
        ));

        $form->setValues($deal->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
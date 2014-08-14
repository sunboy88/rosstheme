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


class AW_Collpur_Block_Adminhtml_Deal_Edit_Tab_CouponsNotActive extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $deal = Mage::registry('collpur_deal');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('collpur')->__('Coupons')));
        $fieldset->addClass('coupons_not_active_fieldset');
        $form->setHtmlIdPrefix('deal_');

        $fieldset->addField('not_active_coupons_message', 'label', array(
            'name' => 'not_active_coupons_message',
            'title' => $this->__('Coupons functionality will be available after deal is created'),
            'label' => $this->__('Coupons functionality will be available after deal is created'),
            'class' => 'validate-new-password'
        ));


        $form->setValues($deal->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
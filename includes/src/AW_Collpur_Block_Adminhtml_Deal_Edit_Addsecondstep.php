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

class AW_Collpur_Block_Adminhtml_Deal_Edit_Addsecondstep extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _toHtml()
    {
        $this->setTemplate("aw_collpur/deal/edit/addsecondstep.phtml");
        return parent::_toHtml();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('collpur')->__('General Information')));

        $fieldset->addField('product_visibility', 'select', array(
            'name' => 'product_visibility',
            'label' => Mage::helper('collpur')->__('Linked Product Visibility'),
            'title' => Mage::helper('collpur')->__('Linked Product Visibility'),
            'options'   => Mage_Catalog_Model_Product_Visibility::getOptionArray(),
        ));

        $fieldset->addField('product_id', 'hidden', array(
            'name' => 'product_id',
            'value' => $this->getProductId()
        ));

        $fieldset->addField('form_key', 'hidden', array(
            'name' => 'form_key',
            'value' => $this->getFormKey()
        ));

        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getProductId()
    {
        return Mage::app()->getRequest()->getParam('product_id');
    }


}

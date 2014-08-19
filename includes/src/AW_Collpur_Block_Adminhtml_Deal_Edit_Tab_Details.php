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


class AW_Collpur_Block_Adminhtml_Deal_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        
        $deal = Mage::registry('collpur_deal');
 
        if($deal->getDealImage()) {
            $deal->setDealImage('aw_collpur/deals/'.$deal->getDealImage());
        } 


        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('collpur')->__('Details')));
        $form->setHtmlIdPrefix('deal_');

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('collpur')->__('Name'),
            'title' => Mage::helper('collpur')->__('Name'),
        ));

        $fieldset->addField('description', 'text', array(
            'name' => 'description',
            'label' => Mage::helper('collpur')->__('Short description'),
            'title' => Mage::helper('collpur')->__('Short description'),
        ));
        
         
        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();

        $config->setData(
                $this->recursiveReplace(
                        'deals_admin',
                        '' . (string) Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName') . '',
                        $config->getData()
        ));        


        $fieldset->addField('deal_image', 'image', array(
            'name' => 'deal_image',
            'label' => Mage::helper('collpur')->__('Image'),
            'title' => Mage::helper('collpur')->__('Image'),
        ));

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $fieldset->addField('full_description', 'editor', array(
                'name' => 'full_description',
                'label' => Mage::helper('collpur')->__('Description'),
                'title' => Mage::helper('collpur')->__('Description'),
                'style' => 'width:700px; height:200px;',
                'value' => '',
                //'wysiwyg' => false,
                'config' => $config
            ));
        } else {
            $fieldset->addField('full_description', 'textarea', array(
                'name' => 'full_description',
                'label' => Mage::helper('collpur')->__('Description'),
                'title' => Mage::helper('collpur')->__('Description'),
                'style' => 'width:700px; height:200px;'
            ));
        }

        $form->setValues($deal->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function recursiveReplace($search, $replace, $subject) {
        if (!is_array($subject))
            return $subject;

        foreach ($subject as $key => $value)
            if (is_string($value))
                $subject[$key] = str_replace($search, $replace, $value);
            elseif (is_array($value))
                $subject[$key] = $this->recursiveReplace($search, $replace, $value);

        return $subject;
    }

}

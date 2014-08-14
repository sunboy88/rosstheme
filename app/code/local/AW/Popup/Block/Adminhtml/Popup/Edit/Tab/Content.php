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


class AW_Popup_Block_Adminhtml_Popup_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        try {
            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
        } catch (Exception $ex) {
        }
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('popup_form', array('legend' => Mage::helper('popup')->__('Content')));

        $fieldset->addField(
            'title',
            'text',
            array(
                 'label'    => Mage::helper('popup')->__('Content Heading'),
                 'style'    => 'width:600px;',
                 'required' => false,
                 'name'     => 'title',
            )
        );

        try {
            $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
            $config->setData(
                Mage::helper('popup')->recursiveReplace(
                    '/popup_admin/',
                    '/' . (string)Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName') . '/',
                    $config->getData()
                )
            );
        } catch (Exception $ex) {
            $config = null;
        }

        $contentField = $fieldset->addField(
            'popup_content',
            'editor',
            array(
                 'name'     => 'popup_content',
                 'style'    => 'height:500px;width:800px;',
                 'required' => false,
                 'config'   => $config,
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getPopupData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPopupData());
            Mage::getSingleton('adminhtml/session')->setPopupData(null);
        } elseif (Mage::registry('popup_data')) {
            $form->setValues(Mage::registry('popup_data')->getData());
        }

        if ($config) {
            // Setting custom renderer for content field to remove label column
            $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
                ->setTemplate('cms/page/edit/form/renderer/content.phtml');
            $contentField->setRenderer($renderer);
        }
        return parent::_prepareForm();
    }
}
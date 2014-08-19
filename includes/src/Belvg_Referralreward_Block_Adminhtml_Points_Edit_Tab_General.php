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
 * @copyright  Copyright (c) 2010 - 2014 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_Block_Adminhtml_Points_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $data = $this->_getFormData();
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('general', array(
            'legend' => Mage::helper('core')->__('Config'),
            'class'  => 'fieldset-wide',
        ));

        $fieldset->addField('vector', 'select', array(
            'label'    => Mage::helper('core')->__('Types'),
            'title'    => Mage::helper('core')->__('Types'),
            'name'     => 'change[vector]',
            'required' => FALSE,
            'values'   => Mage::getModel('referralreward/points_vector')->getValues(),
        ));

        $fieldset->addField('points', 'text', array(
            'label'    => Mage::helper('core')->__('Points'),
            'title'    => Mage::helper('core')->__('Points'),
            'name'     => 'change[points]',
            'required' => TRUE,
        ));

        $form->setValues($data);

        return parent::_prepareForm();
    }

    protected function _getFormData()
    {
        $data = Mage::getSingleton('adminhtml/session')->getReferralrewardData();
        if ($data) {
            Mage::getSingleton('adminhtml/session')->setReferralrewardData(NULL);
        } elseif (Mage::registry('referralreward_data')) {
            $data = Mage::registry('referralreward_data')->getData();
        }

        return $data;
    }
}

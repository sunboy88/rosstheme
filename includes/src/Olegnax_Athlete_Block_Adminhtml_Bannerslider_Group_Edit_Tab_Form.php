<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Block_Adminhtml_Bannerslider_Group_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{

		$data = array();
		if (Mage::getSingleton('adminhtml/session')->getAthleteBannersliderGroupData()) {
			$data = Mage::getSingleton('adminhtml/session')->getAthleteBannersliderGroupData();
		} elseif (Mage::registry('athlete_bannerslider_group_data')) {
			$data = Mage::registry('athlete_bannerslider_group_data')->getData();
		}

		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('athlete_bannerslider_group_form', array('legend' => Mage::helper('athlete')->__('Group information')));

        $fieldset->addField('group_name', 'text', array(
            'label' => Mage::helper('athlete')->__('Group name '),
            'required' => true,
            'name' => 'group_name',
        ));

		$fieldset->addField('slide_width', 'text', array(
            'label' => Mage::helper('athlete')->__('Slide width'),
            'required' => true,
            'name' => 'slide_width',
        ));

		$fieldset->addField('slide_height', 'text', array(
            'label' => Mage::helper('athlete')->__('Slide height'),
            'required' => true,
            'name' => 'slide_height',
        ));

		if (Mage::getSingleton('adminhtml/session')->getAthleteBannersliderGroupData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getAthleteBannersliderGroupData());
			Mage::getSingleton('adminhtml/session')->setAthleteBannersliderGroupData(null);
		} elseif (Mage::registry('athlete_bannerslider_group_data')) {
			$form->setValues(Mage::registry('athlete_bannerslider_group_data')->getData());
		}
		return parent::_prepareForm();
	}
}
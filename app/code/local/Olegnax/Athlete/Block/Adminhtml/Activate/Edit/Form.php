<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */
class Olegnax_Athlete_Block_Adminhtml_Activate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$isElementDisabled = false;
		$form = new Varien_Data_Form();

		$fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('adminhtml')->__('Activate Parameters')));

		$fieldset->addField('store_id', 'multiselect', array(
				'name' => 'stores[]',
				'label' => Mage::helper('cms')->__('Store View'),
				'title' => Mage::helper('cms')->__('Store View'),
				'required' => true,
				'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
				'value' => 0,
				'disabled' => $isElementDisabled
			));

		$fieldset->addField('setup_cms', 'checkbox', array(
			'label' => Mage::helper('athlete')->__('Create Cms Pages & Blocks'),
			'required' => false,
			'name' => 'setup_cms',
			'value' => 1,
		))->setIsChecked(1);

		$form->setAction($this->getUrl('*/*/activate'));
		$form->setMethod('post');
		$form->setUseContainer(true);
		$form->setId('edit_form');

		$this->setForm($form);

		return parent::_prepareForm();
	}
}

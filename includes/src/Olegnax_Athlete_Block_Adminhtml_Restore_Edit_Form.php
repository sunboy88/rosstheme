<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Block_Adminhtml_Restore_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$isElementDisabled = false;
		$form = new Varien_Data_Form();

		$fieldset = $form->addFieldset('fieldset_config', array('legend' => Mage::helper('adminhtml')->__('Restore
		Theme Settings')));

		$fieldset->addField('restore_settings', 'checkbox', array(
				'name' => 'restore_settings',
				'value' => 1,
				'label' => Mage::helper('athlete')->__('Restore default theme settings'),
				'title' => Mage::helper('athlete')->__('Restore default theme settings'),
			)
		);

		$fieldset->addField('store_id', 'multiselect', array(
			'name' => 'stores[]',
			'label' => Mage::helper('cms')->__('Store View'),
			'title' => Mage::helper('cms')->__('Store View'),
			'required' => true,
			'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			'value' => 0,
			'disabled' => $isElementDisabled
		));

		$fieldset->addField('clear_scope', 'checkbox', array(
				'name' => 'clear_scope',
				'value' => 1,
				'label' => Mage::helper('athlete')->__('Clear Configuration Scopes'),
				'title' => Mage::helper('athlete')->__('Clear Configuration Scopes'),
				'note' => Mage::helper('athlete')->__('Check if you want to clear theme settings for all scopes ( default, websites, stores ) '),
			)
		);

		$fieldset_cms = $form->addFieldset('fieldset_cms', array('legend' => Mage::helper('adminhtml')->__('Restore
		CMS Pages / Blocks')));
		$fieldset_cms->addField('restore_pages', 'checkbox', array(
			'label' => Mage::helper('athlete')->__('Restore Default Cms Pages'),
			'required' => false,
			'name' => 'restore_pages',
			'value' => 1,
		));
		$fieldset_cms->addField('overwrite_pages', 'select', array(
			'label' => Mage::helper('athlete')->__('Overwrite existing pages'),
			'note' => Mage::helper('athlete')->__('If set to "Yes", restored pages will overwrite existing pages
		    with the same identifiers.'),
			'name' => 'overwrite_pages',
			'values' => array(
				array(
					'value' => 0,
					'label' => Mage::helper('athlete')->__('No'),
				),
				array(
					'value' => 1,
					'label' => Mage::helper('athlete')->__('Yes'),
				),
			),
		));
		$fieldset_cms->addField('restore_blocks', 'checkbox', array(
			'label' => Mage::helper('athlete')->__('Restore Default Static blocks'),
			'required' => false,
			'name' => 'restore_blocks',
			'value' => 1,
		));
		$fieldset_cms->addField('overwrite_blocks', 'select', array(
			'label' => Mage::helper('athlete')->__('Overwrite existing blocks'),
			'note' => Mage::helper('athlete')->__('If set to "Yes", restored blocks will overwrite existing blocks
		    with the same identifiers.'),
			'name' => 'overwrite_blocks',
			'values' => array(
				array(
					'value' => 0,
					'label' => Mage::helper('athlete')->__('No'),
				),
				array(
					'value' => 1,
					'label' => Mage::helper('athlete')->__('Yes'),
				),
			),
		));

		$form->setAction($this->getUrl('*/*/restore'));
		$form->setMethod('post');
		$form->setUseContainer(true);
		$form->setId('edit_form');

		$this->setForm($form);

		return parent::_prepareForm();
	}
}

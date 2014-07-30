<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Block_Adminhtml_Athleterevolution_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{

		$model = Mage::registry('athleteslideshow_athleterevolution');

		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('athleteslideshow_form', array('legend' => Mage::helper('athleteslideshow')->__('Revolution Slide information')));

		$fieldset->addField('store_id', 'multiselect', array(
			'name' => 'stores[]',
			'label' => Mage::helper('athleteslideshow')->__('Store View'),
			'title' => Mage::helper('athleteslideshow')->__('Store View'),
			'required' => true,
			'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
		));

		$fieldset->addField('transition', 'select', array(
			'label' => Mage::helper('athleteslideshow')->__('Transition'),
			'name' => 'transition',
			'values' => array(
				array(
					'value' => 'boxslide',
					'label' => Mage::helper('athleteslideshow')->__('boxslide'),
				),
				array(
					'value' => 'boxfade',
					'label' => Mage::helper('athleteslideshow')->__('boxfade'),
				),
				array(
					'value' => 'slotzoom-horizontal',
					'label' => Mage::helper('athleteslideshow')->__('slotzoom-horizontal'),
				),
				array(
					'value' => 'slotslide-horizontal',
					'label' => Mage::helper('athleteslideshow')->__('slotslide-horizontal'),
				),
				array(
					'value' => 'slotfade-horizontal',
					'label' => Mage::helper('athleteslideshow')->__('slotfade-horizontal'),
				),
				array(
					'value' => 'slotzoom-vertical',
					'label' => Mage::helper('athleteslideshow')->__('slotzoom-vertical'),
				),
				array(
					'value' => 'slotslide-vertical',
					'label' => Mage::helper('athleteslideshow')->__('slotslide-vertical'),
				),
				array(
					'value' => 'slotfade-vertical',
					'label' => Mage::helper('athleteslideshow')->__('slotfade-vertical'),
				),
				array(
					'value' => 'curtain-1',
					'label' => Mage::helper('athleteslideshow')->__('curtain-1'),
				),
				array(
					'value' => 'curtain-2',
					'label' => Mage::helper('athleteslideshow')->__('curtain-2'),
				),
				array(
					'value' => 'curtain-3',
					'label' => Mage::helper('athleteslideshow')->__('curtain-3'),
				),
				array(
					'value' => 'slideleft',
					'label' => Mage::helper('athleteslideshow')->__('slideleft'),
				),
				array(
					'value' => 'slideright',
					'label' => Mage::helper('athleteslideshow')->__('slideright'),
				),
				array(
					'value' => 'slideup',
					'label' => Mage::helper('athleteslideshow')->__('slideup'),
				),
				array(
					'value' => 'slidedown',
					'label' => Mage::helper('athleteslideshow')->__('slidedown'),
				),
				array(
					'value' => 'fade',
					'label' => Mage::helper('athleteslideshow')->__('fade'),
				),
				array(
					'value' => 'random',
					'label' => Mage::helper('athleteslideshow')->__('random'),
				),
				array(
					'value' => 'slidehorizontal',
					'label' => Mage::helper('athleteslideshow')->__('slidehorizontal'),
				),
				array(
					'value' => 'slidevertical',
					'label' => Mage::helper('athleteslideshow')->__('slidevertical'),
				),
				array(
					'value' => 'papercut',
					'label' => Mage::helper('athleteslideshow')->__('papercut'),
				),
				array(
					'value' => 'flyin',
					'label' => Mage::helper('athleteslideshow')->__('flyin'),
				),
				array(
					'value' => 'turnoff',
					'label' => Mage::helper('athleteslideshow')->__('turnoff'),
				),
				array(
					'value' => 'cube',
					'label' => Mage::helper('athleteslideshow')->__('cube'),
				),
				array(
					'value' => '3dcurtain-vertical',
					'label' => Mage::helper('athleteslideshow')->__('3dcurtain-vertical'),
				),
				array(
					'value' => '3dcurtain-horizontal',
					'label' => Mage::helper('athleteslideshow')->__('3dcurtain-horizontal'),
				),
			),
			'note' => 'The appearance transition of this slide',
		));

		$fieldset->addField('masterspeed', 'text', array(
			'label' => Mage::helper('athleteslideshow')->__('Masterspeed'),
			'required' => false,
			'name' => 'masterspeed',
			'note' => 'Set the Speed of the Slide Transition. Default 300, min:100 max:2000.'
		));
		$fieldset->addField('slotamount', 'text', array(
			'label' => Mage::helper('athleteslideshow')->__('Slotamount'),
			'required' => false,
			'name' => 'slotamount',
			'note' => 'The number of slots or boxes the slide is divided into. If you use boxfade, over 7 slots can be juggy.'
		));
		$fieldset->addField('link', 'text', array(
			'label' => Mage::helper('athleteslideshow')->__('Slide Link'),
			'required' => false,
			'name' => 'link',
		));

		$data = array();
		$out = '';
		if (Mage::getSingleton('adminhtml/session')->getAthleterevolutionData()) {
			$data = Mage::getSingleton('adminhtml/session')->getAthleterevolutionData();
		} elseif (Mage::registry('athleterevolution_data')) {
			$data = Mage::registry('athleterevolution_data')->getData();
		}

		if (!empty($data['image'])) {
			$url = Mage::getBaseUrl('media') . $data['image'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}

		$fieldset->addField('image', 'file', array(
			'label' => Mage::helper('athleteslideshow')->__('Image'),
			'required' => false,
			'name' => 'image',
			'note' => $out,
		));

		$out = '';
		if (!empty($data['thumb'])) {
			$url = Mage::getBaseUrl('media') . $data['thumb'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}

		$fieldset->addField('thumb', 'file', array(
			'label' => Mage::helper('athleteslideshow')->__('Slide thumb'),
			'required' => false,
			'name' => 'thumb',
			'note' => 'An Alternative Source for thumbs. If not defined a copy of the background image will be used in resized form. ' . $out,
		));

		$fieldset->addField('text', 'textarea', array(
			'label'     => Mage::helper('athleteslideshow')->__('Slide Content'),
			'required'  => false,
			'name'      => 'text',
		));

		$fieldset->addField('status', 'select', array(
			'label' => Mage::helper('athleteslideshow')->__('Status'),
			'name' => 'status',
			'values' => array(
				array(
					'value' => 1,
					'label' => Mage::helper('athleteslideshow')->__('Enabled'),
				),
				array(
					'value' => 2,
					'label' => Mage::helper('athleteslideshow')->__('Disabled'),
				),
			),
		));

		$fieldset->addField('sort_order', 'text', array(
			'label' => Mage::helper('athleteslideshow')->__('Sort Order'),
			'required' => false,
			'name' => 'sort_order',
		));

		if (Mage::getSingleton('adminhtml/session')->getAthleterevolutionData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getAthleterevolutionData());
			Mage::getSingleton('adminhtml/session')->getAthleterevolutionData(null);
		} elseif (Mage::registry('athleterevolution_data')) {
			$form->setValues(Mage::registry('athleterevolution_data')->getData());
		}
		return parent::_prepareForm();
	}
}
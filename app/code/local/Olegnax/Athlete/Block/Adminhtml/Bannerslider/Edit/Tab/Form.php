<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Block_Adminhtml_Bannerslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{

		$data = array();
		if (Mage::getSingleton('adminhtml/session')->getAthleteBannersliderData()) {
			$data = Mage::getSingleton('adminhtml/session')->getAthleteBannersliderData();
		} elseif (Mage::registry('athlete_bannerslider_data')) {
			$data = Mage::registry('athlete_bannerslider_data')->getData();
		}

		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('athlete_bannerslider_form', array('legend' => Mage::helper('athlete')->__('Athlete Slide information')));
		$fieldset->addType('colorpicker', 'Olegnax_Athlete_Block_Adminhtml_Bannerslider_Helper_Form_Colorpicker');

		$fieldset->addField('slide_group', 'select', array(
			'label' => Mage::helper('athlete')->__('Slide group'),
			'name' => 'slide_group',
			'values' => Mage::getModel('athlete/bannerslider_group')->toOptionArray(),
		));

		$fieldset->addField('store_id', 'multiselect', array(
			'name' => 'stores[]',
			'label' => Mage::helper('athlete')->__('Store View'),
			'title' => Mage::helper('athlete')->__('Store View'),
			'required' => true,
			'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
		));

        $fieldset->addField('slide_bg', 'colorpicker', array(
            'label' => Mage::helper('athlete')->__('Slide background'),
            'name' => 'slide_bg',
            'note' => 'Leave empty to use default colors',
        ));

		$out = '';
		if (!empty($data['image'])) {
			$url = Mage::getBaseUrl('media') . $data['image'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}

		$fieldset->addField('image_remove', 'checkbox', array(
			'label' => Mage::helper('athlete')->__('Delete Background Image'),
			'required' => false,
			'name' => 'image_remove',
			'onclick' => 'this.value = this.checked ? 1 : 0;',
		));
		$fieldset->addField('image', 'file', array(
			'label' => Mage::helper('athlete')->__('Background Image'),
			'required' => false,
			'name' => 'image',
			'note' => $out,
		));

		$out = '';
		if (!empty($data['imageX2'])) {
			$url = Mage::getBaseUrl('media') . $data['imageX2'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}

		$fieldset->addField('imageX2_remove', 'checkbox', array(
			'label' => Mage::helper('athlete')->__('Delete Background Image for Retina'),
			'required' => false,
			'name' => 'imageX2_remove',
			'onclick' => 'this.value = this.checked ? 1 : 0;',
		));
		$fieldset->addField('imageX2', 'file', array(
			'label' => Mage::helper('athlete')->__('Background Image for Retina'),
			'required' => false,
			'name' => 'imageX2',
			'note' => 'Upload double size image for retina<br/>' . $out,
		));

        $fieldset->addField('title_color', 'colorpicker', array(
            'label' => Mage::helper('athlete')->__('Title color'),
            'name' => 'title_color',
            'note' => 'Leave empty to use default colors',
        ));
        $fieldset->addField('title_bg', 'colorpicker', array(
            'label' => Mage::helper('athlete')->__('Title background'),
            'name' => 'title_bg',
            'note' => 'Leave empty to use default colors',
        ));
        $fieldset->addField('title_position', 'select', array(
            'label' => Mage::helper('athlete')->__('Title position'),
            'name' => 'title_position',
            'values' => Mage::getModel('athlete/config_bannerslider_position')->toOptionArray(),
        ));
		$fieldset->addField('title', 'textarea', array(
			'label' => Mage::helper('athlete')->__('Title'),
			'required' => false,
			'name' => 'title',
		));

        $fieldset->addField('link_color', 'colorpicker', array(
            'label' => Mage::helper('athlete')->__('Link color'),
            'name' => 'link_color',
            'note' => 'Leave empty to use default colors',
        ));
        $fieldset->addField('link_bg', 'colorpicker', array(
            'label' => Mage::helper('athlete')->__('Link background'),
            'name' => 'link_bg',
            'note' => 'Leave empty to use default colors',
        ));
		$fieldset->addField('link_text', 'text', array(
			'label' => Mage::helper('athlete')->__('Link text'),
			'required' => false,
			'name' => 'link_text',
		));
		$fieldset->addField('link_href', 'text', array(
			'label' => Mage::helper('athlete')->__('Link Url'),
			'required' => false,
			'name' => 'link_href',
		));

		$fieldset->addField('status', 'select', array(
			'label' => Mage::helper('athlete')->__('Status'),
			'name' => 'status',
			'values' => array(
				array(
					'value' => 1,
					'label' => Mage::helper('athlete')->__('Enabled'),
				),
				array(
					'value' => 2,
					'label' => Mage::helper('athlete')->__('Disabled'),
				),
			),
		));

		$fieldset->addField('sort_order', 'text', array(
			'label' => Mage::helper('athlete')->__('Sort Order'),
			'required' => false,
			'name' => 'sort_order',
		));

		if (Mage::getSingleton('adminhtml/session')->getAthleteBannersliderData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getAthleteBannersliderData());
			Mage::getSingleton('adminhtml/session')->setAthleteBannersliderData(null);
		} elseif (Mage::registry('athlete_bannerslider_data')) {
			$form->setValues(Mage::registry('athlete_bannerslider_data')->getData());
		}
		return parent::_prepareForm();
	}
}
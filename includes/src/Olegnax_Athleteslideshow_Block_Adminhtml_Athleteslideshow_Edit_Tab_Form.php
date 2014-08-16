<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Block_Adminhtml_Athleteslideshow_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{

		$data = array();
		if (Mage::getSingleton('adminhtml/session')->getAthleteslideshowData()) {
			$data = Mage::getSingleton('adminhtml/session')->getAthleteslideshowData();
		} elseif (Mage::registry('athleteslideshow_data')) {
			$data = Mage::registry('athleteslideshow_data')->getData();
		}

		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('athleteslideshow_form', array('legend' => Mage::helper('athleteslideshow')->__('Athlete Slide information')));
		$fieldset->addType('colorpicker', 'Olegnax_Athleteslideshow_Block_Adminhtml_Athleteslideshow_Helper_Form_Colorpicker');

		$fieldset->addField('store_id', 'multiselect', array(
			'name' => 'stores[]',
			'label' => Mage::helper('athleteslideshow')->__('Store View'),
			'title' => Mage::helper('athleteslideshow')->__('Store View'),
			'required' => true,
			'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
		));

		$out = '';
		if (!empty($data['image'])) {
			$url = Mage::getBaseUrl('media') . $data['image'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}

		$fieldset->addField('image_remove', 'checkbox', array(
			'label' => Mage::helper('athleteslideshow')->__('Delete Background Image'),
			'required' => false,
			'name' => 'image_remove',
			'onclick' => 'this.value = this.checked ? 1 : 0;',
		));
		$fieldset->addField('image', 'file', array(
			'label' => Mage::helper('athleteslideshow')->__('Background Image'),
			'required' => false,
			'name' => 'image',
			'note' => $out,
		));

		$fieldset->addField('title_color', 'colorpicker', array(
			'label' => Mage::helper('athleteslideshow')->__('Title color'),
			'name' => 'title_color',
			'note' => 'Leave empty to use default colors',
		));
		$fieldset->addField('title_bg', 'colorpicker', array(
			'label' => Mage::helper('athleteslideshow')->__('Title background'),
			'name' => 'title_bg',
			'note' => 'Leave empty to use default colors',
		));

		$fieldset->addField('title', 'textarea', array(
			'label' => Mage::helper('athleteslideshow')->__('Title'),
			'required' => false,
			'name' => 'title',
		));

		$fieldset->addField('link_color', 'colorpicker', array(
			'label' => Mage::helper('athleteslideshow')->__('Link color'),
			'name' => 'link_color',
			'note' => 'Leave empty to use default colors',
		));
		$fieldset->addField('link_bg', 'colorpicker', array(
			'label' => Mage::helper('athleteslideshow')->__('Link background'),
			'name' => 'link_bg',
			'note' => 'Leave empty to use default colors',
		));
		$fieldset->addField('link_hover_color', 'colorpicker', array(
			'label' => Mage::helper('athleteslideshow')->__('Link hover color'),
			'name' => 'link_hover_color',
			'note' => 'Leave empty to use default colors',
		));
		$fieldset->addField('link_hover_bg', 'colorpicker', array(
			'label' => Mage::helper('athleteslideshow')->__('Link hover background'),
			'name' => 'link_hover_bg',
			'note' => 'Leave empty to use default colors',
		));

		$fieldset->addField('link_text', 'text', array(
			'label' => Mage::helper('athleteslideshow')->__('Link text'),
			'required' => false,
			'name' => 'link_text',
		));
		$fieldset->addField('link_href', 'text', array(
			'label' => Mage::helper('athleteslideshow')->__('Link Url'),
			'required' => false,
			'name' => 'link_href',
		));

		$out = '';
		if (!empty($data['banner_1_img'])) {
			$url = Mage::getBaseUrl('media') . $data['banner_1_img'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="banner_1_img">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}
		$fieldset->addField('banner_1_img_remove', 'checkbox', array(
			'label' => Mage::helper('athleteslideshow')->__('Delete Banner 1 Image'),
			'required' => false,
			'name' => 'banner_1_img_remove',
			'onclick' => 'this.value = this.checked ? 1 : 0;',
		));
		$fieldset->addField('banner_1_img', 'file', array(
			'label' => Mage::helper('athleteslideshow')->__('Banner 1 image'),
			'required' => false,
			'name' => 'banner_1_img',
			'note' => $out,
		));
		$out = '';
		if (!empty($data['banner_1_imgX2'])) {
			$url = Mage::getBaseUrl('media') . $data['banner_1_imgX2'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}
		$fieldset->addField('banner_1_imgX2_remove', 'checkbox', array(
			'label' => Mage::helper('athlete')->__('Delete Banner 1 image for Retina'),
			'required' => false,
			'name' => 'banner_1_imgX2_remove',
			'onclick' => 'this.value = this.checked ? 1 : 0;',
		));
		$fieldset->addField('banner_1_imgX2', 'file', array(
			'label' => Mage::helper('athlete')->__('Banner 1 image for Retina'),
			'required' => false,
			'name' => 'banner_1_imgX2',
			'note' => 'Upload double size image for retina<br/>' . $out,
		));
		$fieldset->addField('banner_1_href', 'text', array(
			'label' => Mage::helper('athleteslideshow')->__('Banner 1 Url'),
			'required' => false,
			'name' => 'banner_1_href',
		));

		$out = '';
		if (!empty($data['banner_2_img'])) {
			$url = Mage::getBaseUrl('media') . $data['banner_2_img'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="banner_2_img">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}
		$fieldset->addField('banner_2_img_remove', 'checkbox', array(
			'label' => Mage::helper('athleteslideshow')->__('Delete Banner 2 Image'),
			'required' => false,
			'name' => 'banner_2_img_remove',
			'onclick' => 'this.value = this.checked ? 1 : 0;',
		));
		$fieldset->addField('banner_2_img', 'file', array(
			'label' => Mage::helper('athleteslideshow')->__('Banner 2 image'),
			'required' => false,
			'name' => 'banner_2_img',
			'note' => $out,
		));
		$out = '';
		if (!empty($data['banner_2_imgX2'])) {
			$url = Mage::getBaseUrl('media') . $data['banner_2_imgX2'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}
		$fieldset->addField('banner_2_imgX2_remove', 'checkbox', array(
			'label' => Mage::helper('athlete')->__('Delete Banner 2 image for Retina'),
			'required' => false,
			'name' => 'banner_2_imgX2_remove',
			'onclick' => 'this.value = this.checked ? 1 : 0;',
		));
		$fieldset->addField('banner_2_imgX2', 'file', array(
			'label' => Mage::helper('athlete')->__('Banner 2 image for Retina'),
			'required' => false,
			'name' => 'banner_2_imgX2',
			'note' => 'Upload double size image for retina<br/>' . $out,
		));
		$fieldset->addField('banner_2_href', 'text', array(
			'label' => Mage::helper('athleteslideshow')->__('Banner 2 Url'),
			'required' => false,
			'name' => 'banner_2_href',
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

		if (Mage::getSingleton('adminhtml/session')->getAthleteslideshowData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getAthleteslideshowData());
			Mage::getSingleton('adminhtml/session')->getAthleteslideshowData(null);
		} elseif (Mage::registry('athleteslideshow_data')) {
			$form->setValues(Mage::registry('athleteslideshow_data')->getData());
		}
		return parent::_prepareForm();
	}
}
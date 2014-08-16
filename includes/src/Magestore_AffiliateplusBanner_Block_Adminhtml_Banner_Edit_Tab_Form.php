<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusbanner Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Block_Adminhtml_Banner_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_AffiliateplusBanner_Block_Adminhtml_Affiliateplusbanner_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getBannerData()) {
            $data = Mage::getSingleton('adminhtml/session')->getBannerData();
            Mage::getSingleton('adminhtml/session')->setBannerData(null);
        } elseif (Mage::registry('banner_data')) {
            $data = Mage::registry('banner_data')->getData();
        }
        $obj = new Varien_Object($data);
        
        $fieldset = $form->addFieldset('banner_form', array(
            'legend'=>Mage::helper('affiliateplus')->__('Banner information')
        ));

        $inStore = $this->getRequest()->getParam('store');
        $defaultLabel = Mage::helper('affiliateplus')->__('Use Default');
        $defaultTitle = Mage::helper('affiliateplus')->__('-- Please Select --');
        $scopeLabel = Mage::helper('affiliateplus')->__('STORE VIEW');
        if (!$inStore) {
            $disabledTitle = false;
        } else {
            $disabledTitle = !$obj->getData('title_in_store');
        }
        $isDefaultTitleCheck = $disabledTitle ? 'checked="checked"': '';
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('affiliateplus')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
            'disabled'  => $disabledTitle,
            'after_element_html'    => $inStore
                ? '</td><td class="use-default">
            <input id="title_default" name="title_default" type="checkbox" value="1" class="checkbox config-inherit" '
            . $isDefaultTitleCheck . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
            <label for="title_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
            </td><td class="scope-label">
            ['.$scopeLabel.']'
                : '</td><td class="scope-label">['.$scopeLabel.']',
        ));
        
        if (!$inStore) {
            $disabledStatus = false;
        } else {
            $disabledStatus = !$obj->getData('status_in_store');
        }
        $isDefaultStatusCheck = $disabledStatus ? 'checked="checked"': '';
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('affiliateplus')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('affiliateplus')->__('Enabled'),
                ),
                array(
                    'value'     => 2,
                    'label'     => Mage::helper('affiliateplus')->__('Disabled'),
                ),
            ),
            'disabled'  => $disabledStatus,
            'after_element_html'    => $inStore
                ? '</td><td class="use-default">
                <input id="status_default" name="status_default" type="checkbox" value="1"'
                .' class="checkbox config-inherit" ' . $isDefaultStatusCheck
                . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                <label for="status_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
                </td><td class="scope-label">
                ['.$scopeLabel.']'
                : '</td><td class="scope-label">['.$scopeLabel.']',
        ));

        Mage::dispatchEvent('affiliateplus_adminhtml_add_field_banner_form', array(
            'fieldset' => $fieldset,
            'form' => $form
        ));

        /** Types Options */
        $fieldset->addField('type_id', 'select', array(
            'label'     => Mage::helper('affiliateplus')->__('Banner Type'),
            'name'      => 'type_id',
            'required'  => true,
            'values'    => Mage::helper('affiliateplusbanner')->getOptionArray(),
            'disabled'  => (bool) $obj->getData('banner_id'),
            'onchange'  => 'changeBannerType(this)',
            'after_element_html'    => '</td><td class="label"><a href="javascript:showBannerRotatorTab()" title="'
                . Mage::helper('affiliateplus')->__('Select Rotator Banners') . '" id="type_id_rotator_banners">'
                . Mage::helper('affiliateplus')->__('Select Rotator Banners') . '</a>'
        ));
        
        if ($obj->getData('source_file')) {
            $isRequired = false;
        } else {
            $isRequired = true;
        }
        $sourceConfig = array(
            'label'     => Mage::helper('affiliateplus')->__('Source File'),
            'name'      => 'source_file',
            'required'  => $isRequired,
        );
        $widthConfig = array(
            'label'     => Mage::helper('affiliateplus')->__('Width (px)'),
            // 'required'  => true,
            'name'      => 'width',
        );
        $heightConfig = array(
            'label'     => Mage::helper('affiliateplus')->__('Height (px)'),
            // 'required'  => true,
            'name'      => 'height',
        );
        
        // Image banner option
        $imgBanner = Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_IMAGE;
        $fieldset->addField("source_file$imgBanner", 'file', $sourceConfig);
        $fieldset->addField("width$imgBanner", 'text', $widthConfig);
        $fieldset->addField("height$imgBanner", 'text', $heightConfig);
        
        // Flash banner option
        $flashBanner = Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_FLASH;
        $fieldset->addField("source_file$flashBanner", 'file', $sourceConfig);
        $fieldset->addField("width$flashBanner", 'text', $widthConfig);
        $fieldset->addField("height$flashBanner", 'text', $heightConfig);
        
        // Hover banner option
        $hoverBanner = Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_HOVER;
        $fieldset->addField("source_file$hoverBanner", 'file', $sourceConfig);
        $fieldset->addField("width$hoverBanner", 'text', $widthConfig);
        $fieldset->addField("height$hoverBanner", 'text', $heightConfig);
        
        // Peel banner option
        $sourceConfig['label'] = Mage::helper('affiliateplus')->__('Small Image');
        $peelBanner = Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_PEEL;
        $fieldset->addField("source_file$peelBanner", 'file', $sourceConfig);
        $fieldset->addField("width$peelBanner", 'text', $widthConfig);
        $fieldset->addField("height$peelBanner", 'text', $heightConfig);
        
        $fieldset->addField('peel_image', 'file', array(
            'label'     => Mage::helper('affiliateplusbanner')->__('Large Image'),
            'name'      => 'peel_image',
        ));
        
        $fieldset->addField('peel_width', 'text', array(
            'label'     => Mage::helper('affiliateplusbanner')->__('Large Image Width'),
            'name'      => 'peel_width',
        ));
        
        $fieldset->addField('peel_height', 'text', array(
            'label'     => Mage::helper('affiliateplusbanner')->__('Large Image Height'),
            'name'      => 'peel_height',
        ));
        
        $fieldset->addField('peel_direction', 'select', array(
            'label'     => Mage::helper('affiliateplusbanner')->__('Banner Position'),
            'name'      => 'peel_direction',
            'values'    => Mage::helper('affiliateplusbanner')->getDirectionsArray(),
        ));
        
        // Rotator banner option
        $rotatorBanner = Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_ROTATOR;
        $fieldset->addField("width$rotatorBanner", 'text', $widthConfig);
        $fieldset->addField("height$rotatorBanner", 'text', $heightConfig);
        
        $depend = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap('type_id', 'type_id')
                ->addFieldMap("source_file$imgBanner", "source_file$imgBanner")
                ->addFieldMap("width$imgBanner", "width$imgBanner")
                ->addFieldMap("height$imgBanner", "height$imgBanner")
                ->addFieldDependence("source_file$imgBanner", 'type_id', $imgBanner)
                ->addFieldDependence("width$imgBanner", 'type_id', $imgBanner)
                ->addFieldDependence("height$imgBanner", 'type_id', $imgBanner)
                
                // Flash Banner fields
                ->addFieldMap("source_file$flashBanner", "source_file$flashBanner")
                ->addFieldMap("width$flashBanner", "width$flashBanner")
                ->addFieldMap("height$flashBanner", "height$flashBanner")
                ->addFieldDependence("source_file$flashBanner", 'type_id', $flashBanner)
                ->addFieldDependence("width$flashBanner", 'type_id', $flashBanner)
                ->addFieldDependence("height$flashBanner", 'type_id', $flashBanner)
                
                // Hover Banner fields
                ->addFieldMap("source_file$hoverBanner", "source_file$hoverBanner")
                ->addFieldMap("width$hoverBanner", "width$hoverBanner")
                ->addFieldMap("height$hoverBanner", "height$hoverBanner")
                ->addFieldDependence("source_file$hoverBanner", 'type_id', $hoverBanner)
                ->addFieldDependence("width$hoverBanner", 'type_id', $hoverBanner)
                ->addFieldDependence("height$hoverBanner", 'type_id', $hoverBanner)
                
                // Peel Banner fields
                ->addFieldMap("source_file$peelBanner", "source_file$peelBanner")
                ->addFieldMap("width$peelBanner", "width$peelBanner")
                ->addFieldMap("height$peelBanner", "height$peelBanner")
                ->addFieldMap('peel_image', 'peel_image')
                ->addFieldMap('peel_width', 'peel_width')
                ->addFieldMap('peel_height', 'peel_height')
                ->addFieldMap('peel_direction', 'peel_direction')
                ->addFieldDependence("source_file$peelBanner", 'type_id', $peelBanner)
                ->addFieldDependence("width$peelBanner", 'type_id', $peelBanner)
                ->addFieldDependence("height$peelBanner", 'type_id', $peelBanner)
                ->addFieldDependence('peel_image', 'type_id', $peelBanner)
                ->addFieldDependence('peel_width', 'type_id', $peelBanner)
                ->addFieldDependence('peel_height', 'type_id', $peelBanner)
                ->addFieldDependence('peel_direction', 'type_id', $peelBanner)
                
                // Banner Rotator Fields
                ->addFieldMap("width$rotatorBanner", "width$rotatorBanner")
                ->addFieldMap("height$rotatorBanner", "height$rotatorBanner")
                ->addFieldDependence("width$rotatorBanner", 'type_id', $rotatorBanner)
                ->addFieldDependence("height$rotatorBanner", 'type_id', $rotatorBanner);
        
        if ($obj->getData('type_id') != Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_TEXT
            && $obj->getData('banner_id')
        ) {
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'affiliateplus/banner/';
            $fieldset->addField('banner_view', 'note', array(
                'label'     => Mage::helper('affiliateplus')->__('Preview'),
                'text'      => $this->getLayout()->createBlock('affiliateplusbanner/adminhtml_banner_view')
                                    ->setBannerObj($obj->setStoreId($inStore))
                                    ->toHtml(),
            ));
            // $depend->addFieldMap('banner_view', 'banner_view')
            //        ->addFieldDependence('banner_view', 'type_id', $obj->getData('type_id'));
        }
        $this->setChild('form_after', $depend);
        foreach (Mage::helper('affiliateplusbanner')->getOptionHash() as $type => $label) {
            $obj->setData('source_file' . $type, $obj->getData('source_file'))
                ->setData('width' . $type, $obj->getData('width'))
                ->setData('height' . $type, $obj->getData('height'));
        }
        /** End types options */
        
        $fieldset->addField('link', 'text', array(
            'label'     => Mage::helper('affiliateplus')->__('Link'),
            'name'      => 'link',
        ));
        
        $fieldset->addField('target', 'select', array(
            'label'     => Mage::helper('affiliateplus')->__('Target'),
            'name'      => 'target',
            'values'    => Mage::helper('affiliateplusbanner')->getTargetArray()
        ));
        
        $fieldset->addField('rel_nofollow', 'select', array(
            'label'     => Mage::helper('affiliateplus')->__('Rel Nofollow'),
            'name'      => 'rel_nofollow',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')
                        ->toOptionArray()
        ));
        
        if ($obj->getData('banner_id')) {
            $actionCollection = Mage::getResourceModel('affiliateplus/action_collection');
            $actionCollection->getSelect()
                ->columns(array(
                    'raw_total'   => 'SUM(totals)',
                    'uni_total'  => 'SUM(is_unique)',
                ))
                ->group('type')
                ->where('banner_id = ?', $obj->getData('banner_id'));
            $traffics = array(
                'raw_click' => 0,
                'uni_click' => 0,
                'raw_view'  => 0,
                'uni_view'  => 0
            );
            foreach ($actionCollection as $item) {
                if ($item->getType() == '1') {
                    $traffics['raw_view'] = $item->getRawTotal();
                    $traffics['uni_view'] = $item->getUniTotal();
                } else {
                    $traffics['raw_click'] = $item->getRawTotal();
                    $traffics['uni_click'] = $item->getUniTotal();
                }
            }
            $fieldset->addField('clicks', 'note', array(
               'label'     => Mage::helper('affiliateplus')->__('Clicks (unique/ raw)'),
               'text'      => $traffics['uni_click'] . ' / ' . $traffics['raw_click'],
            ));
            $fieldset->addField('views', 'note', array(
               'label'     => Mage::helper('affiliateplus')->__('Impressions (unique/ raw)'),
               'text'      => $traffics['uni_view'] . ' / ' . $traffics['raw_view'],
            ));
        }
        
        $form->setValues($obj->getData());
        return parent::_prepareForm();
    }
}

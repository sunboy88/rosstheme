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
 * Affiliateplusbanner Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Block_Adminhtml_Banner_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('banner_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('affiliateplus')->__('Banner Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_AffiliateplusBanner_Block_Adminhtml_Affiliateplusbanner_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $showRotatorGrid = true;
        if ($banner = Mage::registry('banner_data')) {
            if ($banner->getData('type_id')
                && $banner->getData('type_id') != Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_ROTATOR
            ) {
                $showRotatorGrid = false;
            }
        }
        $this->addTab('form_section', array(
            'label'     => Mage::helper('affiliateplus')->__('Banner Information'),
            'title'     => Mage::helper('affiliateplus')->__('Banner Information'),
            'content'   => $this->getLayout()
                                ->createBlock('affiliateplusbanner/adminhtml_banner_edit_tab_form')
                                ->toHtml(),
        ));
        
        if (!$showRotatorGrid) {
            return parent::_beforeToHtml();
        }
        $this->addTab('banner_section', array(
            'label'     => Mage::helper('affiliateplus')->__('Rotator Banners'),
            'title'     => Mage::helper('affiliateplus')->__('Rotator Banners'),
            'url'       => $this->getUrl('*/*/banner', array(
                '_current'  => true,
                'id'        => $this->getRequest()->getParam('id')
            )),
            'class'     => 'ajax'
        ));
        return parent::_beforeToHtml();
    }
}
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
 * @package     Magestore_Geoip
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Onestepcheckout Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Geoip
 * @author      Magestore Developer
 */
class Magestore_Onestepcheckout_Block_Adminhtml_Country_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('geoip_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('onestepcheckout')->__('Upload New Country Postcode Database'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Geoip_Block_Adminhtml_Geoip_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('onestepcheckout')->__('Upload New Country Postcode Database'),
            'title'     => Mage::helper('onestepcheckout')->__('Upload New Country Postcode Database'),
            'content'   => $this->getLayout()
                                ->createBlock('onestepcheckout/adminhtml_country_edit_tab_form')
                                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
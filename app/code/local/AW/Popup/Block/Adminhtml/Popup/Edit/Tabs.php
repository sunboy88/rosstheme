<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Popup
 * @version    1.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Popup_Block_Adminhtml_Popup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('popup_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('popup')->__('Popup Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'general',
            array(
                 'label'   => Mage::helper('popup')->__('General'),
                 'title'   => Mage::helper('popup')->__('General'),
                 'content' => $this->getLayout()->createBlock('popup/adminhtml_popup_edit_tab_general')->toHtml(),
            )
        );

        $this->addTab(
            'content',
            array(
                 'label'   => Mage::helper('popup')->__('Content'),
                 'title'   => Mage::helper('popup')->__('Content'),
                 'content' => $this->getLayout()->createBlock('popup/adminhtml_popup_edit_tab_content')->toHtml(),
            )
        );

        $this->addTab(
            'mss',
            array(
                 'label'   => Mage::helper('popup')->__('Market Segmentation Suite'),
                 'title'   => Mage::helper('popup')->__('Market Segmentation Suite'),
                 'content' => $this->getLayout()->createBlock('popup/adminhtml_popup_edit_tab_mss')->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}
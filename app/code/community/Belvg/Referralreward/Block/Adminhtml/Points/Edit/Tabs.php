<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/**********************************************
 *        MAGENTO EDITION USAGE NOTICE        *
 **********************************************/
/* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/**********************************************
 *        DISCLAIMER                          *
 **********************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 **********************************************
 * @category   Belvg
 * @package    Belvg_Referralreward
 * @copyright  Copyright (c) 2010 - 2014 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_Block_Adminhtml_Points_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {        
        parent::__construct();
        $this->setId('points_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('referralreward')->__('Change Points'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('referralreward')->__('Points Add'),
            'title'     => Mage::helper('referralreward')->__('Points Add'),
            'content'   => $this->getLayout()->createBlock('referralreward/adminhtml_points_edit_tab_general')->toHtml(),
        ));

        $this->addTab('points_log', array(
            'label'     => Mage::helper('referralreward')->__('Log'),
            'title'     => Mage::helper('referralreward')->__('Log'),
            'content'   => $this->getLayout()->createBlock('referralreward/adminhtml_points_edit_tab_log', 'referralreward_edit_tab_log')->toHtml(),
        ));

        $this->addTab('info', array(
            'label'     => Mage::helper('referralreward')->__('Info'),
            'title'     => Mage::helper('referralreward')->__('Info'),
            'content'   => $this->getLayout()->createBlock('referralreward/adminhtml_points_edit_tab_info', 'referralreward_edit_tab_info')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}

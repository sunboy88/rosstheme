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

class Belvg_Referralreward_Block_Adminhtml_Points_Edit_Tab_Log extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setSaveParametersInSession(TRUE);
        $this->setId('pointsLog');
        $this->setDefaultSort('created_at');
        $this->setUseAjax(TRUE);
        //$this->setDefaultFilter(array('in_products' => 1));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/logGrid', array('_current' => TRUE));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('referralreward/points_log')->getCollection();
        $customerId = (int) Mage::app()->getRequest()->getParam('id');
        $collection->addFieldToFilter('customer_id', $customerId);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('type', array(
            'header'   => Mage::helper('referralreward')->__('Points come from'),
            'index'    => 'type',
            'filter'   => false,
            'renderer' => 'Belvg_Referralreward_Block_Adminhtml_Points_Edit_Tab_Log_Type',
        ));
        $this->addColumn('object_id', array(
            'header'   => Mage::helper('referralreward')->__('Info'),
            'index'    => 'object_id',
            'renderer' => 'Belvg_Referralreward_Block_Adminhtml_Points_Edit_Tab_Log_Info',
            'filter'   => false,
            'sortable' => false,
        ));
        $this->addColumn('points', array(
            'header' => Mage::helper('referralreward')->__('Available Points'),
            'index'  => 'points',
            'type'   => 'number',
        ));
        $this->addColumn('points_orig', array(
            'header' => Mage::helper('referralreward')->__('Points added'),
            'index'  => 'points_orig',
            'type'   => 'number',
        ));
        $this->addColumn('created_at', array(
            'header' => Mage::helper('referralreward')->__('Points added on'),
            'index'  => 'created_at',
            'type'   => 'datetime',
        ));

        if (Mage::helper('referralreward')->storeConfig('settings/point_lifetime_enabled')) {
            $this->addColumn('end_at', array(
                'header' => Mage::helper('referralreward')->__('Points expire'),
                'index'  => 'end_at',
                'type'   => 'datetime',
            ));
        }

        return parent::_prepareColumns();
    }

}

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

class Belvg_Referralreward_Block_Adminhtml_Points_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('pointsGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(TRUE);
    }

    /**
     * Prepare Frame Types Collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at');
        $collection
            ->joinTable('referralreward/points', 'customer_id=entity_id', array('points'), NULL, 'left');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Init Column of Grid Frame Types
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => $this->__('Customer Id'),
            'align'     => 'left',
            'width'     => '50px',
            'truncate'  => 50,
            'index'     => 'entity_id',
        ));

        $this->addColumn('email', array(
            'header'    => $this->__('Email'),
            'type'      => 'text',
            'align'     => 'left',            
            'truncate'  => 100,
            'index'     => 'email',
        ));

        $this->addColumn('points', array(
            'header'    => $this->__('Points'),
            'type'      => 'number',
            'align'     => 'right',            
            'truncate'  => 50,
            'index'     => 'points',
            'filter_condition_callback' => array($this, '_pointsFilter'),
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        $this->addColumn('action', array(
            'header'    =>  Mage::helper('customer')->__('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('customer')->__('Edit'),
                    'url'     => array('base' => '*/*/edit'),
                    'field'   => 'id',
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    } 

    protected function _pointsFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if (isset($value['from']) && $value['from']) {
            $collection->getSelect()->where('belvg_referralreward_points.points >= ' . $value['from']);
        }

        if (isset($value['to']) && $value['to']) {
            $collection->getSelect()->where('belvg_referralreward_points.points <= ' . $value['to']);
        }

        //print_r((string)$collection->getSelect()); die;

        return $this;
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * URL to Frame Type Edit Form
     *
     * return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}


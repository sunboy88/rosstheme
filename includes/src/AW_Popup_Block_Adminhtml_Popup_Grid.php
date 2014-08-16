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


class AW_Popup_Block_Adminhtml_Popup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('popupGrid');
        $this->setDefaultSort('popup_id');
        $this->setDefaultDir('ASC');
        $this->setDefaultLimit('20');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('popup/popup')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'popup_id',
            array(
                 'header' => Mage::helper('popup')->__('ID'),
                 'align'  => 'right',
                 'width'  => '50px',
                 'index'  => 'popup_id',
            )
        );

        $this->addColumn(
            'name',
            array(
                 'header' => Mage::helper('popup')->__('Name'),
                 'align'  => 'left',
                 'index'  => 'name',
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'store_view',
                array(
                     'header'                    => Mage::helper('sales')->__('Store'),
                     'index'                     => 'store_view',
                     'width'                     => '200px',
                     'align'                     => 'left',
                     'type'                      => 'store',
                     'store_view'                => true,
                     'display_deleted'           => false,
                     'filter_condition_callback' => array($this, 'websiteFilterCallback'),
                )
            );
        }

        $this->addColumn(
            'date_from',
            array(
                 'header' => Mage::helper('popup')->__('From'),
                 'align'  => 'center',
                 'width'  => '100px',
                 'index'  => 'date_from',
                 'type'   => 'date',
            )
        );

        $this->addColumn(
            'date_to',
            array(
                 'header' => Mage::helper('popup')->__('To'),
                 'align'  => 'center',
                 'width'  => '100px',
                 'index'  => 'date_to',
                 'type'   => 'date',
            )
        );

        $this->addColumn(
            'sort_order',
            array(
                 'header' => Mage::helper('popup')->__('Sort order'),
                 'align'  => 'center',
                 'width'  => '80px',
                 'index'  => 'sort_order',
            )
        );

        $this->addColumn(
            'status',
            array(
                 'header'  => Mage::helper('popup')->__('Status'),
                 'align'   => 'center',
                 'width'   => '100px',
                 'index'   => 'status',
                 'type'    => 'options',
                 'options' => Mage::getModel('popup/source_status')->toShortOptionArray(),
            )
        );

        $this->addColumn(
            'action',
            array(
                 'header'    => Mage::helper('popup')->__('Action'),
                 'width'     => '100px',
                 'align'     => 'center',
                 'type'      => 'action',
                 'getter'    => 'getId',
                 'actions'   => array(
                     array(
                         'caption' => Mage::helper('popup')->__('Edit'),
                         'url'     => array('base' => '*/*/edit'),
                         'field'   => 'id',
                     ),
                     array(
                         'caption' => Mage::helper('popup')->__('Delete'),
                         'url'     => array('base' => '*/*/delete'),
                         'field'   => 'id',
                         'confirm' => Mage::helper('popup')->__('Are you sure?'),
                     ),
                 ),
                 'filter'    => false,
                 'sortable'  => false,
                 'index'     => 'stores',
                 'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('popup_id');
        $this->getMassactionBlock()->setFormFieldName('popup');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                 'label'   => Mage::helper('popup')->__('Delete'),
                 'url'     => $this->getUrl('*/*/massDelete'),
                 'confirm' => Mage::helper('popup')->__('Are you sure?'),
            )
        );

        $statuses = Mage::getSingleton('popup/source_status')->toOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                 'label'      => Mage::helper('popup')->__('Change status'),
                 'url'        => $this->getUrl('*/*/massStatus', array('_current' => true)),
                 'additional' => array(
                     'visibility' => array(
                         'name'   => 'status',
                         'type'   => 'select',
                         'class'  => 'required-entry',
                         'label'  => Mage::helper('popup')->__('Status'),
                         'values' => $statuses,
                     )
                 ),
            )
        );
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function websiteFilterCallback($collection, $column)
    {
        $collection->addFilterByWebsite($column->getFilter()->getValue());
        return $this;
    }
}
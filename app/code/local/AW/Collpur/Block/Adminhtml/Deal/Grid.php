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
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Collpur_Block_Adminhtml_Deal_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('id');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {

        $this->setDefaultFilter(
                Mage::helper('collpur/adminhtml_deals')->process('gridFilter')
        );

        $collection = Mage::getModel('collpur/deal')->getCollection()->joinProcesses();
        $collection->isNativeCount = true;
        
        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->addAdditionalFields();
        return $this;
    }

    protected function _prepareColumns() {
        $helper = Mage::helper('collpur');

        $this->addColumn('id', array(
            'header' => $helper->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_ids', array(
                'header' => $helper->__('Store View'),
                'index' => 'store_ids',
                'type' => 'store',
                'store_all' => true,
                'store_view' => true,
                'sortable' => false,
                'filter_condition_callback' => array($this, 'filterStore'),
            ));
        }

        $this->addColumn('product_name', array(
            'header' => $helper->__('Product Name'),
            'index' => 'product_name'
        ));

        $this->addColumn('name', array(
            'header' => $helper->__('Deal Name'),
            'index' => 'name'
        ));

        $this->addColumn('available_from',
                array(
                    'header' => $helper->__('Available From'),
                    'index' => 'available_from',
                    'type' => 'datetime',
                    'gmtoffset' => true,
                    'default' => ' ---- '
        ));

        $this->addColumn('available_to',
                array(
                    'header' => $helper->__('Available To'),
                    'index' => 'available_to',
                    'type' => 'datetime',
                    'gmtoffset' => true,
                    'default' => ' ---- '
        ));

        $this->addColumn('price',
                array(
                    'header' => $helper->__('Price'),
                    'index' => 'price',
                    'type' => 'number'
        ));

        $this->addColumn('qty_to_reach_deal',
                array(
                    'header' => $helper->__('Qty to reach deal'),
                    'index' => 'qty_to_reach_deal',
                    'type' => 'number'
        ));

        $this->addColumn('purchases_left',
                array(
                    'header' => $helper->__('Purchases left to reach'),
                    'index' => 'purchases_left',
                    'type' => 'number'
        ));

        $this->addColumn('is_active',
                array(
                    'header' => $helper->__('Is Active'),
                    'index' => 'is_active',
                    'type' => 'options',
                    'options' => array(
                        0 => $this->__('No'),
                        1 => $this->__('Yes')
                    ),
        ));

        $this->addColumn('is_success',
                array(
                    'header' => $helper->__('Success'),
                    'index' => 'is_success',
                    'type' => 'options',
                    'options' => array(
                        0 => $this->__('No'),
                        1 => $this->__('Yes')
                    ),
        ));

        $this->addColumn('progress',
                array(
                    'header' => $helper->__('Condition'),
                    'index' => 'progress',
                    'type' => 'options',
                    'options' => Mage::getModel('collpur/source_progress')->toOptionArray()
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function addAdditionalFields() {
        foreach ($this->getCollection() as $item) {
            $item->setData('store_ids', explode(',', $item->getData('store_ids')));
        }
    }

    protected function filterStore($collection, $column) {
        $val = $column->getFilter()->getValue();

        if (!@$val)
            return;
        else
            $cond = "FIND_IN_SET('$val', {$column->getIndex()}) OR FIND_IN_SET('0', {$column->getIndex()})";

        $collection->getSelect()->where($cond);
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('deal');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('collpur')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => $this->__('Are you sure?')
        ));
    }

}

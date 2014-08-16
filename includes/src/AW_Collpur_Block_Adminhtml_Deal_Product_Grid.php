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


class AW_Collpur_Block_Adminhtml_Deal_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('review')->__('ID'),
            'width' => '50px',
            'index' => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('review')->__('Name'),
            'index' => 'name',
        ));

        if ((int) $this->getRequest()->getParam('store', 0)) {
            $this->addColumn('custom_name', array(
                'header' => Mage::helper('review')->__('Name in Store'),
                'index' => 'custom_name'
            ));
        }

        $this->addColumn('sku', array(
            'header' => Mage::helper('review')->__('SKU'),
            'width' => '80px',
            'index' => 'sku'
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('review')->__('Price'),
            'type' => 'currency',
            'index' => 'price'
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('review')->__('Qty'),
            'width' => '130px',
            'type' => 'number',
            'index' => 'qty'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('review')->__('Status'),
            'width' => '90px',
            'index' => 'status',
            'type' => 'options',
            'source' => 'catalog/product_status',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                    array(
                        'header' => Mage::helper('review')->__('Websites'),
                        'width' => '100px',
                        'sortable' => false,
                        'index' => 'websites',
                        'type' => 'options',
                        'options' => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/productGrid', array('_current' => true));
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/addsecondstep', array('product_id' => $row->getId()));
    }

    protected function _prepareMassaction() {
        return $this;
    }

}

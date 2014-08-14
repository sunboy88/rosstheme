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


class AW_Collpur_Block_Adminhtml_Deal_Coupon_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('couponGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection =
                        Mage::getModel('collpur/coupon')
                        ->getCollection()
                         ->joinStatuses();
        $collection
                ->getSelect()
                ->where('main_table.deal_id IN (?)', Mage::app()->getRequest()->getParam('id'))
                ->joinLeft(array('deals' => $collection->getTable('collpur/deal')),'main_table.deal_id = deals.id',array('deals.product_name'))
                ->joinLeft(
                       array('deal_purchases' => $collection->getTable('collpur/dealpurchases')),
                     'deal_purchases.id = main_table.purchase_id',
                      array('customer_name', 'order_id')
                 );

     //  echo $collection->getSelect();
    //   die;

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('coupon_code', array(
            'header' => Mage::helper('collpur')->__('Coupon code'),
            'index' => 'coupon_code',
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('collpur')->__('Customer Name'),
            'index' => 'customer_name',
        ));

        $this->addColumn('order_id', array(
            'header' => Mage::helper('collpur')->__('Order Id'),
            'index' => 'order_id',
        ));


        $this->addColumn('statuses', array(
            'header' => Mage::helper('collpur')->__('Coupon status'),
            'index' => 'statuses',
        ));
    }

    public function getGridUrl() { 
        return $this->getUrl('*/*/couponsGrid', array('_current' => true, 'id' => Mage::app()->getRequest()->getParam('id')));
    }

    protected function _getParams() {

        $controller = Mage::app()->getRequest()->getControllerName();
        $action = Mage::app()->getRequest()->getActionName();
        $id = Mage::app()->getRequest()->getParam('id');
        return array("controller" => $controller, "action" => $action, "id" => $id);
    }

    protected function _prepareMassaction() {

        $this->setMassactionIdField('main_table.id');
        $this->getMassactionBlock()->setFormFieldName('awcp_coupons');
 
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => $this->__('Delete'),
            'url' => $this->getUrl('*/adminhtml_coupons/massDelete', $this->_getParams()),
            'confirm' => $this->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('status', array(
            'label' => $this->__('Change status'),
            'url' => $this->getUrl('*/adminhtml_coupons/massStatus', $this->_getParams()),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => $this->__('Status'),
                    'values' => AW_Collpur_Model_Source_Coupons::toOptionArray()
                )
            )
        ));

        return $this;
    }

}

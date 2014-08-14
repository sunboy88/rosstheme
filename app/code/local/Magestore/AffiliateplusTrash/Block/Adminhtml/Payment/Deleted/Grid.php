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
 * @package     Magestore_AffiliateplusTrash
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplustrash Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusTrash
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusTrash_Block_Adminhtml_Payment_Deleted_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('deletedPaymentGrid');
        $this->setDefaultSort('payment_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
	{
		$collection = Mage::getModel('affiliateplus/payment')->getCollection();
        $collection->setShowDeleted()->addFieldToFilter('payment_is_deleted', 1);
        
		//event to join other table
		Mage::dispatchEvent('affiliateplus_adminhtml_join_payment_other_table', array('collection' => $collection));
		
		$storeId = $this->getRequest()->getParam('store');
		if($storeId)
			$collection->addFieldToFilter('store_ids', array('finset' => $storeId));
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
		
		$this->addColumn('payment_id', array(
			'header'    => Mage::helper('affiliateplus')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'payment_id',
			'type'		=> 'number'
		));
		
		$this->addColumn('account_email', array(
			'header'    => Mage::helper('affiliateplus')->__('Affiliate Account'),
			'index'     => 'account_email',
			'renderer'  => 'affiliateplus/adminhtml_transaction_renderer_account',
		));
	
		$this->addColumn('amount', array(
			'header'    => Mage::helper('affiliateplus')->__('Amount'),
			'width'     => '80px',
			'align'     =>'right',
			'index'     => 'amount',
			'type'  	=> 'price',
			'currency_code' => $currencyCode,
		));
        
        $this->addColumn('tax_amount', array(
			'header'    => Mage::helper('affiliateplus')->__('Tax'),
			'width'     => '80px',
			'align'     =>'right',
			'index'     => 'tax_amount',
			'type'  	=> 'price',
			'currency_code' => $currencyCode,
		));
	
		$this->addColumn('fee', array(
			'header'    => Mage::helper('affiliateplus')->__('Fee'),
			'width'     => '80px',
			'align'     =>'right',
			'index'     => 'fee',
			'type'  	=> 'price',
			'currency_code' => $currencyCode,
		));
		
		$this->addColumn('payment_method', array(
			'header'    => Mage::helper('affiliateplus')->__('Withdrawal Method'),
			'index'     => 'payment_method',
			'renderer'  => 'affiliateplus/adminhtml_payment_renderer_info',
            'type'      => 'options',
            'options'   => Mage::helper('affiliateplus/payment')->getAllPaymentOptionArray()
		));
        
		//add event to add more column
		//$this->removeColumn('transaction_id');
	  	Mage::dispatchEvent('affiliateplus_adminhtml_change_column_payment_grid', array('grid' => $this));
		
		
		$this->addColumn('request_time', array(
			'header'    => Mage::helper('affiliateplus')->__('Time'),
			'width'     => '180px',
			'align'     =>'right',
			'index'     => 'request_time',
			'type'		=> 'date'
		));
	
		$this->addColumn('status', array(
			'header'    => Mage::helper('affiliateplus')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
				1 =>  Mage::helper('affiliateplus')->__('Pending'),
				2 =>  Mage::helper('affiliateplus')->__('Processing'),
				3 =>  Mage::helper('affiliateplus')->__('Completed'),
                                4 =>  Mage::helper('affiliateplus')->__('Canceled')
			),
		));
	
		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('affiliateplus')->__('Action'),
				'width'     => '80px',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('affiliateplus')->__('View'),
						'url'       => array('base'=> '*/*/edit'),
						'field'     => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
		));
		
		return parent::_prepareColumns();
	}
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('payment_id');
        $this->getMassactionBlock()->setFormFieldName('payment');
        
        $this->getMassactionBlock()->addItem('restore', array(
            'label'     => Mage::helper('affiliateplus')->__('Restore'),
            'url'       => $this->getUrl('*/*/massRestore'),
            'confirm'   => Mage::helper('affiliateplus')->__('Are you sure?'),
        ));
        
        return $this;
    }
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	
	public function getGridUrl()
    {
        return $this->getUrl('*/*/deletedGrid', array('_current'=>true));
    }
}

<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Block_Adminhtml_Bannerslider_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('bannersliderGroupGrid');
		$this->setDefaultSort('group_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('athlete/bannerslider_group')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('group_id', array(
			'header' => Mage::helper('athlete')->__('ID'),
			'align' => 'right',
			'width' => '50px',
			'index' => 'group_id',
		));
		$this->addColumn('group_name', array(
			'header' => Mage::helper('athlete')->__('Name'),
			'align' => 'right',
			'index' => 'group_name',
		));

		$this->addColumn('slide_width', array(
			'header' => Mage::helper('athlete')->__('Slide width'),
			'align' => 'left',
			'index' => 'slide_width',
		));
		$this->addColumn('slide_height', array(
			'header' => Mage::helper('athlete')->__('Slide height'),
			'align' => 'left',
			'index' => 'slide_height',
		));

		$this->addColumn('action',
			array(
				'header' => Mage::helper('athlete')->__('Action'),
				'width' => '100',
				'type' => 'action',
				'getter' => 'getId',
				'actions' => array(
					array(
						'caption' => Mage::helper('athlete')->__('Edit'),
						'url' => array('base' => '*/*/edit'),
						'field' => 'id'
					)
				),
				'filter' => false,
				'sortable' => false,
				'index' => 'group_id',
				'is_system' => true,
			));

		return parent::_prepareColumns();
	}

	protected function _afterLoadCollection()
	{
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('group_id');
		$this->getMassactionBlock()->setFormFieldName('bannerslider_group');

		$this->getMassactionBlock()->addItem('delete', array(
			'label' => Mage::helper('athlete')->__('Delete'),
			'url' => $this->getUrl('*/*/massDelete'),
			'confirm' => Mage::helper('athlete')->__('Are you sure?')
		));

		return $this;
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}
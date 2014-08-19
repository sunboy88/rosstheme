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
 * @package     Magestore_Geoip
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Onestepcheckout Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Onestepcheckout
 * @author      Magestore Developer
 */
class Magestore_Onestepcheckout_Block_Adminhtml_Country_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Geoip_Block_Adminhtml_Geoip_Edit_Tab_Form
     */
    public function __construct()
    {	
        parent::__construct();
        $this->setId('countryGrid');		
		$this->setDefaultDir('DESC');
        $this->setUseAjax(true);
		$this->setSaveParametersInSession(true);
		$up_version = $this->getRequest()->getParam('up_version');
		if(isset($up_version) && $up_version == 1)
			$this->setDefaultSort('last_version');
		else
			$this->setDefaultSort('update_date');
    }
	
	public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case 0:
                $class = 'grid-severity-major';
                break;
            case 1:
                $class = 'grid-severity-notice';
                break;           
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }
	
    protected function _addColumnFilterToCollection($column)
    {
		return parent::_addColumnFilterToCollection($column);
    }
	
	protected function _prepareCollection()
    {		
		$collection = Mage::getModel('onestepcheckout/countrylist')->getCollection()
																   ->addFieldToFilter('type',array('neq'=>1));		
		// $collection = Mage::getResourceModel('directory/country_collection');//->loadData()->toOptionArray(false);
		$this->setCollection($collection);					
		return parent::_prepareCollection();
    }
	
	protected function _prepareColumns()
    {
		
		$this->addColumn('country_code', array(
            'header'    => Mage::helper('onestepcheckout')->__('Country Code'),
            'sortable'  => true,
            'width'     => 70,
            'index'     => 'country_code',          
        ));
		
		$this->addColumn('country_name', array(
            'header'    => Mage::helper('onestepcheckout')->__('Country Name'),
            'sortable'  => true,           
            'index'     => 'country_name', 
			// 'renderer'	=> 'Magestore_Onestepcheckout_Block_Adminhtml_Renderer_Country',						
        ));
		
		$this->addColumn('current_version', array(
            'header'    => Mage::helper('onestepcheckout')->__('Current Version'),
            'sortable'  => true,           
			 'width'     => 70,
            'index'     => 'current_version', 			 		
        ));

		$this->addColumn('last_version', array(
            'header'    => Mage::helper('onestepcheckout')->__('Latest version'),
            'sortable'  => true,  
			 'width'     => 70,
            'index'     => 'last_version', 			 		
        ));
		
		$this->addColumn('current_records', array(
            'header'    => Mage::helper('onestepcheckout')->__('Records Updated'),
            'sortable'  => true,  
			 'width'     => 70,
            'index'     => 'current_records', 			 		
        ));
		
		$this->addColumn('total_records', array(
            'header'    => Mage::helper('onestepcheckout')->__('Records Total'),
            'sortable'  => true,  
			 'width'     => 70,
            'index'     => 'total_records', 			 		
        ));
		
		$this->addColumn('status', array(
            'header'    => Mage::helper('onestepcheckout')->__('Status'),
            'sortable'  => true,           
            'index'     => 'status',
			'width' 	=> 120,
			'type'		=> 'options',
            'options'   => Mage::getModel('onestepcheckout/countrylist')->getStatuses(),
			'frame_callback' => array($this, 'decorateStatus'),
        ));
		
		$this->addColumn('update_date', array(
            'header'    => Mage::helper('onestepcheckout')->__('Latest update time'),
            'sortable'  => true,
            'width'     => 200,
			'type'   	=> 'datetime',
            'index'     => 'update_date', 			
        ));
		
		
		$this->addColumn('action',
            array(
                'header'    =>  Mage::helper('onestepcheckout')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('onestepcheckout')->__('Update Now'),                        
                        'url'       => array('base'=> 'onestepcheckoutadmin/adminhtml_country/showCountryIp'),
                        'field'     => 'id',						
						// 'onclick'	=> 'importCountry(\''.$this->linkImport().'\');return false;',
						'popup'		=> '',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		return parent::_prepareColumns();
	}
	public function getGridUrl()
    {	
		return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/grid', array(
				'_current'=>true				
            ));
	}
	
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('country_code');
        $this->getMassactionBlock()->setFormFieldName('countryid');
           
        $this->getMassactionBlock()->addItem('import', array(
            'label'=> Mage::helper('onestepcheckout')->__('Import Geoip Database'),
            //'url'    => $this->getUrl('*/*/showGeoip', array('_current'=>true)),			          
            // 'onclick'    => 'alert("3")',			          
        ));	
        return $this;
    }
	
	public function linkImport()
    {
		$link = Mage::getSingleton('adminhtml/url')->getUrl('onestepcheckoutadmin/adminhtml_geoip/showGeoip',array(
			'website' => $this->getRequest()->getParam('website'),					
		));
		return $link;
	}	
	
	public function getRowUrl($row)
    {
        return false;
    }
}
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
 * @package     Magestore_AffiliateplusBanner
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusbanner Banner List Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Block_Adminhtml_Banner_Edit_Tab_Banner
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('bannerGrid');
        $this->setDefaultSort('main_table.banner_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_banners' => 1));
        }
    }
    
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_banners') {
            $bannerIds = $this->_getSelectedBanners();
            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.banner_id', array('in' => $bannerIds));
            } else {
                $this->getCollection()->addFieldToFilter('main_table.banner_id', array('nin' => $bannerIds));
            }
            return $this;
        }
        return parent::_addColumnFilterToCollection($column);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('affiliateplus/banner_collection');
        $collection->setStoreId($this->getRequest()->getParam('store'));
        $collection->getSelect()
            ->joinLeft(array('r' => $collection->getTable('affiliateplusbanner/rotator')),
                    'main_table.banner_id = r.banner_id AND r.parent_id = ' . 
                        $this->getRequest()->getParam('id', 0),
                    array('position'))
            ->where('main_table.banner_id != ?', $this->getRequest()->getParam('id', 0));
        $collection->addFieldToFilter('main_table.type_id',
            array('nin' => array(
                Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_ROTATOR
            )
        ));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('in_banners', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'in_banners',
            'values'            => $this->_getSelectedBanners(),
            'align'             => 'center',
            'index'             => 'banner_id',
            'filter_index'      => 'main_table.banner_id',
            'use_index'         => true
        ));
        
        $this->addColumn('rotator_banner_id', array(
            'header'    => $this->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'banner_id',
            'filter_index'  => 'main_table.banner_id'
        ));
        
        $this->addColumn('rotator_title', array(
            'header'    => $this->__('Title'),
            'index'     => 'title',
            'filter_index'  => 'main_table.title'
        ));
        
        $this->addColumn('rotator_link', array(
            'header'    => $this->__('Link'),
            'index'     => 'link',
            'filter_index'  => 'main_table.link'
        ));
        
        $this->addColumn('rotator_type_id', array(
            'header'    => $this->__('Type'),
            'width'     => '100px',
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::helper('affiliateplusbanner')->getOptionHash()
        ));
        
        /* $this->addColumn('rotator_status', array(
            'header'    => $this->__('Status'),
            'width'     => '100px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1   => $this->__('Enabled'),
                2   => $this->__('Disabled')
            )
        )); */
        
        $this->addColumn('position', array(
            'header'    => $this->__('Frequency'),
            'name'      => 'position',
            'index'     => 'position',
            'filter_index'  => 'r.position',
            'editable'  => true,
            'edit_only' => true
        ));
    }
    
    public function getRowUrl($row)
    {
        return '';// $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/bannerGrid', array(
            '_current'  => true,
            'id'        => $this->getRequest()->getParam('id')
        ));
    }
    
    protected function _getSelectedBanners()
    {
        $banners = $this->getBanners();
        if (!is_array($banners)) {
            $banners = array_keys($this->getSelectedRelatedBanners());
        }
        return $banners;
    }
    
    public function getSelectedRelatedBanners()
    {
        $banners = array();
        $collection = Mage::getResourceModel('affiliateplusbanner/rotator_collection')
                ->addFieldToFilter('parent_id', $this->getRequest()->getParam('id'));
        foreach ($collection as $child) {
            $banners[$child->getData('banner_id')] = array('position' => $child->getData('position'));
        }
        return $banners;
    }
}
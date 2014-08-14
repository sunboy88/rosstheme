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
 * Affiliateplusbanner Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Block_Adminhtml_Banner_Grid
    extends Magestore_Affiliateplus_Block_Adminhtml_Banner_Grid
{
    /**
     * prepare banner grid columns
     * 
     * @return Magestore_AffiliateplusBanner_Block_Adminhtml_Banner_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        
        $this->addColumnAfter('target', array(
            'header'    => $this->__('Target'),
            'index'     => 'target',
            'type'      => 'options',
            'options'   => Mage::helper('affiliateplusbanner')->getTargetHash()
        ), 'link');
        
        $this->addColumnAfter('rel_nofollow', array(
            'header'    => $this->__('Nofollow'),
            'index'     => 'rel_nofollow',
            'type'      => 'options',
            'options'   => array(
                '0'     => $this->__('No'),
                '1'     => $this->__('Yes')
            )
        ), 'target');
        
        $this->addColumnAfter('type_id', array(
            'header'    => $this->__('Type'),
            'width'     => '100px',
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::helper('affiliateplusbanner')->getOptionHash()
        ), 'rel_nofollow');
        
        return $this->sortColumnsByOrder();
    }
}
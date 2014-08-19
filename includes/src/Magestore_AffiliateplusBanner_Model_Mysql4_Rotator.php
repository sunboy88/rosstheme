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
 * Affiliateplusbanner Rotator Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Model_Mysql4_Rotator extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('affiliateplusbanner/rotator', 'rotator_id');
    }
    
    /**
     * Delete all excess banner
     * 
     * @param int $parentId
     * @param array $keepBanners
     * @return Magestore_AffiliateplusBanner_Model_Mysql4_Rotator
     */
    public function deleteExcessBanner($parentId, $keepBanners = array())
    {
        $conditions   = array();
        $conditions[] = $this->_getWriteAdapter()->quoteInto('parent_id = ?', $parentId);
        if (count($keepBanners)) {
            $conditions[] = 'banner_id NOT IN (' . implode(',', $keepBanners) . ')';
        }
        try {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                $conditions
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }
    
    /**
     * get Existed banner Ids with parent id
     * 
     * @param int $parentId
     * @return array (child_id => ID) | false
     */
    public function getExistedBannerIds($parentId)
    {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), array('banner_id','rotator_id'))
                ->where('parent_id = ?', $parentId);
        return $this->_getReadAdapter()->fetchPairs($select);
    }
    
    /**
     * get Frequency of children banners
     * 
     * @param int $parentId
     * @return array (child_id => frequency) | false
     */
    public function getBannerFrequency($parentId)
    {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), array('banner_id','position'))
                ->where('parent_id = ?', $parentId);
        return $this->_getReadAdapter()->fetchPairs($select);
    }
}

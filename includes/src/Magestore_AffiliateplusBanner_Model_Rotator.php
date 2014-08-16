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
 * Affiliateplusbanner Rotator Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Model_Rotator extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliateplusbanner/rotator');
    }
    
    /**
     * Save all child banners for current banner (parent_id was setted)
     * 
     * @param array $childBanners
     * @return Magestore_AffiliateplusBanner_Model_Rotator
     */
    public function saveChildBanner($childBanners)
    {
        $childBannerIds = array_keys($childBanners);
        $existBannerIds = $this->getResource()
                ->deleteExcessBanner($this->getData('parent_id'), $childBannerIds)
                ->getExistedBannerIds($this->getData('parent_id'));
        
        foreach ($childBanners as $childId => $encodedData) {
            $bannerData = array();
            parse_str(base64_decode($encodedData), $bannerData);
            $this->addData($bannerData)
                 ->setData('banner_id', $childId);
            if ($existBannerIds && isset($existBannerIds[$childId])) {
                $this->setId($existBannerIds[$childId]);
            } else {
                $this->setId(null);
            }
            try {
                $this->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $this;
    }
    
    /**
     * get Random banner by rotator parent banner ID
     * 
     * @param int $parentId
     * @param int $storeId
     * @return Magestore_Affiliateplus_Model_Banner
     */
    public function getRandomBanner($parentId, $storeId = null)
    {
        $bannerId = null;
        $children = $this->getResource()->getBannerFrequency($parentId);
        if ($children && is_array($children)) {
            $randMax = 0;
            foreach ($children as $childId => $position) {
                if ($position < 0) {
                    unset($children[$childId]);
                } else {
                    $children[$childId] = $position + 1;
                    $randMax += $position + 1;
                }
            }
            $random = rand(1, $randMax);
            $randMax = 0;
            foreach ($children as $childId => $position) {
                if ($random > $randMax) {
                    $bannerId = $childId;
                    $randMax += $position;
                } else {
                    break;
                }
            }
        }
        
        $banner = Mage::getModel('affiliateplus/banner');
        $banner->setStoreId($storeId)->load($bannerId);
        return $banner;
    }
}
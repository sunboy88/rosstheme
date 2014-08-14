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
 * Affiliateplusbanner View Block
 * 
 * @category     Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Block_Adminhtml_Banner_View
    extends Mage_Core_Block_Template
{
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        
        $banner = $this->getBannerObj();
        if ($banner->getData('type_id') == Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_ROTATOR) {
            $randBanner = Mage::getSingleton('affiliateplusbanner/rotator')
                    ->getRandomBanner($banner->getData('banner_id'), $banner->getStoreId());
            if ($banner->getData('width')) {
                $randBanner->setData('width', $banner->getData('width'));
            }
            if ($banner->getData('height')) {
                $randBanner->setData('height', $banner->getData('height'));
            }
            $randBanner->setData('banner_id', $banner->getData('banner_id'))
                    ->setData('status', $banner->getData('status'))
                    ->setData('link', $banner->getData('link'))
                    ->setData('program_id', $banner->getData('program_id'))
                    ->setData('target', $banner->getData('target'))
                    ->setData('rel_nofollow', $banner->getData('rel_nofollow'));
            $this->setBannerObj($randBanner);
        }
        switch ($this->getBannerObj()->getData('type_id')) {
            case Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_FLASH:
                $this->setTemplate('affiliateplusbanner/flash.phtml');
                break;
            case Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_IMAGE:
            case Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_HOVER:
                $this->setTemplate('affiliateplusbanner/image.phtml');
                break;
            case Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_PEEL:
                $this->setTemplate('affiliateplusbanner/peel.phtml');
                break;
            default :
                $this->setTemplate('affiliateplusbanner/blank.phtml');
        }
        if (!$this->getBannerObj()->getData('source_file')) {
            $this->setTemplate('affiliateplusbanner/blank.phtml');
        }
        return $this;
    }
    
    /**
     * get Affiliate banner URL
     * 
     * @param string $fileName
     */
    public function getBannerUrl($fileName)
    {
        return Mage::getBaseUrl('media') . 'affiliateplus/banner/' . $fileName;
    }
    
    /**
     * get Page Peel Banner Params
     * 
     * @param array $config
     * @return string
     */
    public function getPagePeelParams($config = array())
    {
        $banner = $this->getBannerObj();
        $params = array();
        if ($banner->getPeelDirection() == Magestore_AffiliateplusBanner_Helper_Data::TOP_RIGHT_CORNER) {
            $params['bannertype'] = 'R';
        } else {
            $params['bannertype'] = 'L';
        }
        $params['bannerwidth'] = max($banner->getPeelWidth(), $banner->getWidth());
        $params['bannerheight'] = max($banner->getPeelHeight(), $banner->getHeight());
        
        $params['smallimg'] = $this->getBannerUrl($banner->getSourceFile());
        if ($banner->getPeelImage()) {
            $params['img'] = $this->getBannerUrl($banner->getPeelImage());
        } else {
            $params['img'] = $this->getBannerUrl($banner->getSourceFile());
        }
        
        $params['link'] = '';
        $params['bgcolor'] = '0xFFFFFF';
        
        $square = $params['bannerwidth'] * $params['bannerheight'];
        if ($square) {
            $ratio = $banner->getWidth() * $banner->getHeight() / $square;
            $percent = sqrt($ratio) * 100;
            $params['smallperc'] = (int)$percent;
        }
        
        foreach ($config as $key => $value) {
            $params[$key] = $value;
        }
        return http_build_query($params);
    }
    
    /**
     * get Pagepeel large image
     * 
     * @return string
     */
    public function getLargeImage()
    {
        $banner = $this->getBannerObj();
        if ($banner->getPeelImage()) {
            return $this->getBannerUrl($banner->getPeelImage());
        }
        return $this->getBannerUrl($banner->getSourceFile());
    }
    
    /**
     * get media url (library)
     * 
     * @param string $fileName
     * @return string
     */
    public function getMediaUrl($fileName)
    {
        return Mage::getBaseUrl('media') . 'affiliateplusbanner/' . $fileName;
    }
}
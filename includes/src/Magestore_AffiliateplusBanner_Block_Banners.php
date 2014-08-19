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
 * Affiliateplusbanner Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Block_Banners extends Magestore_Affiliateplus_Block_Account_Banner
{
    public function getTypesLabel()
    {
        return Mage::helper('affiliateplusbanner')->getOptionHash();
    }
    
    /**
     * get banner preview HTML
     * 
     * @param Magestore_Affiliateplus_Model_Banner $banner
     * @return string
     */
    public function getBannerPreview($banner)
    {
        $renderBlock = Mage::getBlockSingleton('affiliateplusbanner/view');
        $renderBlock->setRenderType('preview')
                ->setBanner($banner);
        return $renderBlock->toHtml();
    }
    
    /**
     * get Banner code to share
     * 
     * @param Magestore_Affiliateplus_Model_Banner $banner
     * @return string
     */
    public function getBannerCode($banner)
    {
        $renderBlock = Mage::getBlockSingleton('affiliateplusbanner/view');
        $renderBlock->setRenderType('code')
                ->setBanner($banner);
        return trim($renderBlock->toHtml());
    }
}
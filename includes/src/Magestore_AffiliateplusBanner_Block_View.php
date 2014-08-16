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
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Block_View extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setData('cache_lifetime', null);
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
    
    /**
     * get Banner direct URL
     * 
     * @return string
     */
    public function getBannerUrl()
    {
        $banner = $this->getBanner();
        $store  = Mage::app()->getStore($banner->getStoreId());
        $account = $this->getAccount();
        
        $url = Mage::helper('affiliateplus/url')->getUrlLink($banner->getLink());
        if (strpos($url, '?')) {
            $url .= '&acc='.$account->getIdentifyCode();
        } else {
            $url .= '?acc='.$account->getIdentifyCode();
        }
        if ($store->getId() != Mage::app()->getDefaultStoreView()->getId()) {
            $url .= '&___store='.$store->getCode();
        }
        if ($banner->getId()) {
            $url .= '&bannerid='.$banner->getId();
        }
        
        $urlParams = new Varien_Object(array(
            'helper'    => Mage::helper('affiliateplus/url'),
            'params'    => array(),
        ));
        Mage::dispatchEvent('affiliateplus_helper_get_banner_url', array(
            'banner'    => $banner,
            'url_params'    => $urlParams,
        ));
        $params = $urlParams->getParams();
        if (count($params)) {
            $url .= '&'.http_build_query($urlParams->getParams(), '', '&');
        }
        return $url;
    }
    
    /**
     * get PHP banner URL (ofter for text banner)
     * 
     * @return string
     */
    public function getPhpBannerUrl()
    {
        return $this->getBannerUrl();
    }
    
    /**
     * get JS banner URL 
     * 
     * @return string
     */
    public function getJsBannerUrl()
    {
        return $this->getUrl('affiliateplus/banner/image', array(
            'id'            => $this->getBanner()->getId(),
            'account_id'    => $this->getAccount()->getId(),
            'store_id'      => $this->getBanner()->getStoreId(),
            'type'          => 'javascript',
            'link'          => Mage::helper('core')->urlEncode($this->getBanner()->getLink())
        ));
    }
    
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $banner = $this->getBanner();
        if ($banner->getData('type_id') == Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_ROTATOR) {
            if ($this->getRenderType() != 'preview') {
                $this->setTemplate('affiliateplusbanner/rotator.phtml');
                return $this;
            }
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
            $this->setBanner($randBanner);
            $banner = $this->getBanner();
        }
        
        switch ($banner->getData('type_id')) {
            case Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_IMAGE:
                $this->setTemplate('affiliateplusbanner/image.phtml');
                break;
            case Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_FLASH:
                $this->setTemplate('affiliateplusbanner/flash.phtml');
                break;
            case Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_TEXT:
                $this->setTemplate('affiliateplusbanner/text.phtml');
                break;
            case Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_HOVER:
                $this->setTemplate('affiliateplusbanner/hover.phtml');
                break;
            case Magestore_AffiliateplusBanner_Helper_Data::BANNER_TYPE_PEEL:
                $this->setTemplate('affiliateplusbanner/peel.phtml');
                break;
        }
        
        return $this;
    }
    
    /**
     * get current affiliate account
     * 
     * @return Magestore_Affiliateplus_Model_Account
     */
    public function getAccount()
    {
        if (!$this->hasData('affiliate_account')) {
            $this->setData('affiliate_account', Mage::getSingleton('affiliateplus/session')->getAccount());
        }
        return $this->getData('affiliate_account');
    }
    
    /**
     * get PHP banner url (media link)
     * 
     * @return string
     */
    public function getPhpBannerMedia()
    {
        $url = $this->getJsUrl() . 'magestore/affiliateplus/banner.php?';
        $url .= 'id=' . $this->getBanner()->getId();
        $url .= '&account_id=' . $this->getAccount()->getId();
        $url .= '&store_id=' . $this->getBanner()->getStoreId();
        return $url;
        /*return $this->getUrl('affiliateplus/banner/image', array(
            'id'            => $this->getBanner()->getId(),
            'account_id'    => $this->getAccount()->getId(),
            'store_id'      => $this->getBanner()->getStoreId()
        ));*/
    }
    
    /**
     * get Direct banner url (media link)
     * 
     * @return string
     */
    public function getDirectBannerMedia()
    {
        return Mage::getBaseUrl('media') . 'affiliateplus/banner/' . $this->getBanner()->getSourceFile();
    }
    
    /**
     * get Direct Affiliate banner URL
     * 
     * @param string $fileName
     */
    public function getBannerMedia($fileName)
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
        $banner = $this->getBanner();
        $params = array();
        if ($banner->getPeelDirection() == Magestore_AffiliateplusBanner_Helper_Data::TOP_RIGHT_CORNER) {
            $params['bannertype'] = 'R';
        } else {
            $params['bannertype'] = 'L';
        }
        $params['bannerwidth'] = max($banner->getPeelWidth(), $banner->getWidth());
        $params['bannerheight'] = max($banner->getPeelHeight(), $banner->getHeight());
        if ($this->getRenderType() == 'preview' || $this->getRenderType() == 'rotator') {
            $params['smallimg'] = $this->getDirectBannerMedia();
        } else {
            $params['smallimg'] = $this->getPhpBannerMedia();
        }
        if ($banner->getPeelImage()) {
            $params['img'] = $this->getBannerMedia($banner->getPeelImage());
        } else {
            $params['img'] = $this->getDirectBannerMedia();
        }
        $params['link'] = $this->getBannerUrl();
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
        $banner = $this->getBanner();
        if ($banner->getPeelImage()) {
            return $this->getBannerMedia($banner->getPeelImage());
        }
        return $this->getDirectBannerMedia();
    }
    
    /**
     * get Json HTML for rotator banner
     * 
     * @return string
     */
    public function getJsonHtml()
    {
        $banner = $this->getBanner();
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

        $renderBlock = $this->getLayout()->createBlock('affiliateplusbanner/view', 'rotator_child_banner');
        $renderBlock->setRenderType('rotator')
                ->setAffiliateAccount($this->getAccount())
                ->setBanner($randBanner);
        return Zend_Json::encode(array('html' => $renderBlock->toHtml()));
    }
}
<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Collpur_Block_BaseDeal extends Mage_Core_Block_Template {

    const CATALOG_IMAGE_SIZE = 325;
    const PRODUCT_IMAGE_SIZE = 669;

    protected $_baseHolder = NULL;
    protected $_holderImagePath;
    protected $_varienImage;
    protected $_cacheDir = 'cache';
    protected $_jsonEncoder;
    public $_mode = 'catalog';
    public $imageType = 'small_image';
    public $translator;
    protected $registry = array();
    protected $_featuredDealId = NULL;
    protected $_rewriteResouce;
    protected $_currentStoreId;

    protected function _construct() {
        
        $this->_initBaseHolderImage();
        $this->translator = Mage::helper('collpur');
        $this->_jsonEncoder = new Zend_Json();
        $this->_rewriteResource = Mage::getResourceModel('collpur/rewrite');
        $this->_currentStoreId = Mage::app()->getStore()->getId();
    }

    private function register($id, $jsonString) {
        $this->registry[$id] = $this->_jsonEncoder->encode($jsonString);
    }

    public function getJsonConfig($id) {

        if (isset($this->registry[$id])) {
            return $this->registry[$id];
        }
        return NULL;
    }

    protected function _initBaseHolderImage() {

        $this->_baseHolder = new Mage_Catalog_Model_Product_Image();
        $this->_baseHolder->setDestinationSubDir($this->imageType);
        $this->_baseHolder->setBaseFile('/no_selection');
        $this->_holderImagePath = preg_replace("#" . Mage::getBaseDir() . "/#is", "", $this->_baseHolder->getBaseFile());
    }

    protected function _lookUpCache($image) {

        $root = Mage::getBaseDir();
        $cachePath = $this->initMediaPath($image,$this->_cacheDir,$this->_mode);       
        $cacheDir = $this->initMediaPath(NULL,$this->_cacheDir,$this->_mode);
        $basePath = $this->initMediaPath($image);

        if (!$image) {
            return $this->_holderImagePath;
        }
        if (file_exists($root.DS.$cachePath)) {  
            return $cachePath;
        } else if (file_exists($root.DS.$basePath)) {
            return $this->_initVarienImage($basePath, $image, $cacheDir, $this->_mode, $root);
        }

        return NULL;
    }

    public function initMediaPath($image=NULL,$cacheDir=NULL,$mode=NULL,$router=NULL,$web = NULL) {

        if(!$cacheDir && !$mode && !$router) {
            $path = 'media'.DS.AW_Collpur_Helper_Data::PREFIX_MODULE . DS . AW_Collpur_Helper_Data::ROUTER . DS . $image;
        }
        else if($image && $router) {            
            $path = 'media'.DS.AW_Collpur_Helper_Data::PREFIX_MODULE . DS . $router . DS .$image;
        }
        else if($image) {
            $path =  'media'.DS.AW_Collpur_Helper_Data::PREFIX_MODULE . DS . AW_Collpur_Helper_Data::ROUTER . DS . $cacheDir. DS .$mode. DS.$image;
        }
        else {
           $path =  'media'.DS.AW_Collpur_Helper_Data::PREFIX_MODULE . DS . AW_Collpur_Helper_Data::ROUTER . DS . $cacheDir. DS .$mode;
        }

        if($web) return Mage::getBaseUrl('web').$path;
        return $path;

     }

     protected function _initVarienImage($basePath, $imageName, $cacheDir, $mode, $root) {

        if ($mode == 'catalog') {
            if (!$width = (int) Mage::getStoreConfig('collpur/general/thumbnailsize')) {
                $width = self::CATALOG_IMAGE_SIZE;
            }
        } else {
            if (!$width = (int) Mage::getStoreConfig('collpur/general/imagesize')) {
                $width = self::PRODUCT_IMAGE_SIZE;
            }
        }

        try {
            $image = new Varien_Image($root.DS.$basePath);
            $image->keepAspectRatio(true);
            $image->keepTransparency(true);
            $image->keepFrame(false);
            $image->quality(90);

            if ($image->getOriginalWidth() <= $width) {
                $width = $image->getOriginalWidth();
            }

            $image->resize($width, $image->getOriginalHeight());
            $image->save($root.DS.$cacheDir, $imageName);
        } catch(Exception $e) { Mage::log($e->getMessage()); }
        
        return $cacheDir . DS . $imageName;
    }

    public function getBaseHolderImage() {

    }

    public function processDealName($deal) {
        if($deal->getName()) {
            return $deal->getName();
        }
        return $deal->getProductName();        
    }

    public function getTimeLeftToBuy($deal, $time = 'available_to') {

        $gmtTo = AW_Collpur_Helper_Data::getGmtTimestamp($deal->getData($time));
        $now = AW_Collpur_Helper_Data::getGmtTimestamp(true, true);
        $this->_cacheJsonString($deal, $now, $gmtTo);
        return Mage::helper('collpur/deals')->getTimeLeftToBuy(false, false, $now, $gmtTo);
    }

    private function _cacheJsonString($deal, $from, $to) {
        /* Cache json stirng for current deal */
        $this->register($deal->getId(), '{
           "dateNow":' . $from . ',
           "dateTo":' . $to . ',
           "translation":  {
                "day":"' . $this->__('day') . '",
                "days":"' . $this->__('days') . '",
                "hour":"' . $this->__('hour') . '",
                "hours":"' . $this->__('hours') . '",
                "minute":"' . $this->__('minute') . '",
                "minutes":"' . $this->__('minutes') . '",
                "second":"' . $this->__('seconds') . '",
                "seconds":"' . $this->__('seconds') . '"}
        }');
    }

    public function getDealImage($image, $type = NULL) {

        return Mage::getBaseUrl('web') . $this->_lookUpCache($image);
    }

    public function getSkinImage($name, $ext='png') {
        !$ext?$ext='':$ext='.'.$ext;
        return Mage::getDesign()->getSkinBaseUrl().'aw_collpur/images/'.$name.$ext;
    }

    public function getMenus() {

        return AW_Collpur_Model_Source_Menus::getMenusArray();
    }

    protected function _tabIsAllowed($menu) {
 
        if ($menu['skip'] || !$menu['size']) { return false; }
        if ($menu['key'] == AW_Collpur_Helper_Deals::FEATURED) {
            $this->_featuredDealId = Mage::getModel('collpur/deal')->getRandomFeaturedId();
        }
        return true;
    }

    protected function getDealTabUrl($param) {

        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
        return Mage::getUrl('deals', array('_secure' => Mage::app()->getStore(true)->isCurrentlySecure())).$param.$suffix;

    }

    public function checkCurrentTab($param) {
        if (!Mage::app()->getRequest()->getParam('section') && $param == AW_Collpur_Helper_Deals::FEATURED) {
            return "class = 'awcp-current'";
        }
        if (Mage::app()->getRequest()->getParam('section') == $param) {
            return "class = 'awcp-current'";
        }
    }

    public function filter($text) {
        $processorModelName = 'widget/template_filter';

        $processor = Mage::getModel($processorModelName);

        if ($processor instanceof Mage_Core_Model_Email_Template_Filter)
            return $processor->filter($text);
        else
            return $text;
    }


     public function _modifyCrumbs($layout,$bridge=false,$deal=false,$mode='product') {


         $sections = AW_Collpur_Helper_Deals::getSectionsAssoc();

         if ($mode == 'category') {            
            $currentSection = Mage::app()->getRequest()->getParam('section');
            $layout->getBlock('breadcrumbs')->addCrumb('home', array('label' => 'Home', 'title' => 'Home', 'link' => Mage::getBaseUrl()));
            if (array_key_exists($currentSection, $sections)) {
                $layout->getBlock('breadcrumbs')->addCrumb('category', array('label' => $this->__($sections[$currentSection]), 'title' => $this->__($sections[$currentSection])));
                $layout->getBlock('head')->setTitle($this->__($sections[$currentSection]));                
            }
            return;
        }

         $deal = $this->getDealInstance($deal->getId());
         if(!$deal->getId()) {
             return;
         }
         
         $disallowActive = false;
         if($deal->isNotRunning()) {
             $categoryCrumb = AW_Collpur_Helper_Deals::NOT_RUNNING;
             $categoryLabel = $this->__($sections[AW_Collpur_Helper_Deals::NOT_RUNNING]);
         }
         else if($deal->isRunning()) {
            $categoryCrumb = AW_Collpur_Helper_Deals::RUNNING;
            $categoryLabel = $this->__($sections[AW_Collpur_Helper_Deals::RUNNING]);
            $data = AW_Collpur_Model_Source_Menus::getMenusArray(false);
 
             foreach ($data as $info) {
                if ($info['key'] == AW_Collpur_Helper_Deals::RUNNING) {
                    if (!Mage::getStoreConfig("collpur/{$info['alias']}/enabled")) {
                        $disallowActive = true;
                        break;
                    }
                    $disallowActive = false;
                    break;
                }
                $disallowActive = false;
            }
         }
         else if($deal->isClosed()) {
           $categoryCrumb = AW_Collpur_Helper_Deals::CLOSED;
           $categoryLabel = $this->__($sections[AW_Collpur_Helper_Deals::CLOSED]);
         }
 
        $layout->getBlock('breadcrumbs')->addCrumb('home',array('label'=>'Home','title'=>'Home','link'=>Mage::getBaseUrl()));
        if(!$disallowActive) { $layout->getBlock('breadcrumbs')->addCrumb('category',array('label'=>$categoryLabel,'title'=>$categoryLabel,'link'=>$this->getDealTabUrl($categoryCrumb))); }
        $layout->getBlock('breadcrumbs')->addCrumb('product',array('label'=>$this->processDealName($deal)));
    }

    public function getDealInstance($id) {
        return Mage::getModel('collpur/deal')->load($id);
    }

    public function getDealPageLink($id)
    {
        $identifier = $this->_rewriteResource->loadByDealId($id, $this->_currentStoreId);
        $prefix = Mage::getStoreConfig('catalog/seo/product_url_suffix');
        return Mage::getUrl("deals/",array('_secure' => Mage::app()->getStore(true)->isCurrentlySecure()))."{$identifier}{$prefix}";
    }



}
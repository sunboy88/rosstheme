<?php
/**
 * @copyright  Copyright (c) 2009-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Shopby_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    const MIDDLE    = 0;
    const BEGINNING = 1;

    public function match(Zend_Controller_Request_Http $request)
    {


        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }

        $pageId = $request->getPathInfo();
        // remove suffix if any
        $suffix = Mage::helper('amshopby/url')->getUrlSuffix();
        if ($suffix && '/' != $suffix){
            $pageId = str_replace($suffix, '', $pageId);
        }

        //add trailing slash
        $pageId = trim($pageId, '/') . '/';

        $reservedKey = Mage::getStoreConfig('amshopby/seo/key') . '/';


        //  canon/
        //  electronics - false

        //  electronics/shopby/canon/
        //  electronics/shopby/red/
        //  electronics/shopby/

        //  shopby/
        //  shopby/red/
        //  shopby/canon/ - false
        //  shopby/manufacturer-canon/ - false
        //  manufacturer-canon/ - true

        // starts from shopby
        $isAllProductsPage = (substr($pageId, 0, strlen($reservedKey)) == $reservedKey);

        // has shopby in the middle
        $isCategoryPage = (false !== strpos($pageId, '/' . $reservedKey));

        if (!Mage::getStoreConfig('amshopby/seo/urls')) // Prevent using SEO urls with 'Use SEO URLs' disabled
        {
            // If path info have something after reserved key
            if (($isAllProductsPage || $isCategoryPage) &&
                substr($pageId, -strlen($reservedKey), strlen($reservedKey)) != $reservedKey)
            {
                return false;
            }
        }

        if ($isAllProductsPage){
            // no support for old style urls
            if ($this->hasBrandIn(self::MIDDLE, $pageId)){
                return false;
            }
        }

        if (!$isAllProductsPage && !$isCategoryPage){
            if (!$this->hasBrandIn(self::BEGINNING, $pageId)){
                return false;
            }
            //it is brand page and we modify the url to be in the old style
            $pageId = $reservedKey . $pageId;
        }

        // get layered navigation params as string
        list($cat, $params) = explode($reservedKey, $pageId, 2);
        $params = trim($params, '/');
        if ($params)
            $params = explode('/', $params);

        // remember for futire use in the helper
        if ($params){
            Mage::register('amshopby_current_params', $params);
        }

        $cat = trim($cat, '/');
        if ($cat){ // normal category
            // if somebody has old urls in the cache.
            if (!Mage::getStoreConfig('amshopby/seo/urls'))
                return false;

            // we do not use Mage::getVersion() here as it is not defined in the old versions.
            $isVersionEE13 = ('true' == (string)Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
            if ($isVersionEE13) {
                $urlRewrite = Mage::getModel('enterprise_urlrewrite/url_rewrite');
                /* @var $urlRewrite Enterprise_UrlRewrite_Model_Url_Rewrite */

                if (version_compare(Mage::getVersion(), '1.13.0.2', '>=')) {
                    $catReqPath = array('request' => $cat . $suffix, 'whole' => $cat);
                } 
                else {
                    $catReqPath = array($cat);
                }

                $urlRewrite
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->loadByRequestPath($catReqPath);
            } 
            else {
                $urlRewrite = Mage::getModel('core/url_rewrite');
                /* @var $urlRewrite Mage_Core_Model_Url_Rewrite */

                $cat = $cat . $suffix;
                $catReqPath = $cat;

                $urlRewrite
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->loadByRequestPath($catReqPath);
            }

            // todo check in ee13
            if (!$urlRewrite->getId()){
                $store = $request->getParam('___from_store');
                $store = Mage::app()->getStore($store)->getId();
                if (!$store){
                    return false;
                }

                $urlRewrite->setData(array())
                    ->setStoreId($store)
                    ->loadByRequestPath($catReqPath);

                if (!$urlRewrite->getId()){
                    return false;
                }
            }

            $request->setPathInfo($cat);
            $request->setModuleName('catalog');
            $request->setControllerName('category');
            $request->setActionName('view');

            if ($isVersionEE13) {
                $categoryId = str_replace('catalog/category/view/id/', '', $urlRewrite->getTargetPath());
                $request->setParam('id', $categoryId);
            }
            else {
                $request->setParam('id', $urlRewrite->getCategoryId());
                $urlRewrite->rewrite($request);
            }
        }
        else { // root category
            $realModule = 'Amasty_Shopby';

            $request->setPathInfo(trim($reservedKey, '/'));
            $request->setModuleName('amshopby');
            $request->setRouteName('amshopby');
            $request->setControllerName('index');
            $request->setActionName('index');
            $request->setControllerModule($realModule);

            $file = Mage::getModuleDir('controllers', $realModule) . DS . 'IndexController.php';
            include $file;

            //compatibility with 1.3
            $class = $realModule . '_IndexController';
            $controllerInstance = new $class($request, $this->getFront()->getResponse());

            $request->setDispatched(true);
            $controllerInstance->dispatch('index');
        }

        return true;
    }

    public function hasBrandIn($position, $pageId)
    {
        $code = Mage::getStoreConfig('amshopby/brands/attr');
        $code = trim(str_replace('_', Mage::getStoreConfig('amshopby/seo/special_char'), $code));


        if (!$code) {
            return false;
        }

        $options = Mage::helper('amshopby/url')->getAllFilterableOptionsAsHash();
        //ckeck if we have brand names
        if (empty($options[$code])) {
            return false;
        }

        $found[self::MIDDLE]    = false;
        $found[self::BEGINNING] = false;
        foreach ($options[$code] as $key => $id) {
            if (!Mage::getStoreConfig('amshopby/seo/hide_attributes')){
                $key = $code . Mage::getStoreConfig('amshopby/seo/option_char') . $key;

            }

            if (0 === strpos($pageId, $key . '/')) {
                $found[self::BEGINNING] = true;
            }

            if (false !== strpos($pageId, '/' . $key . '/')) {
                $found[self::MIDDLE] = true;
            }
        }

        return $found[$position];
    }
}
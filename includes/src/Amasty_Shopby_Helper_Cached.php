<?php
/**
 * @copyright Amasty
 */

class Amasty_Shopby_Helper_Cached extends Mage_Core_Helper_Abstract
{
    const CACHE_TAG = 'amshopby';

    private $lightCache = array();

    public function __construct()
    {
        //$this->debugCache();
    }

    private function debugCache()
    {
        $cache = Mage::app()->getCache();
        print_r($cache->getTags());
    }

    public function flushCache()
    {
        $this->_cleanCache(array(self::CACHE_TAG));
        if ($this->isCacheEnabled()) {
            /** @var Mage_Core_Model_Session $session */
            $session = Mage::getSingleton('adminhtml/session');
            $session->addSuccess($this->__('Amasty Improved Navigtion cache has been flushed'));
        }
    }

    protected function load($key)
    {
        if (array_key_exists($key, $this->lightCache)) {
            return $this->lightCache[$key];
        }

        if ($this->isCacheEnabled()) {
            $data = $this->_loadCache($this->makeCacheKey($key));
            if ($data === false) {
                return false;
            }

            $data = unserialize($data);

            //echo 'loaded ' . $key . PHP_EOL;

            $this->lightCache[$key] = $data;
            return $data;
        } else {
            return false;
        }
    }

    protected function save($data, $key)
    {
        $this->saveLite($data, $key);

        if ($this->isCacheEnabled()) {
            $this->_saveCache(serialize($data), $this->makeCacheKey($key), array(self::CACHE_TAG));
        }
    }

    protected function saveLite($data, $key)
    {
        $this->lightCache[$key] = $data;
    }

    private function makeCacheKey($key)
    {
        $storeId = Mage::app()->getStore()->getId();

        $isSearch = Mage::app()->getRequest()->getModuleName() == 'catalogsearch' ? 'search' : 'catalog';

        return 'amshopby_ ' . $isSearch . '_store' . $storeId . '_' . $key;
    }

    private function isCacheEnabled() {
        if (!defined('AMSHOPBY_CACHE_ENABLED')) {
            return true;
        }
        return AMSHOPBY_CACHE_ENABLED;
    }
}
<?php
/**
* @copyright Amasty.
*/ 

class Amasty_Shopby_Helper_Data extends Amasty_Shopby_Helper_Cached
{
    protected $_useSolr;
    
    const XML_PATH_SEO_PRICE_NOFOLLOW       = 'amshopby/seo/price_nofollow';
    const XML_PATH_SEO_PRICE_NOINDEX        = 'amshopby/seo/price_noindex';
    const XML_PATH_SEO_PRICE_RELNOFOLLOW    = 'amshopby/seo/price_rel_nofollow';

    public function getSeoPriceNofollow()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEO_PRICE_NOFOLLOW);
    }
    
    public function getSeoPriceNoindex()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEO_PRICE_NOINDEX);
    }
    
    public function getSeoPriceRelNofollow()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEO_PRICE_RELNOFOLLOW);
    }
    
    protected function _getFilters()
    {
        $cacheId = 'filters';

        $result = $this->load($cacheId);
        if ($result) {
            return $result;
        }

        //get all possible filters as collection
        $filterCollection = Mage::getResourceModel('amshopby/filter_collection')
                ->addFieldToFilter('show_on_view', 1)
                ->addTitles();
        // convert to array
        $filters = array();
        foreach ($filterCollection as $filter){
            $filters[$filter->getId()] = $filter;
        }

        $this->save($filters, $cacheId);
        return $filters;
    }

    /**
     * @return Amasty_Shopby_Model_Filter[]
     */
    public function getAttributesSettings()
    {
        $cacheId = 'attribute_settings';

        $result = $this->load($cacheId);
        if ($result) {
            return $result;
        }

        $attrCollection = Mage::getResourceModel('amshopby/filter_collection')->load();

        $attributes = array();
        foreach ($attrCollection as $row){
            $attributes[$row->getAttributeId()] = $row;
        }

        $this->save($attributes, $cacheId);
        return $attributes;
    }


    public function getIconsData()
    {
        $cacheId = 'icons_data';

        $result = $this->load($cacheId);
        if ($result) {
            return $result;
        }

        $filters = $this->_getFilters();

        $optionCollection = Mage::getResourceModel('amshopby/value_collection')
            ->addPositions()
            ->addFieldToFilter('img_medium', array('gt' => ''))  
            ->addValue();  

        $result = array();
        /** @var Amasty_Shopby_Helper_Url $hlp */
        $hlp = Mage::helper('amshopby/url');
        foreach ($optionCollection as $opt){
            
            $filterId = $opt->getFilterId();
            // it is possible when "use on view" = "false"
            if (empty($filters[$filterId]))
                continue;
                
            $filter = $filters[$filterId];
                            
            // seo urls fix when different values        
            $opt->setTitle($opt->getValue() ? $opt->getValue() : $opt->getTitle());
                
            $img  = $opt->getImgMedium();
            $query = array(
                $filter->getAttributeCode() => $opt->getOptionId(),
            );
            $url = $hlp->getFullUrl($query, true);

            $result[$opt->getOptionId()] = array(
                'url'   => str_replace('___SID=U&','', $url),
                'title' => $opt->getTitle(),
                'descr' => $opt->getDescr(),
                'img'   => Mage::getBaseUrl('media') . 'amshopby/' . $img,  
                'pos'   => $filter->getPosition(),  
                'pos2'  => $opt->getSortOrder(),  
            );    
        }

        $this->saveLite($result, $cacheId); //dependence on getFullUrl($query, true);
        return $result;
    }    
    
    /**
     * Returns HTML with attribute images
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $mode (view, list, grid)
     * @param array $names arrtibute codes to show images for
     * @param bool $exclude flag to indicate taht we need to show all attributes beside specified in $names
     * @return string
     */
    public function showLinks($product, $mode='view', $names=array(), $exclude=false)
    {
        $filters = $this->_getFilters();
        
        $items = array();
        foreach ($filters as $filter){
            $code = $filter->getAttributeCode(); 
            if (!$code){
                continue;
            }
            
            if ($names && in_array($code, $names) && $exclude)
                continue;
                
            if ($names && !in_array($code, $names) && !$exclude)
                continue;
            
            $optIds  = trim($product->getData($code), ','); 
            if (!$optIds && $product->isConfigurable()){
                $usedProds = $product->getTypeInstance(true)->getUsedProducts(null, $product);
                foreach ($usedProds as $child){
                    if ($child->getData($code)){
                        $optIds .= $child->getData($code) . ',';
                    }
                }
            }
            
            if ($optIds){
                $optIds = explode(',', $optIds);
                $optIds = array_unique($optIds);
                $iconsData = $this->getIconsData();
                foreach ($optIds as $id){
                    if (isset($iconsData[$id])){
                        $items[] = $iconsData[$id];
                    }
                }
            }
        }  
       
        //sort by position in the layered navigation
        usort($items, array('Amasty_Shopby_Helper_Data', '_srt'));
        
        //create block
        $block = Mage::getModel('core/layout')->createBlock('core/template')
            ->setArea('frontend')
            ->setTemplate('amasty/amshopby/links.phtml');
        $block->assign('_type', 'html')
            ->assign('_section', 'body')        
            ->setLinks($items)
            ->setMode($mode); // to be able to created different html
             
        return $block->toHtml();          
    }
    
    public static function _srt($a, $b)
     {
        $res = ($a['pos'] < $b['pos']) ? -1 : 1;
        if ($a['pos'] == $b['pos']){ 
            if ($a['pos2'] == $b['pos2'])
                $res = 0;
            else 
                $res = ($a['pos2'] < $b['pos2']) ? -1 : 1;
        }
        
        return $res;
     }
    
    public function isVersionLessThan($major, $minor)
    {
        $curr = explode('.', Mage::getVersion()); // 1.3. compatibility
        $need = func_get_args();
        foreach ($need as $k => $v){
            if ($curr[$k] != $v)
                return ($curr[$k] < $v);
        }
        return false;
    }
    
    /**
     * Gets params (6,17,89) from the request as array and sanitize them
     *
     * @param string $key attribute code
     * @return array
     */
    public function getRequestValues($key)
    {
       $v = Mage::app()->getRequest()->getParam($key);
       
       if (is_array($v)){//smth goes wrong
           return array();
       }
       
       if (preg_match('/^[0-9,]+$/', $v)){
            $v = array_unique(explode(',', $v));
       }
       else { 
            $v = array();
       }
       
       return $v;       
    } 
    
    /**
     * Check that amlanding is installed and filter enabled
     * @return boolean
     */
    public function landingNewFilter()
    {
        return ('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Xlanding/active') && Mage::helper('amlanding')->newFilterActive());
    }

    public function error404()
    {
        Mage::app()->getResponse()
            ->setHeader('HTTP/1.1','404 Not Found')
            ->setHeader('Status','404 File not found');

        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
        if (!Mage::helper('cms/page')->renderPage(Mage::app()->getFrontController()->getAction(), $pageId)) {
            header('Location: /');
            exit;
        }
        Mage::app()->getResponse()->sendResponse();
        exit;
    }

    /**
     * Display 404 error if multiple values was selected for 'Single Choice Only' attributes
     */
    public function restrictMultipleSelection()
    {
        $settings = $this->getAttributesSettings();
        /** @var Amasty_Shopby_Helper_Attributes $attributeHelper */
        $attributeHelper = Mage::helper('amshopby/attributes');
        $requestedCodes = $attributeHelper->getRequestedFilterCodes();
        $multiselectCodes = $this->getMultiselectAttributeCodes();

        foreach ($requestedCodes as $code => $value)
        {
            if (false !== strpos($value, ',')) // Multiple values
            {
                if (!in_array($code, $multiselectCodes)) {
                    $this->error404();
                }
            }
        }
    }

    protected function getMultiselectAttributeCodes()
    {
        $cacheId = 'multiselect_attribute_codes';

        $result = $this->load($cacheId);
        if ($result) {
            return $result;
        }

        /** @var Amasty_Shopby_Helper_Attributes $attributesHelper */
        $attributesHelper = Mage::helper('amshopby/attributes');
        $attributes = $attributesHelper->getFilterableAttributes();
        $settings = $this->getAttributesSettings();

        $result = array();
        foreach ($attributes as $attribute) {
            if (isset($settings[$attribute->getId()])) {
                if (!$settings[$attribute->getId()]->getSingleChoice()) {
                    $result[] = $attribute->getAttributeCode();
                }
            }
        }

        $this->save($result, $cacheId);
        return $result;
    }

    public function useSolr()
    {
        if (isset($this->_useSolr)) {
            return $this->_useSolr;
        }

        if ($this->isModuleEnabled('Enterprise_Search')) {
            /** @var Enterprise_Search_Helper_Data $helper */
            $helper = Mage::helper('enterprise_search');

            $moduleName = Mage::app()->getRequest()->getModuleName();
            if ($moduleName == 'catalog' || $moduleName == 'amshopby') {
                $result = $helper->getIsEngineAvailableForNavigation(true);
            } else if ($moduleName == 'catalogsearch') {
                $result = $helper->getIsEngineAvailableForNavigation(false);
            } else if ($moduleName == 'admin' || is_null($moduleName)) {
                // process indexation
                $result = $helper->isActiveEngine();
            }
        } else {
            $result = false;
        }

        $this->_useSolr = $result;
        return $result;
    }

}
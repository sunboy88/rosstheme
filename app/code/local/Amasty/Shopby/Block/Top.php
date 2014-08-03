<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Shopby
*/
class Amasty_Shopby_Block_Top extends Mage_Core_Block_Template
{
    private $options = array();
    
    private function trim($str)
    {
        $str = strip_tags($str);
        $str = str_replace('"', '', $str);
        return trim($str, " -");
    }
    
    public function getBlockId()
    {
        return 'amshopby-filters-wrapper';
    }

    /**
     * @param Amasty_Shopby_Model_Page|null $page
     */
    protected function _handleCanonical($page = null)
    {
        if (is_object($page) && $page->getUrl()) {
            $url = $page->getUrl();
        } else {
            /** @var Amasty_Shopby_Helper_Url $urlHelper */
            $urlHelper = Mage::helper('amshopby/url');
            $url = $urlHelper->getCanonicalUrl();
        }

        if ($url) {
            $this->_replaceCanonical($url);
        }
    }

    protected function _replaceCanonical($url)
    {
        /** @var Mage_Page_Block_Html_Head $head */
        $head = Mage::app()->getLayout()->getBlock('head');

        foreach ($head->getData('items') as $item) {
            if (strpos($item['params'], 'canonical') !== false) {
                $head->removeItem('link_rel', $item['name']);
            };
        }

        $head->addLinkRel('canonical', $url);
    }

    protected function _isPageHandled()
    {
        /** @var Amasty_Shopby_Helper_Page $pageHelper */
        $pageHelper = Mage::helper('amshopby/page');
        $page = $pageHelper->getCurrentMatchedPage();
        $this->_handleCanonical($page);
        if (is_null($page)) {
            return false;
        }

        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');

        // metas
        $title = $head->getTitle();
        // trim prefix if any
        $prefix = Mage::getStoreConfig('design/head/title_prefix');
        $prefix = htmlspecialchars(html_entity_decode(trim($prefix), ENT_QUOTES, 'UTF-8'));
        if ($prefix){
            $title = substr($title, strlen($prefix));
        }
        $suffix = Mage::getStoreConfig('design/head/title_suffix');
        $suffix = htmlspecialchars(html_entity_decode(trim($suffix), ENT_QUOTES, 'UTF-8'));
        if ($suffix){
            $title = substr($title, 0, -1-strlen($suffix));
        }
        $descr = $head->getDescription();
        $kw = $head->getKeywords();

        $titleSeparator = Mage::getStoreConfig('amshopby/general/title_separator');
        $descrSeparator = Mage::getStoreConfig('amshopby/general/descr_separator');
        $kwSeparator = ',';

        if ($page->getUseCat()){
            $title = $title . $titleSeparator . $page->getMetaTitle();
            $descr = $descr . $descrSeparator . $page->getMetaDescr();
            $kw = $page->getMetaKw() . $kwSeparator . $kw;
        }
        else {
            $title = $page->getMetaTitle();
            $descr = $page->getMetaDescr();
            $kw = $page->getMetaKw();
        }

        $head->setTitle($this->trim($title));
        $head->setDescription($this->trim($descr));
        $head->setKeywords($this->trim($kw));

        // in-page description
        $page->setShowOnList(true);
        $this->options = array($page);

        return true;

    }

    protected function _prepareLayout()
    {
        /** @var Amasty_Shopby_Block_Catalog_Product_List_Toolbar $toolbar */
        $toolbar = $this->getLayout()->getBlock('product_list_toolbar');
        if ($toolbar instanceof Amasty_Shopby_Block_Catalog_Product_List_Toolbar) {
            $toolbar->replacePager();
        }

        if ($this->_isPageHandled()){
        	$this->handleExtraAttributes();
			return parent::_prepareLayout();
        }

        $robotsIndex  = 'index';
        $robotsFollow = 'follow';
        
       
        $filters = Mage::getResourceModel('amshopby/filter_collection')
                ->addTitles()
                ->setOrder('position');
        $hash = array();
        
        foreach ($filters as $f){
            $code = $f->getAttributeCode();
            $vals = Mage::helper('amshopby')->getRequestValues($code);
            if ($vals){
                foreach($vals as $v){
                    $hash[$v] = $f->getShowOnList();
                }
                if ($f->getSeoNofollow()){
                    $robotsFollow = 'nofollow';
                }
                if ($f->getSeoNoindex()){
                    $robotsIndex = 'noindex';
                }
            }
        }

        $priceVals = Mage::app()->getRequest()->getParam('price');
        if ($priceVals) {
            if (Mage::helper('amshopby')->getSeoPriceNofollow()){
                $robotsFollow = 'nofollow';
            }
            if (Mage::helper('amshopby')->getSeoPriceNoindex()){
                $robotsIndex = 'noindex';
            }
        }
        
        /*
         * Check Category Settings
         */
        $catNoIndex = Mage::getStoreConfig('amshopby/seo/cat_noindex');
        if ($catNoIndex != '') {
            $categoriesIds = array_flip(explode(",", $catNoIndex));
            if (isset($categoriesIds[Mage::getSingleton('catalog/layer')->getCurrentCategory()->getId()])) {
                $robotsIndex = 'noindex';
            }
        }

        $catNoFollow = Mage::getStoreConfig('amshopby/seo/cat_nofollow');
        if ($catNoFollow != '') {
            $categoriesIds = array_flip(explode(",", $catNoFollow));
            if (isset($categoriesIds[Mage::getSingleton('catalog/layer')->getCurrentCategory()->getId()])) {
                $robotsFollow = 'nofollow';
            }
        }
        $this->handleExtraAttributes();        

        $head = $this->getLayout()->getBlock('head');
        if ($head){
            if ('noindex' == $robotsIndex || 'nofollow' == $robotsFollow){
                $head->setRobots($robotsIndex .', '. $robotsFollow);
            }
        }

        if (!$hash){
            return parent::_prepareLayout();
        }

        $options = Mage::getResourceModel('amshopby/value_collection')
            ->addFieldToFilter('option_id', array('in' => array_keys($hash)))
            ->load();

        $cnt = $options->count();
        if (!$cnt){
            return parent::_prepareLayout();
        }

        //some of the options value have wrong value;
        if ($cnt && $cnt < count($hash)){
            return parent::_prepareLayout();
            // or make 404 ?
        }

        // sort options by attribute ids and add "show_on_list" property
        foreach ($options as $opt){
            $id = $opt->getOptionId();
            
            $opt->setShowOnList($hash[$id]);
            $hash[$id] = clone $opt;
        }

        // unset "fake"  options (not object)
        foreach ($hash as $id => $opt){
            if (!is_object($opt)){
                unset($hash[$id]);
            }
        }
        if (!$hash){
            return parent::_prepareLayout();
        }

        if ($head){
            $title = $head->getTitle();
            // trim prefix if any
            $prefix = Mage::getStoreConfig('design/head/title_prefix');
            $prefix = htmlspecialchars(html_entity_decode(trim($prefix), ENT_QUOTES, 'UTF-8'));
            if ($prefix){
                $title = substr($title, strlen($prefix));
            }
            $suffix = Mage::getStoreConfig('design/head/title_suffix');
            $suffix = htmlspecialchars(html_entity_decode(trim($suffix), ENT_QUOTES, 'UTF-8'));
            if ($suffix){
                $title = substr($title, 0, -1-strlen($suffix));
            }

            $descr = $head->getDescription();
          
            $titleSeparator = Mage::getStoreConfig('amshopby/general/title_separator');
            $descrSeparator = Mage::getStoreConfig('amshopby/general/descr_separator');

            $kwSeparator = ',';
            $kw = '';

            $query = Mage::app()->getRequest()->getQuery();
            foreach ($hash as $opt){
            	if (isset($query[Mage::getStoreConfig('amshopby/brands/attr')])) {
        			if ($opt->getOptionId() == $query[Mage::getStoreConfig('amshopby/brands/attr')]) {
        				$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
						$breadcrumbs->addCrumb('amshopby-brand', array('label' => $opt->getTitle(), 'title' => $opt->getTitle()));
        			} 
		        }
            	
                if ($opt->getMetaTitle())
                    $title .= $titleSeparator . $opt->getMetaTitle();

                if ($opt->getMetaDescr())
                    $descr .= $descrSeparator . $opt->getMetaDescr();

                if ($opt->getMetaKw())
                    $kw .= $opt->getMetaKw() . $kwSeparator;
            }

            $kw = $kw . $head->getKeywords();

            $head->setTitle($this->trim($title));
            $head->setDescription($this->trim($descr));
            $head->setKeywords($this->trim($kw));
        }
        $this->options = $hash;

        return parent::_prepareLayout();
    }

    public function getOptions()
    {
        $res = array();
        foreach ($this->options as $opt){
            if (!$opt->getShowOnList()){
                continue;
            }

            $item = array();
            $item['title'] = $this->htmlEscape($opt->getTitle());
            $item['descr'] = $opt->getDescr();
            $item['cms_block'] = '';

            $blockName = $opt->getCmsBlock();
            if ($blockName) {
                $item['cms_block'] = $this->getLayout()
                    ->createBlock('cms/block')
                    ->setBlockId($blockName)
                    ->toHtml();
            }

            $item['image'] = '';
            if ($opt->getImgBig()){
                $item['image'] = Mage::getBaseUrl('media') . '/amshopby/' . $opt->getImgBig();
            }
            $res[] = $item;
        }
        return $res;
    }
    
/**
     * Handle price in urls.
     * If it noindex or nofollow tag is enabled - modify head tag
     */
    public function handleExtraAttributes()
    {
    	$head = $this->getLayout()->getBlock('head');

        if ($head){
        	
        	$index = 'index';
        	$follow = 'follow';
        	
        	/*
        	 * Set only if price is in request
        	 */
        	if (Mage::app()->getRequest()->getParam('price')) {
	        	$robotsIndex = Mage::getStoreConfig('amshopby/general/price_tag_noindex');
	        	$robotsFollow = Mage::getStoreConfig('amshopby/general/price_tag_nofollow');
	        	
	        	if ($robotsIndex) {
	        		$index = 'noindex';
	        	}
	        	
	            if ($robotsFollow) {
	        		$follow = 'nofollow';
	        	}
	        	
	            $head->setRobots($index .', '. $follow);
        	}
        }
    }

}
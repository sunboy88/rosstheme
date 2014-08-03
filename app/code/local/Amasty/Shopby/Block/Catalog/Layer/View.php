<?php
class Amasty_Shopby_Block_Catalog_Layer_View extends Amasty_Shopby_Block_Catalog_Layer_View_Adapter
{
    protected $_filterBlocks = null;
    protected $_blockPos     = 'left';

    protected $attributeOptionsData;
    
    public function getFilters()
    {
        if (!is_null($this->_filterBlocks)){
            return $this->_filterBlocks;
        }

        if ($this->_isCurrentUserAgentExcluded()) {
            return array();
        }

        $filters = parent::getFilters();

        $filters = $this->_excludeCurrentLandingFilters($filters);

        // append stock filter
        $f = $this->getChild('stock_filter');
    	if ($f->getPosition() > -1 && $this->_blockPos == Mage::getStoreConfig('amshopby/block/stock_filter_pos')) {
        	$filters[] = $f;
        }

        // remove some filters from the home page
        $exclude = Mage::getStoreConfig('amshopby/general/exclude');
        if ('/' == Mage::app()->getRequest()->getRequestString() && $exclude){
            $exclude = explode(',', preg_replace('/[^a-zA-Z0-9_\-,]+/','', $exclude));
            $filters = $this->excludeFilters($filters, $exclude);
        } else {
            $exclude = array();
        }
        
        $this->computeAttributeOptionsData($filters);

        // update filters with new properties
        $allSelected = array();
        foreach ($filters as $f){
            $strategy = $this->_getFilterStrategy($f);

            if (is_object($strategy)) {
                // initiate all filter-specific logic
                $strategy->prepare();
                $f->setIsExcluded($strategy->getIsExcluded());

                // remember selected options for dependent excluding
                if ($strategy instanceof Amasty_Shopby_Helper_Layer_View_Strategy_Attribute) {
                    $selectedValues = $strategy->getSelectedValues();
                    if ($selectedValues){
                        $allSelected = array_merge($allSelected, $selectedValues);
                    }
                }
            }
        }
        
        //exclude dependant, sinse 1.4.7
        foreach ($filters as $f){
            $ids = trim(str_replace(' ', '', $f->getDependOn()));
            $parentAttributes = trim(str_replace(' ', '', $f->getDependOnAttribute()));
            
            if (!$ids && !$parentAttributes){
                continue;
            }
            if (!empty($ids)) {
                $ids = explode(',', $ids);
                if (!array_intersect($allSelected, $ids)){
                    $exclude[] = $f->getAttributeModel()->getAttributeCode();
                    continue;
                }
            }

            if (!empty($parentAttributes)) {
                $attributePresent = false;
                $parentAttributes = explode(',', $parentAttributes);
                foreach ($parentAttributes as $parentAttribute) {
                    if (Mage::app()->getRequest()->getParam($parentAttribute)) {
                        $attributePresent = true;
                        break;
                    }
                }
                if (!$attributePresent) {
                    $exclude[] = $f->getAttributeModel()->getAttributeCode();
                }
            }
        }

        // 1.2.7 exclude some filters from the selected categories
        $filters = $this->excludeFilters($filters, $exclude);

        usort($filters, array($this, 'sortFiltersByOrder'));

        $this->_filterBlocks = $filters;
        return $filters;
    }

    protected function _getFilterStrategy(Mage_Catalog_Block_Layer_Filter_Abstract $filter)
    {
        $strategyCode = null;
        if ($filter instanceof Amasty_Shopby_Block_Catalog_Layer_Filter_Stock) {
            $strategyCode = 'stock';
        }
        else if ($filter instanceof Mage_Catalog_Block_Layer_Filter_Attribute || $filter instanceof Enterprise_Search_Block_Catalog_Layer_Filter_Attribute) {
            $strategyCode = 'attribute';
        }
        else if ($filter instanceof Mage_Catalog_Block_Layer_Filter_Category || $filter instanceof Enterprise_Search_Block_Catalog_Layer_Filter_Category) {
            $strategyCode = 'category';
        }
        else if ($filter instanceof Mage_Catalog_Block_Layer_Filter_Price || $filter instanceof Enterprise_Search_Block_Catalog_Layer_Filter_Price) {
            $strategyCode = 'price';
        }
        else if ($filter instanceof Mage_Catalog_Block_Layer_Filter_Decimal || $filter instanceof Enterprise_Search_Block_Catalog_Layer_Filter_Decimal) {
            $strategyCode = 'decimal';
        }

        /** @var Amasty_Shopby_Helper_Layer_View_Strategy_Abstract|null $strategy */
        if ($strategyCode) {
            $strategy = Mage::helper('amshopby/layer_view_strategy_' . $strategyCode);
            $strategy->setLayer($this);
            $strategy->setFilter($filter);
        } else {
            $strategy = null;
        }

        return $strategy;
    }

    protected function computeAttributeOptionsData($filters)
    {
        $ids = array();
        foreach ($filters as $f){
            if ($f->getItemsCount() && ($f instanceof Mage_Catalog_Block_Layer_Filter_Attribute || $f instanceof Enterprise_Search_Block_Catalog_Layer_Filter_Attribute)){
                $items = $f->getItems();
                foreach ($items as $item){
                    $ids[] = $item->getOptionId();
                }
            }
        }

        // images of filter values
        $optionsCollection = Mage::getResourceModel('amshopby/value_collection')
            ->addFieldToFilter('option_id', array('in' => $ids))
            ->load();

        $this->attributeOptionsData = array();
        foreach ($optionsCollection as $row){
            $this->attributeOptionsData[$row->getOptionId()] = array(
                'img' => $row->getImgSmall(),
                'img_hover' => $row->getImgSmallHover(),
                'descr' => $row->getDescr()
            );
        }
    }

    public function getAttributeOptionsData()
    {
        if (is_null($this->attributeOptionsData)) {
            throw new Exception('AttributeOptionsData not initialized');
        }

        return $this->attributeOptionsData;
    }

    protected function _excludeCurrentLandingFilters(array $filters)
    {
        /** @var Amasty_Xlanding_Model_Page $landingPage */
        $landingPage = Mage::registry('amlanding_current_page');
        if (is_null($landingPage)) {
            return $filters;
        };

        $attributes = $landingPage->getAttributesAsArray();
        $excludeCodes = array();
        foreach ($attributes as $attr) {
            $excludeCodes[] = $attr['code'];
        }

        $result = array();
        foreach ($filters as $f) {
            if ($f->getAttributeModel()){
                $code = $f->getAttributeModel()->getAttributeCode();
                if (in_array($code, $excludeCodes)) {
                    continue;
                }
            }

            if ($f instanceof Mage_Catalog_Block_Layer_Filter_Category) {
                if ($landingPage->getCategory()) {
                    continue;
                }
            }

            $result[] = $f;
        }

        return $result;
    }
    
    public function sortFiltersByOrder($filter1, $filter2) 
    {
        if ($filter1->getPosition() == $filter2->getPosition()) {
            if ($filter1 instanceof Mage_Catalog_Block_Layer_Filter_Category) {
                return -1;
            } else
                if ($filter2 instanceof Mage_Catalog_Block_Layer_Filter_Category) {
                return 1;
            }

            return 0;
        } 
        return $filter1->getPosition() > $filter2->getPosition() ? 1 : -1;
    }
    
    protected function _getFilterableAttributes()
    {
        $attributes = $this->getData('_filterable_attributes');
        if (is_null($attributes)) {
            $settings   = $this->_getDataHelper()->getAttributesSettings();
            $attributes = Mage::helper('amshopby/attributes')->getFilterableAttributes();
            foreach ($attributes as $k => $v){
                $pos = 'left';
                if (isset($settings[$v->getId()])){
                    $pos = $settings[$v->getId()]->getBlockPos();
                }
                elseif($v->getAttributeCode() == 'price'){
                    $pos = Mage::getStoreConfig('amshopby/block/price_pos');                    
                }
                if ($this->_notInBlock($pos)){
                    unset($attributes[$k]);
                }
            } 
            
            $this->setData('_filterable_attributes', $attributes);
        }

        return $attributes;
    }    
    
    public function getStateHtml()
    {
        $pos = Mage::getStoreConfig('amshopby/block/state_pos'); 
        if ($this->_notInBlock($pos)){
            return '';
        }
        $this->getChild('layer_state')->setTemplate('amasty/amshopby/state.phtml');
        return $this->getChildHtml('layer_state');
    } 
    
    public function canShowBlock()
    {
        if ($this->canShowOptions()){
            return true;
        }
        
        $cnt = 0;
        $pos = Mage::getStoreConfig('amshopby/block/state_pos'); 
        if (!$this->_notInBlock($pos)){
            $cnt = count($this->getLayer()->getState()->getFilters());
        }        
        return $cnt;
    }  
      
    public function getBlockId()
    {
        return 'amshopby-filters-' . $this->_blockPos;
    }       
    
    protected function excludeFilters($filters, $exclude)
    {
        $new = array();
        foreach ($filters as $f){
            $code = substr($f->getData('type'), 1+strrpos($f->getData('type'), '_'));
            if ($f->getAttributeModel()){
                $code = $f->getAttributeModel()->getAttributeCode();
            }
            
            if (in_array($code, $exclude) || $f->getIsExcluded()){
                 continue;
            } 
             
            $new[] = $f;          
        }
        return $new;
    }
    
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        
        $queldorei = false;
        if (!$html){
            // compatibility with "shopper" theme
            // @see catalog/layer/view.phtml
            $queldorei_blocks = Mage::registry('queldorei_blocks');
            if ($queldorei_blocks AND !empty($queldorei_blocks['block_layered_nav'])) {
                $html = $queldorei_blocks['block_layered_nav'];
            }
            if (!$html){
                return '';
            }
            $queldorei = true;
        }
        
        $pos = strrpos($html, '</div>');
        if ($pos !== false) {
            //add an overlay before closing tag
            $html = substr($html, 0, strrpos($html, '</div>')) 
                  . '<div style="display:none" class="amshopby-overlay"></div>'
                  . '</div>';
        }

        
        // to make js and css work for 1.3 also
        $html = str_replace('class="narrow-by', 'class="block-layered-nav narrow-by', $html);
        // add selector for ajax
        $html = str_replace('block-layered-nav', 'block-layered-nav ' . $this->getBlockId(), $html);

        if (Mage::getStoreConfig('amshopby/general/enable_collapsing')) {
            $html = str_replace('block-layered-nav', 'block-layered-nav amshopby-collapse-enabled', $html);
        }
        
        // we don't want to move this into the template are different in custom themes
        foreach ($this->getFilters() as $f){
            $name = $this->__($f->getName());
            if ($f->getCollapsed() && !$f->getHasSelection()){
                $html = str_replace('<dt>'.$name, '<dt class="amshopby-collapsed">'.$name, $html);
            }
            $comment = $f->getComment();
            if ($comment){
                $img = Mage::getDesign()->getSkinUrl('images/amshopby-tooltip.png');
                $img = '<img class="amshopby-tooltip-img" src="'.$img.'" width="9" height="9" alt="'.htmlspecialchars($comment).'" id="amshopby-img-'.$f->getAttributeCode().'"/>';
                $html = str_replace($name.'</dt>', $name . $img . '</dt>', $html);    
            }
            
        }
        
        if ($queldorei AND !empty($queldorei_blocks['block_layered_nav'])) {
            // compatibility with "shopper" theme
            // @see catalog/layer/view.phtml
            Mage::unregister('queldorei_blocks');
            $queldorei_blocks['block_layered_nav'] = $html;
            Mage::register('queldorei_blocks', $queldorei_blocks);
            return '';
        }
        
        return $html;
    }    

    protected function _prepareLayout()
    {
        $pos = Mage::getStoreConfig('amshopby/block/categories_pos');
        if ($this->_notInBlock($pos)){
            $this->_categoryBlockName = 'amshopby/catalog_layer_filter_empty';   
        }        
        if (Mage::getStoreConfig('amshopby/block/stock_filter') >= 0) {
        	$stockBlock = $this->getLayout()->createBlock('amshopby/catalog_layer_filter_stock')
            	->setLayer($this->getLayer())
            	->init();
            	
            $this->setChild('stock_filter', $stockBlock);
        }

        if (Mage::registry('amshopby_layout_prepared')){
            return parent::_prepareLayout();
        }
        else {
            Mage::register('amshopby_layout_prepared', true);
        }
        
        if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) { 
            $url = Mage::helper('amshopby/url')->getFullUrl($_GET);
            Mage::getSingleton('customer/session')
                ->setBeforeAuthUrl($url);           
        }
        
        $head = $this->getLayout()->getBlock('head');
        if ($head){
            $head->addJs('amasty/amshopby/amshopby.js');     
             
            if (Mage::getStoreConfigFlag('amshopby/block/ajax')){
                $request = Mage::app()->getRequest();
                
                $isProductPage = $request->getControllerName() == "product" &&
                    $request->getActionName() == "view";
                
                if (!$isProductPage)
                $head->addJs('amasty/amshopby/amshopby-ajax.js');                 
            }
        }
        
        return parent::_prepareLayout();
    } 
    
    protected function _notInBlock($pos)
    {
        if (!in_array($pos, array('left', 'right', 'top','both'))){
            $pos = 'left';
        }
        return (!in_array($pos, array($this->_blockPos, Amasty_Shopby_Model_Source_Position::BOTH)));
    }
      
    protected function _isCurrentUserAgentExcluded()
    {
        /** @var Mage_Core_Helper_Http $helper */
        $helper = Mage::helper('core/http');
        $currentAgent = $helper->getHttpUserAgent();

        $excludeAgents = explode(',', Mage::getStoreConfig('amshopby/seo/exclude_user_agent'));
        foreach ($excludeAgents as $agent) {
            if (stripos($currentAgent, trim($agent)) !== false) {
                return true;
            }
        }

        return false;
    }

	public function getClearUrl()
    {
        /** @var Amasty_Shopby_Helper_Url $helper */
        $helper = Mage::helper('amshopby/url');
        $query = array();
        if ($helper->isOnBrandPage()) {
            $brandAttr = Mage::getStoreConfig('amshopby/brands/attr');
            $brandId = $this->getRequest()->getParam($brandAttr);
            if ($brandId) {
                $query[$brandAttr] = (int) $brandId;
            }
        }
		return $helper->getFullUrl($query, true);
	}

    protected function _getDataHelper()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        return $helper;
    }

}
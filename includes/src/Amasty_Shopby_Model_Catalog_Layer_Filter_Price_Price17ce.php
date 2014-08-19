<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */

class Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Price17ce extends Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Price17ce_Parent
{
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        if (!$this->calculateRanges()){
             $this->_items = array($this->_createItem('', 0, 0)); 
        }        
        
        $filterBlock->setValueFrom(Mage::helper('amshopby')->__('From'));
        $filterBlock->setValueTo(Mage::helper('amshopby')->__('To'));
        
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        //validate filter
        $filterParams = explode(',', $filter);
        $filter = $this->_validateFilter($filterParams[0]);
        if (!$filter) {
            return $this;
        }

        list($from, $to) = $filter;
        
        $filterBlock->setValueFrom($from > 0.01 ? $from : '');
        $filterBlock->setValueTo($to > 0.01 ? $to : ''); 

        /*
         * Workaround for defect related to decreasing price for layered navgiation
         * 
         * Check for not empty for prices like "4000-" 
         */
        if (!empty($to)) {
            $to = $to + Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE;            
        }
        
        /*
         * Workaround for JS
         */
        if ($to == 0) {
            $to = '';
        }
        
        $this->setInterval(array($from, $to));

        $priorFilters = array();
        for ($i = 1; $i < count($filterParams); ++$i) {
            $priorFilter = $this->_validateFilter($filterParams[$i]);
            if ($priorFilter) {
                $priorFilters[] = $priorFilter;
            } else {
                //not valid data
                $priorFilters = array();
                break;
            }
        }
        if ($priorFilters) {
            $this->setPriorIntervals($priorFilters);
        }
        $this->_applyPriceRange();
        $this->getLayer()->getState()->addFilter($this->_createItem(
            $this->_renderRangeLabel(empty($from) ? 0 : $from, $to),
            $filter
        ));
        
        if ($this->hideAfterSelection()){
             $this->_items = array();
        } 
        elseif ($this->calculateRanges()){
            $this->_items = array($this->_createItem('', 0, 0));
        }

        return $this;
    }
    
    /**
     * Retrieve resource instance
     *
     * @return Amasty_Shopby_Model_Mysql4_Price17
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getSingleton('amshopby/mysql4_price17');
        }
        return $this->_resource;
    }
    
    public function getMaxValue()
    {
        return $this->_getResource()->getMaxPrice($this);
    }
    
     public function getMinValue()
    {
        return $this->_getResource()->getMinPrice($this);
    }

    protected function _getItemsData()
    {
        if (!Mage::getStoreConfig('amshopby/general/use_custom_ranges')) {
            $this->setInterval(array());
            return parent::_getItemsData();
        }
            
        $key = $this->_getCacheKey();

        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if ($data === null) {
            $ranges = $this->_getCustomRanges();
            $counts = $this->_getResource()->getFromToCount($this, $ranges);
            $data = array();
            
            foreach ($counts as $index => $count) {
                if (!$index) // index may be NULL if some products has price out of all ranges
                    continue;
                    
                $from  = $ranges[$index][0];
                $to    = $ranges[$index][1];
                
                $to2 = $to;
                if ($to > 999998) {
                	$to2 = '';
                }
                $data[] = array(
                    'label' => $this->_renderRangeLabel($from, $to2),
                    'value' => $from . '-' . $to,
                    'count' => $count,
                    'pos'   => $from,
                );
            }
            usort($data, array($this, '_srt')); 

            $tags = array(
                Mage_Catalog_Model_Product_Type_Price::CACHE_TAG,
            );
            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }
}
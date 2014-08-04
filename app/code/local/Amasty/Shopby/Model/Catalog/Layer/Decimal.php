<?php

class Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal extends Mage_Catalog_Model_Layer_Filter_Decimal
{
    private $_rangeSeparator = ',';
    private $_fromToSeparator = '-';
    
    protected $settings = null;

    /**
     * Initialize filter and define request variable
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getSettings()
    {
        if (is_null($this->settings)){
            $this->settings = Mage::getResourceModel('amshopby/filter')
              ->getFilterByAttributeId($this->getAttributeModel()->getAttributeId()); 
        }
        return $this->settings;
    }
    
    /**
     * Retrieve resource instance
     *
     * @return Amasty_Shopby_Model_Mysql4_Decimal
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getModel('amshopby/mysql4_decimal');
        }
        return $this->_resource;
    }

    /**
     * Apply decimal range filter to product collection
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
     * @return Mage_Catalog_Model_Layer_Filter_Decimal
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        
        if (!$this->calculateRanges()){
            $this->_items = array($this->_createItem('', 0, 0));
        }         
        
        $filterBlock->setValueFrom(Mage::helper('amshopby')->__('From'));
        $filterBlock->setValueTo(Mage::helper('amshopby')->__('To'));
       
        /**
         * Filter must be string: $index, $range
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }
        
        $isFromTo = false;
        $range  = array();
        
        /*
         * Try range
         */
        $range = explode($this->_rangeSeparator, $filter);
        if (count($range) != 2) {
             /*
              * Try from to
              */
             $range = explode($this->_fromToSeparator, $filter);             
             if (count($range) == 2) {
                 $isFromTo = true;
             } else {
                 return $this;
             }
        } 

        list ($from, $to) = $range;
        $from  = floatval($from);
        $to    = floatval($to);

        if ($from || $to) {
            if (!$isFromTo) {
                $range = $from;
                $index = $to;
                $from  = ($index-1) * $range;
                $to    = $index * $range;
            }   
            
            $this->_getResource()->applyFilterToCollection($this, $from, $to);
            
            $filterBlock->setValueFrom($from);
            
            if ($to > 0) {
                $filterBlock->setValueTo($to);
            } else {
                $filterBlock->setValueTo('');    
            }
            
            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_renderItemLabel($from, $to, true), $filter)
            );
            
            if ($this->hideAfterSelection()){
                 $this->_items = array();
            } 
            elseif ($this->calculateRanges()){
                $this->_items = array($this->_createItem('', 0, 0));
            }      
        }
        
        return $this;
        
    }

    /**
     * Prepare text of item label
     *
     * @param   int $range
     * @param   float $value
     * @param   bool $flatValues set to true, if range and value are flat from - to values, not multiplier and start. 
     * @return  string
     */
    protected function _renderItemLabel($range, $value, $flatValues = false)
    {
        if ($flatValues) {
            $from = $range;
            $to = $value;
        } else {
               $from   = ($value - 1) * $range;
              $to     = $value * $range;
        } 
            
        $settings = $this->getSettings();
        if (!empty($settings['value_label'])) {
            return Mage::helper('catalog')->__('%s - %s %s', $from, $to, $settings['value_label']);    
        } else {
            return Mage::helper('catalog')->__('%s - %s', $from, $to);
        }
    }
    
    public function getRange()
    {
        $settings = $this->getSettings();
        if (!empty($settings['range'])){
            return $settings['range'];
        }
            
        return parent::getRange(); 
    }
    
    public function calculateRanges()
    {
        $settings = $this->getSettings();
        return ($settings['display_type'] == 0 || $settings['display_type'] == 1);     
    } 
    
    public function hideAfterSelection()
    {
        $settings = $this->getSettings();
        if ($settings['from_to_widget']){
            return false;
        }
        if ($settings['display_type'] == 3){
            return false;
        }
        return true;
    }
     
}
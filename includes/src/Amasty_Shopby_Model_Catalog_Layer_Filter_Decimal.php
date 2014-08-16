<?php

class Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal extends Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal_Adapter
{
    private $_rangeSeparator = ',';
    private $_fromToSeparator = '-';

    protected $settings = null;

    public function getItemsCount()
    {
        $min = $this->getMinValue();
        $max = $this->getMaxValue();

        $noneVariant = is_null($min) || is_null($max);
        $oneHiddenVariant = ($min == $max) && Mage::getStoreConfig('amshopby/general/hide_one_value');

        if ($noneVariant || $oneHiddenVariant) {
            $count = 0;
        } else {
            $count = parent::getItemsCount();
        }

        return $count;
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
        $attributeCode = $this->getAttributeModel()->getAttributeCode();
        /** @var Amasty_Shopby_Helper_Attributes $attributeHelper */
        $attributeHelper = Mage::helper('amshopby/attributes');
        if (!$attributeHelper->lockApplyFilter($attributeCode, 'attr')) {
            return $this;
        }

        if (!$this->calculateRanges()){
            $this->_items = array($this->_createItem('', 0, 0));
        }         

        $filterBlock->setValueFrom(Mage::helper('amshopby')->__('From'));
        $filterBlock->setValueTo(Mage::helper('amshopby')->__('To'));

        $input = $request->getParam($this->getRequestVar());
        $fromTo = $this->_parseRequestedValue($input);
        if (is_null($fromTo)) {
            return $this;
        }

        list($from, $to) = $fromTo;
        $this->_getResource()->applyFilterToCollection($this, $from, $to);

        $filterBlock->setValueFrom($from);

        if ($to > 0) {
            $filterBlock->setValueTo($to);
        } else {
            $filterBlock->setValueTo('');
        }

        $this->getLayer()->getState()->addFilter(
            $this->_createItem($this->_renderItemLabel($from, $to, true), $input)
        );

        if ($this->hideAfterSelection()){
            $this->_items = array();
        }
        elseif ($this->calculateRanges()){
            $this->_items = array($this->_createItem('', 0, 0));
        }

        return $this;
        
    }

    protected function _parseRequestedValue($input)
    {
        if (!$input) {
            return null;
        }

        /* Try $index, $range */
        $inputVals = explode($this->_rangeSeparator, $input);
        if (count($inputVals) == 2) {
            list($index, $range) = $inputVals;
            $from  = ($index-1) * $range;
            $to    = $index * $range;
            return array($from, $to);
        }

        /* Try from to */
        $inputVals = explode($this->_fromToSeparator, $input);
        if (count($inputVals) == 2) {
            list ($from, $to) = $inputVals;
            $from  = floatval($from);
            $to    = floatval($to);
            return array($from, $to);
        }

        return null;
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

    public function addFacetCondition()
    {
        if ($this->calculateRanges()) {
            parent::addFacetCondition();
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
        return $settings['display_type'] == Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_DEFAULT
        || $settings['display_type'] == Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_DROPDOWN;
    } 
    
    public function hideAfterSelection()
    {
        $settings = $this->getSettings();
        if ($settings['from_to_widget']){
            return false;
        }
        if ($settings['display_type'] == Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_SLIDER){
            return false;
        }
        return true;
    }

}
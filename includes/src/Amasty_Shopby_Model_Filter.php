<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */

/**
 * @method int getDisplayType()
 * @method int getSingleChoice()
 * @method string getIncludeIn()
 * @method string getExcludeFrom()
 */
class Amasty_Shopby_Model_Filter extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/filter');
    }

    public function getDisplayTypeString()
    {
        $hash = $this->getDisplayTypeOptionsSource()->getHash();
        return $hash[$this->getDisplayType()];
    }

    public function getDisplayTypeOptionsSource()
    {
        $sourceName = ($this->getBackendType() == 'decimal') ? 'price' : 'attribute';
        $modelName = 'amshopby/source_' . $sourceName;
        $source = Mage::getModel($modelName);
        return $source;
    }

    public function getIncludeInArray()
    {
        $cats = trim(str_replace(' ', '', $this->getIncludeIn()));
        return ($cats == '') ? null : explode(',', $cats);
    }

    public function getExcludeFromArray()
    {
        $cats = trim(str_replace(' ', '', $this->getExcludeFrom()));
        return ($cats == '') ? array() : explode(',', $cats);
    }
}

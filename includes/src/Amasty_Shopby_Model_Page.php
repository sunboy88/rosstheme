<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Shopby
*/

/**
 * @method Amasty_Shopby_Model_Page setCond($serializedCond)
 * @method string getCond()
 * @method Amasty_Shopby_Model_Page setCats($cats)
 * @method string getCats()
 * @method Amasty_Shopby_Model_Page setUrl(string $url)
 * @method string getUrl()
 */
class Amasty_Shopby_Model_Page extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('amshopby/page');
    }

    public function getAllFilters($addEmpty=false)
    {
        $collection = Mage::getModel('amshopby/filter')->getResourceCollection()
            ->addTitles();
            
        $values = array();
        if ($addEmpty){
            $values[''] = '';
        }
        foreach ($collection as $row){
            $values[$row->getAttributeCode()] = $row->getFrontendLabel();
        } 
        return $values;
    }

    public function matchCategory(Mage_Catalog_Model_Category $category)
    {
        $cats = $this->getCats();
        return $cats ?
            in_array($category->getId(), explode(',', $cats)) :
            true;
    }

    public function matchFilters()
    {
        $strict = Mage::getStoreConfig('amshopby/seo/page_match_strict');

        /** @var Amasty_Shopby_Helper_Attributes $attributesHelper */
        $attributesHelper = Mage::helper('amshopby/attributes');
        $requestedExtraFilters = $strict ? $attributesHelper->getRequestedFilterCodes() : null;

        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        $conditions = $this->_getConditions();

        foreach ($conditions as $code => $expected) {
            $actual = $helper->getRequestValues($code);

            if ($strict) {
                unset($requestedExtraFilters[$code]);

                if (array_diff($actual, $expected)) {
                    return false;
                }
            }

            if (array_diff($expected, $actual)) {
                return false;
            }
        }

        if ($strict && $requestedExtraFilters) {
            return false;
        }

        return true;
    }

    protected function _getConditions()
    {
        $conditions = unserialize($this->getCond());
        if (!is_array($conditions)) {
            return array();
        }

        $result = array();

        foreach ($conditions as $k => $v) {
            if (!$v){ // multiselect can be empty
                continue;
            }

            if (is_array($v) && is_numeric($k)) {
                /* Multiple attributes fix */
                $code = $v['attribute_code'];
                $value = $v['attribute_value'];
            } else {
                $code = $k;
                $value = $v;
            }

            if (!is_array($value)) {
                $value = array($value);
            }
            $result[$code] = $value;
        }

        return $result;
    }

    public function getFrontendInput($attributeCode)
    {
        $attributes = Mage::getModel('amshopby/filter')->getResourceCollection()->addFrontendInput($attributeCode);
        return $attributes->getFirstItem();
    }
        
    public function getOptionsForFilter($attributeCode, $frontendInput)
    {
        $filters = Mage::getModel('amshopby/filter')->getResourceCollection()->addFrontendInput($attributeCode);
        $filterId = $filters->getFirstItem()->getFilterId();
        
        $options = Mage::getModel('amshopby/value')->getResourceCollection()->addFilter('filter_id', $filterId);
            
        $values = array();
        foreach ($options as $option) {
            if ('select' == $frontendInput) {
                $values[$option->getOptionId()] = $option->getTitle();
            } elseif ('multiselect' == $frontendInput) {
                $values[] = array(
                    'value' => $option->getOptionId(),
                    'label' => $option->getTitle(),
                );
            }
        } 
        return $values;
    }
}
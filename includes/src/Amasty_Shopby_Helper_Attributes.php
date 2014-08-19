<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */
class Amasty_Shopby_Helper_Attributes extends Amasty_Shopby_Helper_Cached
{
    protected $_options;
    protected $_requestedFilterCodes;

    public $_appliedFilterCodes = array();

    /**
     * @return array
     */
    public function getAllFilterableOptionsAsHash()
    {
        $cacheId = 'filterable_options_hash';

        $result = $this->load($cacheId);
        if ($result) {
            return $result;
        }

        $xAttributeValuesUnique = array();
        $hash = array();
        $attributes = $this->getFilterableAttributes();

        /** @var Amasty_Shopby_Helper_Url $helper */
        $helper = Mage::helper('amshopby/url');

        $options = $this->getAllOptions();

        foreach ($attributes as $a){
            $code        = $a->getAttributeCode();
            $code = str_replace(array('_', '-'), Mage::getStoreConfig('amshopby/seo/special_char'), $code);
            $hash[$code] = array();
            foreach ($options as $o){
                if ($o['value'] && $o['attribute_id'] == $a->getId()) { // skip first empty
                    $nonUniqueValue = $o['url_alias'] ? $o['url_alias'] : $o['value'];
                    $unKey = $helper->createKey($nonUniqueValue);

                    while (isset($hash[$code][$unKey])
                        || (Mage::getStoreConfig('amshopby/seo/hide_attributes') && isset($xAttributeValuesUnique[$unKey]))
                    ) {
                        $unKey .= Mage::getStoreConfig('amshopby/seo/special_char');
                    }
                    $hash[$code][$unKey] = $o['option_id'];
                    $xAttributeValuesUnique[$unKey] = true;
                }
            }
        }
        $xAttributeValuesUnique = null;

        $this->save($hash, $cacheId);
        return $hash;
    }

    public function getFilterableAttributes()
    {
        $cacheId = 'filterable_attributes';

        $result = $this->load($cacheId);
        if ($result) {
            return $result;
        }

        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        $collection
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');

        if (Mage::app()->getRequest()->getModuleName() == 'catalogsearch') {
            $collection->addIsFilterableInSearchFilter();
        } else {
            $collection->addIsFilterableFilter();
        }

        $collection->load();

        $result = array();
        foreach ($collection as $attribute) {
            /** @var Mage_Eav_Model_Attribute $attribute */
            $result[$attribute->getAttributeId()]  = $attribute;
        }

        $this->save($result, $cacheId);
        return $result;
    }

    public function getDecimalAttributeCodeMap()
    {
        $cacheId = 'decimal_attribute_code_map';

        $result = $this->load($cacheId);
        if ($result) {
            return $result;
        }

        $map = array();
        $attributes = $this->getFilterableAttributes();
        foreach ($attributes as $attribute) {
            /** @var Mage_Eav_Model_Attribute $attribute */
            $map[$attribute->getAttributeCode()] = $attribute->getBackendType() == 'decimal';
        }

        $this->save($map, $cacheId);
        return $map;
    }

    /**
     * Get option for specific attribute
     * @param string $attributeCode
     * @return array
     */
    public function getAttributeOptions($attributeCode)
    {
        $cacheId = 'attribute_options_by_attribute_code';

        $hash = $this->load($cacheId);
        if (!$hash) {
            $hash = array();
            $attributes = $this->getFilterableAttributes();
            $options = $this->getAllOptions();
            foreach ($attributes as $attribute)
            {
                $code = $attribute->getAttributeCode();
                $hash[$code] = array();

                foreach ($options as $option) {
                    if ($option['attribute_id'] == $attribute->getAttributeId()) {
                        $hash[$code][] = array(
                            'value' => $option['option_id'],
                            'label' => $option['value'],
                        );
                    }
                }
            }
            $this->save($hash, $cacheId);
        }

        return isset($hash[$attributeCode]) ? $hash[$attributeCode] : array();
    }

    protected function getAllOptions()
    {
        /** @var Amasty_Shopby_Model_Mysql4_Value_Collection $settingCollection */
        $settingCollection = Mage::getResourceModel('amshopby/value_collection');
        $settingCollection->load();

        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setStoreFilter();

        $valuesCollection->getSelect()->order('sort_order', 'ASC');

        $v = $valuesCollection->toArray();
        $options = $v['items'];
        foreach ($options as &$option) {
            /** @var Amasty_Shopby_Model_Value $setting */
            $setting = $settingCollection->getItemByColumnValue('option_id', $option['option_id']);
            $option['url_alias'] = $setting ? $setting->getUrlAlias() : '';
        }

        return $options;
    }


    public function getRequestedFilterCodes()
    {
        if (!isset($this->_requestedFilterCodes)) {
            $this->_requestedFilterCodes = array();
            $requestedParams = Mage::app()->getRequest()->getParams();

            $attributes = $this->getFilterableAttributes();

            foreach ($attributes as $attribute) {
                /** @var Mage_Eav_Model_Attribute $attribute*/

                $code = $attribute->getData('attribute_code');
                if (array_key_exists($code, $requestedParams)) {
                    $this->_requestedFilterCodes[$code] = $requestedParams[$code];
                }
            }
        }
        return $this->_requestedFilterCodes;
    }

    public function lockApplyFilter($code, $type)
    {
        $hash = $type . '*' . $code;
        if (in_array($hash, $this->_appliedFilterCodes)) {
            return false;
        } else {
            $this->_appliedFilterCodes[] = $hash;
            return true;
        }
    }
}
<?php
abstract class Amasty_Shopby_Helper_Layer_View_Strategy_Modeled extends Amasty_Shopby_Helper_Layer_View_Strategy_Abstract
{
    /** @var  Mage_Catalog_Model_Resource_Eav_Attribute */
    protected $attribute;

    /** @var  Amasty_Shopby_Model_Filter */
    protected $model;

    public function setFilter(Mage_Catalog_Block_Layer_Filter_Abstract $filter)
    {
        parent::setFilter($filter);

        $this->attribute = $filter->getAttributeModel();
        $this->model = $this->getFilterModel();
    }

    public function prepare()
    {
        parent::prepare();

        $this->transferModelData();
    }

    protected function setCollapsed()
    {
        return $this->isCollapseEnabled() && $this->model && $this->model->getCollapsed();
    }

    protected function setPosition()
    {
        return $this->attribute->getPosition();
    }

    protected function transferModelData()
    {
        if (!$this->model) {
            return;
        }

        $fields = $this->getTransferableFields();
        foreach ($fields as $field) {
            $this->filter->setData($field, $this->model->getData($field));
        }
    }

    protected function getTransferableFields()
    {
        return array();
    }

    protected function getFilterModel()
    {
        $settings = $this->_getDataHelper()->getAttributesSettings();
        $attributeId = $this->attribute->getId();
        /** @var Amasty_Shopby_Model_Filter $model */
        $model = isset($settings[$attributeId]) ? $settings[$attributeId] : null;
        return $model;
    }

    public function getIsExcluded()
    {
        if (!$this->model) {
            return true;
        }

        $categoryId = $this->getCurrentCategoryId();

        $exclude = false;

        $includeCategories = $this->model->getIncludeInArray();
        if ($includeCategories) {
            if (!in_array($categoryId, $includeCategories)) {
                $exclude = true;
            }
        }

        if (!$exclude) {
            $excludeCategories = $this->model->getExcludeFromArray();
            if (in_array($categoryId, $excludeCategories)) {
                $exclude = true;
            }
        }
        return $exclude;
    }
}

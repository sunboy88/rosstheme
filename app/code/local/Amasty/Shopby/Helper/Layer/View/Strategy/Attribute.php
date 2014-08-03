<?php
class Amasty_Shopby_Helper_Layer_View_Strategy_Attribute extends Amasty_Shopby_Helper_Layer_View_Strategy_Modeled
{
    public function prepare()
    {
        parent::prepare();

        $this->prepareItems();
    }

    protected function setTemplate()
    {
        return 'amasty/amshopby/attribute.phtml';
    }

    protected function setHasSelection()
    {
        $selected = $this->getSelectedValues();
        return !empty($selected);
    }

    protected function prepareItems()
    {
        $items = $this->filter->getItems();

        $options = $this->layer->getAttributeOptionsData();

        foreach ($items as $item){
            /** @var Amasty_Shopby_Model_Catalog_Layer_Filter_Item $item */
            $optId = $item->getOptionId();
            if (!empty($options[$optId]['img'])){
                $item->setImage($options[$optId]['img']);
            }
            if (!empty($options[$optId]['img_hover'])){
                $item->setImageHover($options[$optId]['img_hover']);
            }
            if (!empty($options[$optId]['descr'])){
                $item->setDescr($options[$optId]['descr']);
            }

            $item->setIsSelected(in_array($optId, $this->getSelectedValues()));
        }
    }

    public function getSelectedValues()
    {
        $selectedValues = $this->_getDataHelper()->getRequestValues($this->attribute->getAttributeCode());
        return $selectedValues;
    }

    protected function getTransferableFields()
    {
        return array('max_options', 'hide_counts', 'sort_by', 'display_type', 'single_choice', 'seo_rel', 'depend_on', 'depend_on_attribute', 'comment', 'show_search');
    }

    public function getIsExcluded()
    {
        if (parent::getIsExcluded()) {
            return true;
        }
        Mage::app()->getRequest()->getParams();
        if (defined('AMSHOPBY_FEATURE_HIDE_SINGLE_CHOICE_FILTERS') && AMSHOPBY_FEATURE_HIDE_SINGLE_CHOICE_FILTERS) {
            if ($this->model->getSingleChoice()) {
                if (Mage::app()->getRequest()->getParam($this->attribute->getAttributeCode())) {
                    return true;
                }
            }
        }

        return false;
    }
}

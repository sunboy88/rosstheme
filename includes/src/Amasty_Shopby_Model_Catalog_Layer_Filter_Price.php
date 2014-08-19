<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Catalog_Layer_Filter_Price extends Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Adapter
{
    /**
     * Display Types
     */
    const DT_DEFAULT    = 0;
    const DT_DROPDOWN   = 1;
    const DT_FROMTO     = 2;
    const DT_SLIDER     = 3;

    public function _srt($a, $b)
    {
        $res = ($a['pos'] < $b['pos']) ? -1 : 1;
        return $res;
    }

    protected function _getCustomRanges()
    {
        $ranges = array();
        $collection = Mage::getModel('amshopby/range')->getCollection()
            ->setOrder('price_frm','asc')
            ->load();
            
        $rate = Mage::app()->getStore()->getCurrentCurrencyRate(); 
        foreach ($collection as $range){
            $from = $range->getPriceFrm()*$rate;
            $to = $range->getPriceTo()*$rate;
            
            $ranges[$range->getId()] = array($from, $to);
        }
        
        if (!$ranges){
            echo "Please set up Custom Ranges in the Admin > Catalog > Improved Navigation > Ranges";
            exit;
        }
        
        return $ranges;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        /** @var Amasty_Shopby_Helper_Attributes $attributeHelper */
        $attributeHelper = Mage::helper('amshopby/attributes');
        if (!$attributeHelper->lockApplyFilter('', 'price')) {
            return $this;
        }

        parent::apply($request, $filterBlock);
    }
    
    public function calculateRanges()
    {
        return (Mage::getStoreConfig('amshopby/general/price_type') == self::DT_DEFAULT 
            || Mage::getStoreConfig('amshopby/general/price_type') == self::DT_DROPDOWN);     
    } 
    
    public function hideAfterSelection()
    {
        if (Mage::getStoreConfig('amshopby/general/price_from_to')){
            return false;
        }
        if (Mage::getStoreConfig('amshopby/general/price_type') == self::DT_SLIDER){
            return false;
        }
        return true;
    }

    public function getItemsCount()
    {
        $cnt = parent::getItemsCount();
        $checkForOne = $this->calculateRanges() && Mage::getStoreConfig('amshopby/general/hide_one_value');

        return ($cnt == 1 && $checkForOne) ? 0 : $cnt;
    }
}
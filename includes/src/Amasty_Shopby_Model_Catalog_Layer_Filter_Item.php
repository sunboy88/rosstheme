<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */ 
class Amasty_Shopby_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        $query = array(
            $this->getFilter()->getRequestVar()=>$this->getValue(),
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );
        $url = Mage::helper('amshopby/url')->getFullUrl($query);
        return $url;
    }
    
    
    public function getRemoveUrl()
    {
        $query = array(
            $this->getFilter()->getRequestVar() => $this->getFilter()->getResetValue(),
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );
        
        $url = Mage::helper('amshopby/url')->getFullUrl($query);
        return $url;        
    } 

}
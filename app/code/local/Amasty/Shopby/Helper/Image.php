<?php
/**
* @copyright Amasty.
*/ 
class Amasty_Shopby_Helper_Image extends Mage_Catalog_Helper_Image
{
    public function setProduct($product)
    {
        $configurableCodes = Mage::getStoreConfig('amshopby/general/configurable_images');
        if (!empty($configurableCodes) && $product->isConfigurable() && $product->isSaleable()) {
            
            $configurableCodes = explode(",", trim($configurableCodes));
            
            $productTypeIns = $product->getTypeInstance(true);
            $childIds = $productTypeIns->getChildrenIds($product->getId());
            
            $requestParams = Mage::app()->getRequest()->getParams();
            
            foreach ($childIds[0] as $childId) {
                
                $hasInRequest = 0;
                $hasMatch = 0;
                
                $childProduct = Mage::getModel('catalog/product')->setStoreId($product->getStoreId())->load($childId);
                foreach ($configurableCodes as $filterCode) {
                    if (in_array($filterCode, array_keys($requestParams))) {
                        $hasInRequest++;
                        $value = $requestParams[$filterCode];                        
                        if (strpos($value, ",") !== false) {
                            $value = explode(",", $value);
                        } else {
                            $value = array($value);
                        }
                        if (in_array($childProduct->getData($filterCode), array_values($value))) {
                            $hasMatch++;
                        }
                    }
                }
                
                if ($hasInRequest != 0 && $hasInRequest == $hasMatch) {
                    $product = $childProduct;
                }
            }
        }
        parent::setProduct($product);
    }
}

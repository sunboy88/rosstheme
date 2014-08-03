<?php
/**
 * @copyright Amasty.
 */
class Amasty_Shopby_Helper_Page extends Mage_Core_Helper_Abstract
{
    /**
     * @return Amasty_Shopby_Model_Page
     */
    public function getCurrentMatchedPage()
    {
        $result = null;

        /** @var Amasty_Shopby_Model_Mysql4_Page_Collection $collection */
        $collection = Mage::getModel('amshopby/page')->getCollection();
        $collection->addStoreFilter();

        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::registry('current_category');

        foreach ($collection as $page){
            /** @var Amasty_Shopby_Model_Page $page */

            $match = $page->matchCategory($category) && $page->matchFilters();
            if ($match) {
                $result = $page;
                break;
            }
        }

        return $result;
    }
}
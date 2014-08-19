<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
if (Mage::helper('amshopby')->useSolr()) {
    class Amasty_Shopby_Block_Catalog_Layer_View_Adapter extends Enterprise_Search_Block_Catalog_Layer_View {}
} else {
    class Amasty_Shopby_Block_Catalog_Layer_View_Adapter extends Mage_Catalog_Block_Layer_View {}
}
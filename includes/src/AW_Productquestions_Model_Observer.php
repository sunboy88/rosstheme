<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productquestions
 * @version    1.5.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Productquestions_Model_Observer {

    /**
     * @param $observer
     * @return $count, needed for tests
     */
    public function updateProductQuestionsProductsNames($observer) {
        $product = $observer->getEvent()->getProduct();

        if (!$storeId = $product->getStore()->getId())
            $storeId = null;

        $count = 0;
        $count = Mage::getResourceModel('productquestions/productquestions')
                ->setProductTitleById($product->getId(), $product->getName(), $storeId);
        return $count;
    }

    /**
     * @param $observer
     * @return $count, needed for tests
     */
    public function deleteProductQuestionsForProduct($observer) {
        $product = $observer->getEvent()->getProduct();

        if (!$storeId = $product->getStore()->getId())
            $storeId = null;

        $count = 0;
        $count = Mage::getResourceModel('productquestions/productquestions')
                ->deleteByProductId($product->getId(), $storeId);
        return $count;
    }

}

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


class AW_Productquestions_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case {

    public function setUp() {
        parent::setUp();
        $this->_data = Mage::getModel('productquestions/observer');
    }

    /**
     * @test
     * @loadFixture questions
     */
    public function updateProductQuestionsProductsNames() {
        $observer = new Varien_Object();
        $event = new Varien_Object();
        $store = Mage::getModel('core/store');
        $product = Mage::getModel('catalog/product');
        $product->setId(12);
        $product->setName('Product');
        $product->setStore($store);
        $event->setProduct($product);
        $observer->setEvent($event);
        $this->assertEquals(2, $this->_data->updateProductQuestionsProductsNames($observer));
    }

    /**
     * @test
     * @loadFixture questions
     */
    public function deleteProductQuestionsForProduct() {
        $observer = new Varien_Object();
        $event = new Varien_Object();
        $store = Mage::getModel('core/store');
        $product = Mage::getModel('catalog/product');
        $product->setId(12);
        $product->setStore($store);
        $event->setProduct($product);
        $observer->setEvent($event);
        $this->assertEquals(2, $this->_data->deleteProductQuestionsForProduct($observer));
    }

}

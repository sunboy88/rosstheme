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


class AW_Productquestions_Test_Helper_Producttab extends EcomDev_PHPUnit_Test_Case {

    public function setUp() {
        parent::setUp();
        $this->_data = Mage::helper('productquestions');
    }

    /**
     * @test
     * @dataProvider provider_getparams
     */
    public function getTabparams($data) {
        $frontName = Mage::app()->getConfig()->getNode('admin/routers/productquestions_admin/args/frontName');
        Mage::app()->getRequest()->setParam('id', $data['id']);
        $productId = Mage::app()->getRequest()->getParam('id');

        $check = array(
            'label' => Mage::helper('catalog')->__('Productquestions'),
            'url' => Mage::getUrl($frontName . '/adminhtml_index/', array('id' => $productId, '_current' => true)),
            'class' => 'ajax',
        );
        $result = AW_Productquestions_Helper_Producttab::getTabparams();
        $this->assertEquals($check, $result);
    }

    public function provider_getparams() {
        return array(
            array(array('id' => '1')),
            array(array('id' => '2')),
            array(array('id' => ''))
        );
    }

}
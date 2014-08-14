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


class AW_Productquestions_Test_Model_Urlrewrite extends EcomDev_PHPUnit_Test_Case {

    public function setUp() {
        parent::setUp();
        $this->_data = Mage::getModel('productquestions/urlrewrite');
    }

    /**
     * @test
     * @loadFixture product
     * @loadFixture questions
     */
    public function rewrite() {
        $request = new Zend_Controller_Request_Http();
        $response = new Zend_Controller_Response_Http();
        $request->setPathInfo('productquestions/index/index/id/12/category/2/-questions.html');
        $result = $this->_data->rewrite($request, $response);
        $this->assertEquals(false, $result);
    }

}
<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Collpur_Test_Controller_Dealscontroller extends EcomDev_PHPUnit_Test_Case_Controller {

    public function setup() {
        AW_Collpur_Test_Model_Mocks_Foreignresetter::dropForeignKeys();
        parent::setup();
    }

    /**
     * @test
     * If there are no deals yet we redirect to the page with message
     */
    public function emptyAction() {        

        $this->dispatch('deals/');
        $request = Mage::app()->getRequest();
        $this->assertEquals(AW_Collpur_Controller_Router::FRONT_ROUTER, $request->getModuleName());
        $this->assertEquals(AW_Collpur_Controller_Router::FRONT_CONTROLLER, $request->getControllerName());
        $this->assertEquals('empty', $request->getActionName());

        $this->assertLayoutBlockCreated('aw_collpur_deal_empty');
        $this->assertLayoutBlockRendered('aw_collpur_deal_empty');
        $this->assertResponseBodyContains('There are no deals available');
    }

    /**
     * @test
     * @loadFixture listAction
     * @loadFixture aw_collpur_rewrite
     * @loadFixture inventoryStockItem
     * @loadFixture catalog_product_website
     * if ($dealId = $this->_rewriteResource->loadByKey($key, Mage::app()->getStore()->getId()))
     */
    
     public function testRedirectToDealViewByKey() {
       
            $this->dispatch('deals/htc-touch-diamond.html');
            $this->assertLayoutBlockCreated('aw_collpur_deal_view');
            $this->assertLayoutBlockRendered('aw_collpur_deal_view');      

     }

    /**
     * @test
     * @loadFixture
     * @loadFixture inventoryStockItem
     * @loadFixture catalog_product_website
     * This dispatch action is the same when user clicks 'Deals' link at the top links    
     */
    public function listAction() {
      
        /* test closed deals redirect */
        $this->dispatch('deals/' . AW_Collpur_Helper_Deals::CLOSED . '.html');
        $request = Mage::app()->getRequest();
        /* Assert write redirect deals/deals/list/section/closed/ */
        $this->assertEquals(AW_Collpur_Controller_Router::FRONT_ROUTER, $request->getModuleName());
        $this->assertEquals(AW_Collpur_Controller_Router::FRONT_CONTROLLER, $request->getControllerName());
        $this->assertEquals(AW_Collpur_Controller_Router::LIST_ACTION, $request->getActionName());
        $this->assertEquals(AW_Collpur_Helper_Deals::CLOSED, $request->getParam('section'));
        $request->clearParams();

         
        /* test active deals redirect */
        $this->dispatch('deals/' . AW_Collpur_Helper_Deals::RUNNING . '.html');
        $request = Mage::app()->getRequest();
        /* Assert write redirect deals/deals/list/section/closed/ */
        $this->assertEquals(AW_Collpur_Controller_Router::FRONT_ROUTER, $request->getModuleName());
        $this->assertEquals(AW_Collpur_Controller_Router::FRONT_CONTROLLER, $request->getControllerName());
        $this->assertEquals(AW_Collpur_Controller_Router::LIST_ACTION, $request->getActionName());
        $this->assertEquals(AW_Collpur_Helper_Deals::RUNNING, $request->getParam('section'));
        $request->clearParams();

        
        /* test closed deals */
        $this->dispatch('deals/' . AW_Collpur_Helper_Deals::NOT_RUNNING . '.html');
        $request = Mage::app()->getRequest();
        /* Assert write redirect deals/deals/list/section/closed/ */
        $this->assertEquals(AW_Collpur_Controller_Router::FRONT_ROUTER, $request->getModuleName());
        $this->assertEquals(AW_Collpur_Controller_Router::FRONT_CONTROLLER, $request->getControllerName());
        $this->assertEquals(AW_Collpur_Controller_Router::LIST_ACTION, $request->getActionName());
        $this->assertEquals(AW_Collpur_Helper_Deals::NOT_RUNNING, $request->getParam('section'));
        $request->clearParams();
       
 
          //  $this->dispatch('deals/' . AW_Collpur_Helper_Deals::FEATURED . '.html');
           // $request = Mage::app()->getRequest();
          //  $this->assertEquals(AW_Collpur_Controller_Router::FRONT_ROUTER, $request->getModuleName());
          //  $this->assertEquals(AW_Collpur_Controller_Router::FRONT_CONTROLLER, $request->getControllerName());
          //  $this->assertEquals(AW_Collpur_Controller_Router::VIEW_ACTION, $request->getActionName());
           // $this->assertEquals(AW_Collpur_Helper_Deals::FEATURED, $request->getParam('mode'));
           // $request->clearParams();
          
        $this->dispatch('deals/dealdoesntexist.html');
        $headers = Mage::app()->getResponse()->getHeaders();
        $err404 = false;
        foreach ($headers as $header) {
            if ($header['value'] == '404 Not Found' && $header['name'] == 'Http/1.1') {
                $err404 = true;
                break;
            }
        }
        $this->assertEquals(1, (int) $err404);
    }

}

?>

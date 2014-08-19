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


class AW_Collpur_Test_Block_Basedeal extends EcomDev_PHPUnit_Test_Case {

    /**
     *
     * @test
     * @dataProvider provider__getDealImage
     */
    public function getDealImage() {

        $appLocal = 'app/code/local/AW/Collpur/Test/Block';
        $cachePath = 'aw_collpur/deals/cache';
        $baseDir = Mage::getBaseDir();

        shell_exec("cp -r $baseDir/$appLocal/media $baseDir/");

        $block = new AW_Collpur_Block_BaseDeal();
        /* Call with no parameters shoud return default image holder
         * catalog/product/placeholder/small_image
         */
        $this->assertEquals(preg_match(1, "#catalog/product/placeholder/small_image#is", $block->getDealImage()));

        /*
         * As we in catalog mode and there is file uploaded media/aw_collpur/deals/2.jpg
         * and there is nothing in cache ..deals/catalog/2.jpg
         * 1. Dir catalog should be created and
         * 2. 2.jpg file should be resized and put there
         */
        $block->getDealImage('2.jpg');
        $this->assertEquals(is_dir("{$baseDir}/media/{$cachePath}/catalog"), true);
        $this->assertEquals(file_exists("{$baseDir}/media/{$cachePath}/catalog/2.jpg"), true);
        /* Call again to make sure cached image call will not fail */
        $block->getDealImage('2.jpg');
        /*
         * Change cache mode and try again        
         */
        $block->_mode = 'product';
        $block->getDealImage('2.jpg');
        $this->assertEquals(is_dir("{$baseDir}/media/{$cachePath}/product"), true);
        $this->assertEquals(file_exists("{$baseDir}/media/{$cachePath}/product/2.jpg"), true);
        /* Call again to make sure cached image call will not fail */
        $block->getDealImage('2.jpg');
    }

    public function provider__getDealImage() {

        return array(
            array(1)
        );
    }

    /**
     * 
     *  @test
     *  @dataProvider provider__processDealName
     *  @loadFixture
     *  This function is a duplicate of collpur/deal model ->getDealName
     *  Return deal name: if null product name
     * 
     */

    public function processDealName($data) {

        $deal = Mage::getModel('collpur/deal')->load($data['dealId']);
        $block = new AW_Collpur_Block_BaseDeal();
        $this->assertEquals($data['dealName'], $block->processDealName($deal));
    }

    public function provider__processDealName() {

        return array(
            array(
                array('dealId' => 1, 'dealName' => 'Name'),
            ),
            array(
                array('dealId' => 2, 'dealName' => 'Nokia')
            ),
        );

        
    }


}
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


class AW_Collpur_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case {

    /**
     * Core function returning timeDiff
     * Use gmmktime and gmdate to generate mock timestamps
     *
     * @dataProvider provider__testGetGmtTimestamp
     * @loadFixture
     * @loadExpectation
     */
    public function testGetGmtTimestamp($data) {

        if ($data['timezone'] != false) {
            @ini_set('date.timezone', $data['timezone']);
        }

        if ($data['now'] == true) {
            $currentDate = AW_Collpur_Helper_Data::getGmtTimestamp(true, true, false, false);
            if (true !== (gmdate('Y-m-d h:i:s') == $currentDate->toString('YYYY-MM-dd HH:mm:ss'))) {
                $this->fail('Incorrect current date time stamp calculation!');
            }
            
            $this->assertEquals(gmdate('Y-m-d h:i:s'),AW_Collpur_Helper_Data::getGmtTimestamp(true, true, false, 'toString'));

            return;
        }

        if (isset($data['dbDate']) && $data['add'] == false) {
            $stamp = AW_Collpur_Helper_Data::getGmtTimestamp($data['dbDate']);
            $this->assertEquals($stamp, $data['expectedStamp']);
            return;
        }

        if ($data['add'] == true) {
            $dateAddedStamp = AW_Collpur_Helper_Data::getGmtTimestamp($data['dbDate'], false, $data['add']);
            $this->assertEquals($dateAddedStamp, $data['expectedStamp']);
            return;
        }
    }

    public function provider__testGetGmtTimestamp() {

        return array(
            array(
                array('dbDate' => '2011-09-05 01:28:30', 'expectedStamp' => '1315186110', 'add' => false, 'now' => false, 'add' => false, 'timezone' => false),
            ),
            array(
                array('now' => true, 'add' => false, 'timezone' => 'Africa/Accra')
            ),
            array(
                array('dbDate' => '2011-09-05 01:28:30', 'expectedStamp' => '1315358910', 'now' => false, 'add' => '2', 'timezone' => 'Europe/Malta')
            )
        );
    }


    /**
     * @test
     */
    public function testExtensionEnabled() {
        $this->assertEquals(true,Mage::helper('collpur')->extensionEnabled('AW_Collpur'));
        $this->assertEquals(false,Mage::helper('collpur')->extensionEnabled('NOT_Collpur'));
    }

    /**
     * @test
     * Simple coverage to make sure that function exists
     */
    public function magentoLess14() {
        Mage::helper('collpur')->magentoLess14();
    }
    


}
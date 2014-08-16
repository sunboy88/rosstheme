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


class AW_Collpur_Helper_Data extends Mage_Core_Helper_Abstract {

    const ROUTER = 'deals';
    const PREFIX_MODULE = 'aw_collpur';

    public function magentoLess14() {
        return version_compare(Mage::getVersion(), '1.4', '<');
    }

    public function extensionEnabled($extensionName) {
        $modules = (array) Mage::getConfig()->getNode('modules')->children();
        if (!isset($modules[$extensionName])
                || $modules[$extensionName]->descend('active')->asArray() == 'false'
                || Mage::getStoreConfig('advanced/modules_disable_output/' . $extensionName)
                || !Mage::getStoreConfig('collpur/general/enable')
        )
            return false;
        return true;
    }

    /**
     * Use our own method to get buyRequest
     * @param Mage_Sales_Model_Quote_Item $salesItem
     * @return Varien_Object
     *
     */

    public function getBuyRequest($salesItem,$option = false) {

        if($option)  {
            $option = $salesItem->getOptionByCode('info_buyRequest');
            $buyRequest = new Varien_Object($option && $option->getValue() ? unserialize($option->getValue()) : null);      
            $buyRequest->setOriginalQty($buyRequest->getQty())->setQty($salesItem->getQty() * 1);
            return $buyRequest;
         }
 
        $option = $salesItem->getProductOptionByCode('info_buyRequest');
        if (!$option) {
            $option = array();
        }
        $buyRequest = new Varien_Object($option);
        $buyRequest->setQty($salesItem->getQtyOrdered() * 1);
        return $buyRequest;
   }

    /**
     * Core function calculating timeDiff
     * @param string $date
     * @param bool $now
     * @param int|string $add
     * @param bool $timestamp
     * @return Zend_Date
     * 
     */

    public static function getGmtTimestamp($date = false, $now = false, $add = false, $timestamp = true) {


        /* It's incorrect call of the method */
        if (!$date) { return false; }

        if (!ini_get('date.timezone')) {
            if ($timezone = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE)) {
                @ini_set('date.timezone', $timezone);
            }
        }

        if ($now) {
            $date = gmdate('Y-m-d H:i:s',gmdate('U'));
        }

        $date = new Zend_Date($date, Zend_Date::ISO_8601);
        $date->setTimezone('UTC');

        if ($add) {
            $date->addDay($add);
        }

        if (!$timestamp) {
            return $date;
        }

        if($timestamp === 'toString') {
            return $date->toString('YYYY-MM-dd HH:mm:ss');
        }


        return $date->getTimestamp();
    }

}
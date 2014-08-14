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


class AW_Productquestions_Helper_Producttab extends Mage_Core_Helper_Abstract {
    /*
     * Returns Product Edit Productquestions Tab params
     * @return array
     */

    public static function getTabparams() {
        $frontName = Mage::app()->getConfig()->getNode('admin/routers/productquestions_admin/args/frontName');
        $productId = Mage::app()->getRequest()->getParam('id');
        $_isSecure = Mage::getStoreConfig('web/secure/use_in_adminhtml');
        return array(
            'label' => Mage::helper('catalog')->__('Product Questions'),
            'url' => Mage::getUrl($frontName . '/adminhtml_index/', array('id' => $productId, '_current' => true, '_secure' => $_isSecure)),
            'class' => 'ajax',
        );
    }

}
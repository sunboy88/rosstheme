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
 * @package    AW_Popup
 * @version    1.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Popup_Model_Source_Page extends AW_Popup_Model_Source_Abstract
{
    const HOME_PAGE_ID = 1;
    const PRODUCT_VIEW_ID = 2;
    const CATEGORY_VIEW_ID = 3;
    const CMS_PAGES_ID = 4;
    const CUSTOMER_AREA_ID = 5;
    const CHECKOUT_ID = 6;
    const CART_ID = 7;

    const HOME_PAGE_LABEL = 'Home Page';
    const PRODUCT_VIEW_LABEL = 'Product View';
    const CATEGORY_VIEW_LABEL = 'Category View';
    const CMS_PAGES_LABEL = 'CMS Pages';
    const CUSTOMER_AREA_LABEL = 'Customer Area';
    const CHECKOUT_LABEL = 'Checkout';
    const CART_LABEL = 'Cart';

    const HOME_PAGE_NAME = 'home';
    const PRODUCT_VIEW_NAME = 'product';
    const CATEGORY_VIEW_NAME = 'category';
    const CMS_PAGES_NAME = 'cms';
    const CUSTOMER_AREA_NAME = 'customer';
    const CHECKOUT_NAME = 'checkout';
    const CART_NAME = 'cart';

    public function toOptionArray()
    {
        $helper = $this->_getHelper();
        return array(
            array('value' => self::HOME_PAGE_ID, 'label' => $helper->__(self::HOME_PAGE_LABEL)),
            array('value' => self::PRODUCT_VIEW_ID, 'label' => $helper->__(self::PRODUCT_VIEW_LABEL)),
            array('value' => self::CATEGORY_VIEW_ID, 'label' => $helper->__(self::CATEGORY_VIEW_LABEL)),
            array('value' => self::CMS_PAGES_ID, 'label' => $helper->__(self::CMS_PAGES_LABEL)),
            array('value' => self::CUSTOMER_AREA_ID, 'label' => $helper->__(self::CUSTOMER_AREA_LABEL)),
            array('value' => self::CHECKOUT_ID, 'label' => $helper->__(self::CHECKOUT_LABEL)),
            array('value' => self::CART_ID, 'label' => $helper->__(self::CART_LABEL)),
        );

    }

    /**
     * Retrieve Page Id by Page Name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getPageIDByName($name)
    {
        $pages = array(
            self::HOME_PAGE_NAME     => self::HOME_PAGE_ID,
            self::PRODUCT_VIEW_NAME  => self::PRODUCT_VIEW_ID,
            self::CATEGORY_VIEW_NAME => self::CATEGORY_VIEW_ID,
            self::CMS_PAGES_NAME     => self::CMS_PAGES_ID,
            self::CUSTOMER_AREA_NAME => self::CUSTOMER_AREA_ID,
            self::CHECKOUT_NAME      => self::CHECKOUT_ID,
            self::CART_NAME          => self::CART_ID,
        );
        return $pages[$name];
    }
}
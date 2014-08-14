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


class AW_Popup_Helper_Data extends Mage_Core_Helper_Abstract
{
    const COOKIE_NAME = 'aw_pop';

    /** Check is extension installed and active */
    public static function isExtensionAvailable($extension = null)
    {
        if (!$extension) {
            return false;
        }
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        return array_key_exists($extension, $modules)
            && 'true' == (string)$modules[$extension]->active
            && !(bool)Mage::getStoreConfig("advanced/modules_disable_output/{$extension}")
        ;
    }

    /**
     * Retrieve Default Align
     *
     * @return number
     */
    public function getDefaultPosition()
    {
        return Mage::getStoreConfig('popup/interface/default_position');
    }

    /**
     * Retrieve Default TimeOut for Hide Automatically
     *
     * @return number
     */
    public function getAutoHide()
    {
        return Mage::getStoreConfig('popup/interface/auto_hide');
    }

    public function getCookiesLifetime()
    {
        return Mage::getStoreConfig('popup/interface/cookies_lifetime');
    }

    /**
     * Retrieve count of popups in the base
     *
     * @return number
     */
    public function getPopupCount()
    {
        $collection = Mage::getModel('popup/popup')->getCollection();
        return count($collection);
    }

    /**
     * Save viewed popup into cookie
     */
    public function setViewedPopup($id)
    {
        /** @var $cookies   Mage_Core_Model_Cookie */
        $cookies = Mage::getModel('core/cookie');
        if (!$cookies->get(self::COOKIE_NAME . $id)) {
            $cookies->set(self::COOKIE_NAME . $id, $id, Mage::helper('popup')->getCookiesLifetime());
        }
    }

    /**
     * Retrieve viewed popups
     *
     * @return string
     */
    public function getViewedPopups()
    {
        $cookies = Mage::getModel('core/cookie');
        $viewedPopup = array();
        foreach ($cookies->get() as $key => $item) {
            if (strpos($key, self::COOKIE_NAME) !== false) {
                $viewedPopup[] = $item;
            }
        }
        return $viewedPopup;
    }

    public function converVariables($content)
    {
        $filters = array(
            0 => 'Mage_Catalog_Model_Template_Filter',
            1 => 'Mage_Newsletter_Model_Template_Filter',
            2 => 'Mage_Cms_Model_Template_Filter',
            3 => 'Mage_Widget_Model_Template_Filter',
            4 => 'Mage_Core_Model_Email_Template_Filter',
        );
        $coreData = new Mage_Core_Model_Config();
        $dir = $coreData->getDistroServerVars();
        foreach ($filters as $filter) {
            $path = $dir['app_dir'] . '/code/core/' . str_replace('_', '/', $filter);
            if (file_exists($path . '.php') && class_exists($filter)) {
                $processor = new $filter;
                $content = $processor->filter($content);
            }
        }

        return $content;
    }

    /**
     * Retrieve popup that satisfies conditions
     *
     * @param string $pageName
     *
     * @return AW_Popup_Model_Popup
     */
    public function getPopup($pageName)
    {
        $pageId = Mage::getModel('popup/source_page')->getPageIDByName($pageName);
        $collection = Mage::getModel('popup/popup')->getPopupCollectionByPageId($pageId);
        $collection->includeCustomerStat();

        if (count($this->getViewedPopups()) > 0) {
            $collection->addFieldToFilter('main_table.popup_id', array('nin' => $this->getViewedPopups()));
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        foreach ($collection as $_item) {

            if ($this->isExtensionAvailable('AW_Marketsuite') && $_item->getData('mss_rule_id')) {
                $mssVerification = Mage::getModel('marketsuite/api')->checkRule(
                    $customer, $_item->getData('mss_rule_id')
                );

                if (!$mssVerification) {
                    unset($_item);
                    continue;
                }
            }

            $popup = $_item;
            $_item->addVisit();
            break;
        }

        if (!isset($popup)) {
            return null;
        }

        $popup->setData('popup_content', $this->converVariables($popup->getData('popup_content')));
        $popup->setData('title', htmlspecialchars($popup->getData('title'), ENT_QUOTES));
        if (!$popup->getData('width')) {
            if (preg_match('/width=.([0-9]*)/', $popup->getData('popup_content'), $matches)) {
                $popup->setData('width', $matches[1]);
            } else {
                $popup->setData('width', 600);
            }
        }
        return $popup;
    }

    public static function recursiveReplace($search, $replace, $subject)
    {
        if (!is_array($subject)) {
            return $subject;
        }

        foreach ($subject as $key => $value) {
            if (is_string($value)) {
                $subject[$key] = str_replace($search, $replace, $value);
            } elseif (is_array($value)) {
                $subject[$key] = self::recursiveReplace($search, $replace, $value);
            }
        }

        return $subject;
    }

    public function getModuleName()
    {
        $cmsPageId = Mage::getSingleton('cms/page')->getIdentifier();
        $homePageId = Mage::getStoreConfig('web/default/cms_home_page', Mage::app()->getStore()->getId());
        if ($cmsPageId === $homePageId) {
            return AW_Popup_Model_Source_Page::HOME_PAGE_NAME;
        }
        switch (Mage::app()->getRequest()->getModuleName()) {
            case 'catalog':
                return Mage::app()->getRequest()->getControllerName();
                break;
            case 'checkout':
                if (strcmp(Mage::app()->getRequest()->getControllerName(), 'cart') == 0) {
                    return AW_Popup_Model_Source_Page::CART_NAME;
                } else {
                    return AW_Popup_Model_Source_Page::CHECKOUT_NAME;
                }
                break;
            case 'cms':
                return AW_Popup_Model_Source_Page::CMS_PAGES_NAME;
                break;
            case 'customer':
                if ((strcmp(Mage::app()->getRequest()->getControllerName(), 'account') == 0)
                    && (strcmp(Mage::app()->getRequest()->getActionName(), 'index') == 0)
                ) {
                    return AW_Popup_Model_Source_Page::CUSTOMER_AREA_NAME;
                }
        }
        return null;
    }
}
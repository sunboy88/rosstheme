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


class AW_Collpur_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $section = (string) @strip_tags(Mage::app()->getRequest()->getParam('section'));
        if (AW_Collpur_Model_Source_Menus::isNotAllowed($section)) {
            $this->norouteAction();
            return;
        }

        if (!$section || $section == AW_Collpur_Helper_Deals::FEATURED) {
            $startPage = AW_Collpur_Model_Source_Menus::getFirstAvailable();
            if ($startPage == AW_Collpur_Helper_Deals::FEATURED) {
                $this->getResponse()->setRedirect(Mage::getUrl('deals/index/view',
                    array(
                        '_secure' => Mage::app()->getStore(true)->isCurrentlySecure(),
                        '_store' => Mage::app()->getStore()->getId(),
                        'id' => Mage::getModel('collpur/deal')->getRandomFeaturedId(),
                        'mode' => AW_Collpur_Helper_Deals::FEATURED
                    )
                ));
            } elseif($startPage) {
                $this->getResponse()->setRedirect(Mage::getUrl('deals/index/list',
                    array(
                        '_secure' => Mage::app()->getStore(true)->isCurrentlySecure(),
                        '_store' => Mage::app()->getStore()->getId(),
                        'section' => $startPage
                    )
                ));
            }
        }
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('tag/session');
        $this->_initLayoutMessages('checkout/session');

        $layout = $this->getLayout();
        $layout->getBlock('content')->append(
            $layout->createBlock('collpur/deals')
        );
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();

        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('tag/session');
        $this->_initLayoutMessages('checkout/session');

        $this->renderLayout();
    }

    public function emptyAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
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


class AW_Collpur_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract {

    protected $_rewriteResource;
    
    const FRONT_ROUTER = 'deals';
    const FRONT_CONTROLLER = 'index';
    const LIST_ACTION = 'list';
    const VIEW_ACTION = 'view';

    public function initControllerRouters($observer) {
        $front = $observer->getEvent()->getFront();
        $deals = new AW_Collpur_Controller_Router();
        $front->addRouter('deals', $deals);
    }

    public function match(Zend_Controller_Request_Http $request) {

        $this->_rewriteResource = Mage::getResourceModel('collpur/rewrite');

        if (!Mage::app()->isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                    ->setRedirect(Mage::getUrl('install'))
                    ->sendResponse();
            exit;
        }
        if ($this->_combineRequest($request)) {
            return true;
        }
        return false;
    }

    private function _combineRequest($request) {

        $identifier = $request->getPathInfo();

        /*
         *  Immidiate return if
         *  It's not a deal module      
         */

        if (!preg_match("#(^/)*deals/#is", $identifier)) {
            return false;
        }

        if (!AW_Collpur_Model_Source_Menus::getFirstAvailable() && $identifier == '/deals/') {
            /* No deals available and we redirect to the empty page and only if user clicks deals link i.e goes to the route page
             * otherwise redirect to 404             
             */
            $request->setModuleName('deals')->setControllerName('index')->setActionName('empty');
            return true;
        }

        /* As a default start page assumed to be featured deal, but if it's unavailable,
         * there is no choice but redirect to the first available list section
         */
        if ($identifier == '/deals/' || preg_match("#/deals/" . AW_Collpur_Helper_Deals::FEATURED . "#is", $identifier)) {
            if (AW_Collpur_Model_Source_Menus::isNotAllowed(AW_Collpur_Helper_Deals::FEATURED) || !Mage::getModel('collpur/deal')->getRandomFeaturedId()) {
                $startPage = AW_Collpur_Model_Source_Menus::getFirstAvailable();
                $request->setModuleName('deals')
                        ->setControllerName('index')
                        ->setActionName('list')
                        ->setParam('section', $startPage);
                return true;
            }
            $request->setModuleName('deals')
                    ->setControllerName('index')
                    ->setActionName('view')
                    ->setParam('id', Mage::getModel('collpur/deal')->getRandomFeaturedId())
                    ->setParam('mode', AW_Collpur_Helper_Deals::FEATURED);
            return true;
        }

        /* Handle list category  mode */
        $key = preg_replace("#(/|deals/|\..+)#is", "", $identifier);
        /* Clearing $key */
        if (in_array($key, AW_Collpur_Helper_Deals::getSectionsKeys())) {
            $request->setModuleName('deals')
                    ->setControllerName('index')
                    ->setActionName('list')
                    ->setParam('section', $key);
            return true;
        }
        /* Default handle for deal view */
        if ($dealId = $this->_rewriteResource->loadByKey($key, Mage::app()->getStore()->getId())) {
           $deal = Mage::getModel('collpur/deal')->load($dealId);   
            if ($deal->isAllowed() && !$deal->isArchived() && $deal->getId()) {
                $request->setModuleName('deals')
                        ->setControllerName('index')
                        ->setActionName('view')
                        ->setParam('id', $dealId);

                return true;
            }
        }
    }

}

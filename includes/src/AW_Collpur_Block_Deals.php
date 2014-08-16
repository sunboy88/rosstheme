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

class AW_Collpur_Block_Deals extends AW_Collpur_Block_BaseDeal
{

    protected $_baseDeal;
    protected $_collection;
    private $_limitParam = NULL;

    protected function _construct()
    {
        parent::_construct();
        if ($this->getCmsmode()) {
            AW_Collpur_Helper_Deals::setActiveMenus($this->getCmsmode());
        }
        $this->setTemplate('aw_collpur/deals/list.phtml');
        $this->setAvailableDealsScope($this->getAvailableDeals());
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }

    public function getAvailableDeals()
    {

        if (isset($this->_data['available_deals_scope'])) {
            return $this->getAvailableDealsScope();
        }

        $dealsCollection = Mage::getModel('collpur/deal')
            ->getCollection()
            ->addIsActiveFilter();

        $section = Mage::app()->getRequest()->getParam('section');

        if ($section == AW_Collpur_Helper_Deals::CLOSED || $this->getCmsmode() == AW_Collpur_Helper_Deals::CLOSED) {
            $dealsCollection->getClosedDeals();
            $this->_limitParam = AW_Collpur_Helper_Deals::CLOSED;
        } elseif ($section == AW_Collpur_Helper_Deals::NOT_RUNNING || $this->getCmsmode() == AW_Collpur_Helper_Deals::NOT_RUNNING) {
            $dealsCollection->getFutureDeals();
            $this->_limitParam = AW_Collpur_Helper_Deals::NOT_RUNNING;
        } elseif ($section == AW_Collpur_Helper_Deals::RUNNING || $this->getCmsmode() == AW_Collpur_Helper_Deals::RUNNING) {
            $dealsCollection->getActiveDeals();
            $this->_limitParam = AW_Collpur_Helper_Deals::RUNNING;
        } elseif ($section == AW_Collpur_Helper_Deals::FEATURED || $this->getCmsmode() == AW_Collpur_Helper_Deals::FEATURED) {
            $dealsCollection->getActiveDeals()->addFeaturedFilter();
            $this->_limitParam = AW_Collpur_Helper_Deals::FEATURED;
        } else {
            return new Varien_Data_Collection;
        }

        return $dealsCollection;
    }

    protected function _prepareLayout()
    {
        $pager = $this->getLayout()->createBlock('page/html_pager', 'available_deals_pager');
        $pager->setAvailableLimit(array("10" => "10", "15" => "15", "25" => "25"));
        $pager->setLimitVarName('dealslimit' . $this->_limitParam);
        $pager->setPageVarName('dealsvarname' . $this->_limitParam);
        $pager->setCollection($this->getAvailableDeals());
        $this->setChild('available_deals_pager', $pager);
        $this->_modifyCrumbs($this->getLayout(), false, false, 'category');
    }

    public function isNative()
    {
        return 'deals' == Mage::app()->getRequest()->getModuleName();
    }
}

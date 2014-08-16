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

class AW_Popup_Model_Popup extends Mage_Core_Model_Abstract
{
    private $_popupsForPage = null;

    protected function _beforeSave()
    {
        // convert StoreView from Array to String
        $storeView = $this->getStoreView();
        if (is_array($storeView)) {
            $storeView = implode(',', $storeView);
        }
        if ($storeView) {
            $this->setStoreView($storeView);
        }

        // convert ShowAt from Array to String
        $showAt = $this->getShowAt();
        if (is_array($showAt)) {
            $showAt = implode(',', $showAt);
        }
        if ($showAt) {
            $this->setShowAt($showAt);
        }
        return $this;
    }

    public function getPopupCollectionByPageId($pageId)
    {
        $today = date('Y-m-d');
        if (!$this->_popupsForPage) {
            $this->_popupsForPage = $this->getCollection()
                ->addFieldToFilter('status', array('eq' => 1))
                ->addShowAtFilter($pageId)
                ->addFilterByStoreId(Mage::app()->getStore()->getId())
                ->addFieldToFilter('date_from', array('lteq' => $today))
                ->addFieldToFilter('date_to', array('gteq' => $today))
                ->addOrder('sort_order', 'ASC')
            ;
        }
        return $this->_popupsForPage;
    }

    public function addVisit()
    {
        if (!$popupId = $this->getId()) {
            return;
        }
        $this->setUseCount($this->getUseCount() + 1);
        $this->save();

        $data = array();
        $data['popup_id'] = $popupId;
        $sessionData = Mage::getSingleton('customer/session');
        if ($sessionData->getCustomerId()) {
            $data['customer_id'] = $sessionData->getCustomerId();
        }
        $data['session_id'] = $sessionData->getEncryptedSessionId();

        $model = Mage::getModel('popup/stat');
        $model->addData($data);
        $model->save();
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('popup/popup');
    }
}
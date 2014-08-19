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


class AW_Collpur_Adminhtml_CouponsController extends Mage_Adminhtml_Controller_Action {

    protected $_params;

    public function massDeleteAction() {

        $this->_params = $params = Mage::app()->getRequest()->getParams();

        if (!is_array($params['awcp_coupons'])) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {

                $coupons = Mage::getModel('collpur/coupon')->getCollection()->addFieldToFilter('id', array('in' => $params['awcp_coupons']));
                $coupons->deleteCoupons($coupons);

                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully deleted', count($params['awcp_coupons']))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }


        $this->_redirect($this->_getRoot(), $this->_getParams());
    }

    public function massStatusAction() {


        $this->_params = $params = Mage::app()->getRequest()->getParams();


        if (!is_array($params['awcp_coupons'])) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {

                $coupons = Mage::getModel('collpur/coupon')->getCollection()->addFieldToFilter('id', array('in' => $params['awcp_coupons']));
                $coupons->updateStatus($coupons, $params['status']);

                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($params['awcp_coupons']))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect($this->_getRoot(), $this->_getParams());
    }

    protected function _getRoot() {

        return "*/{$this->_params['controller']}/{$this->_params['action']}";
    }

    protected function _getParams() {

        return array('id' => $this->_params['id'], 'tab' => $this->_params['tab']);
    }

}
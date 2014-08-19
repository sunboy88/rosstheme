<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/******************************************
 *      MAGENTO EDITION USAGE NOTICE      *
 ******************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/******************************************
 *      DISCLAIMER                        *
 ******************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 ******************************************
 * @category   Belvg
 * @package    Belvg_Referralreward
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_CustomerController extends Mage_Core_Controller_Front_Action
{
    const HANDLE_CART      = 'checkout_cart_index';
    const BLOCK_NAME_TOTAL = 'checkout.cart.totals';

    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/cart')->getQuote();
    }

    protected function totalAction()
    {
        $discountPoints = (int) $this->getRequest()->getParam('discount');
        $settings      = Mage::helper('referralreward')->getSettings();

        if ($settings['use_coupon']) {
            Mage::helper('referralreward')->createCouponForReferral($discountPoints);
        } else {
            Mage::getSingleton('core/session')->setPointsDiscount($discountPoints);
        }

        $this->_getQuote()->collectTotals();//->save();

        $this->getLayout()->getUpdate()->addHandle(self::HANDLE_CART);
        $this->loadLayout();

        $return = array(
            'error' => 0,
            'total' => $this->_getBlockHtml(self::BLOCK_NAME_TOTAL),
        );

        $this->getResponse()->setBody( Mage::helper('core')->jsonEncode($return) );
    }

    protected function _getBlockHtml($blockName)
    {
        $block = $this->getLayout()->getBlock($blockName);

        return ($block instanceof Mage_Core_Block_Template) ? $block->toHtml() : '';
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function _initCustomerAccount()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account/');

            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
    }

    /**
     * Default customer account referral system page
     */
    public function indexAction()
    {
        $this->_initCustomerAccount();
        $this->renderLayout();
    }

    /**
     * Points Transfer to Other
     */
    public function transferAction()
    {
        $helper   = Mage::helper('referralreward');
        $settings = $helper->getSettings();
        if (!$settings['transfer']) {
            $this->_redirect('*/*/');
        }

        $this->_initCustomerAccount();
        $this->renderLayout();
    }

    protected function _loadByEmail($email)
    {
        $customer = Mage::getModel("customer/customer");
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($email);

        return $customer;
    }

    public function transferPostAction()
    {
        $helper   = Mage::helper('referralreward');
        $settings = $helper->getSettings();
        if (!$settings['transfer']) {
            $this->_redirect('*/*/');
        }

        $emailCheck = $this->getRequest()->getParam('transfer-email-check');
        if ($emailCheck) {
            return $this->_transferEmailCheckResult();
        };

        $email    = $this->getRequest()->getParam('transfer-email');
        $customer = $this->_loadByEmail($email);

        if (!$customer->getId()) {
            $this->_getSession()->addError($this->__("Customer with that email does not exist"));
            $this->_redirect('*/*/transfer');

            return;
        }

        if ($customer->getId() == $this->_getSession()->getId()) {
            $this->_getSession()->addError($this->__("You can't send points to yourself"));
            $this->_redirect('*/*/transfer');

            return;
        }

        $points     = (int) abs($this->getRequest()->getParam('transfer-points'));
        $helper     = Mage::helper('referralreward');
        $pointsItem = $helper->getItemCurrentCustomer();
        if ($points && $points <= $pointsItem->getPoints()) {
            try {
                $logModel = $helper->getLogModel(Belvg_Referralreward_Model_Points_Log::TYPE_TRANSFER);
                $logModel->supplementPoints($customer, array(
                    'points'    => $points,
                    'object_id' => $pointsItem->getCustomerId(),
                ));
                $logModel->withdrawPoints($pointsItem->setWithdrawPoints($points));
                $this->_getSession()->addSuccess($this->__('%s points transferred', $this->__(($points == 1) ? '%s point' : '%s points', $points)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        } else {
            $this->_getSession()->addError($this->__("Points value isn't valid"));
        }

        $this->_redirect('*/*/transfer');

        return;
    }

    protected function _transferEmailCheckResult()
    {
        $email    = $this->getRequest()->getParam('transfer-email');
        $customer = $this->_loadByEmail($email);
        $result   = array();
        if ($customer->getId()) {
            $result['error']   = 0;
            $result['info']    = $this->__("Customer: %s", $customer->getFirstname() . ' ' . $customer->getLastname());
        } else {
            $result['error']   = 1;
            $result['message'] = $this->__("Customer with that email does not exist");
        }

        $this->getResponse()->setBody( Mage::helper('core')->jsonEncode($result) );

        return $result;
    }

    /**
     * Log Points Accruals
     */
    public function logAction()
    {
        $this->_initCustomerAccount();
        $this->renderLayout();
    }

    /**
     * Adding freinds to the database via the form
     */
    public function addfriendsAction()
    {
        $customerId = (int) $this->_getSession()->getId();
        if ($customerId) {
            $emails = $this->getRequest()->getParam('invite-textarea-to');
            $emails = str_replace(' ', '', $emails);
            $emails = explode(',', $emails);
            // remove \n
            foreach ($emails AS $i => $email) {
                $emails[$i] = trim($email);
            }

            $names  = $emails;
            $count  = Mage::getModel('referralreward/friends')->saveFriends($customerId, $emails, $names);
            $this->_createReturnMessage($count);
        }

        $this->_redirect('referralreward/customer/');
    }

    /**
     * Adding freinds to the database via the import /importer.php
     */
    public function savefriendsAction()
    {
        $customerId  = (int)$this->_getSession()->getId();
        if ($customerId) {
            $friends = htmlspecialchars($this->getRequest()->getParam('data'));
            $friends = explode('||', $friends);
            $names   = array();
            $emails  = array();
            foreach ($friends AS $friend) {
                $friend   = explode('|', $friend);
                $names[]  = trim($friend[0]);
                $emails[] = trim($friend[1]);
            }

            $count = Mage::getModel('referralreward/friends')->saveFriends($customerId, $emails, $names);
            $this->_createReturnMessage($count);
        }
    }

    protected function _createReturnMessage($count)
    {
        if ($count['enable']) {
            if ($count['enable'] > 1) {
                $this->_getSession()->addSuccess($this->__('%s have been added', $count['enable']));
            } else {
                $this->_getSession()->addSuccess($this->__('%s has been added', $count['enable']));
            }
        }

        if ($count['disable']) {
            $this->_getSession()->addSuccess($this->__('%s already registered', $count['disable']));
        }

        if ($count['enable'] == 0 && $count['disable'] == 0) {
            $this->_getSession()->addError($this->__("no emails were added"));
        }
    }

    /**
     * Saving customer's referral link
     */
    public function savelinkAction()
    {
        $customerId     = (int) $this->_getSession()->getId();
        if ($customerId) {
            $renameLink = strtolower(trim(htmlspecialchars($this->getRequest()->getParam('renamelink'))));
            $points     = Mage::getModel('referralreward/points')->getItemByUrl($renameLink);
            $result     = array();
            if ($points->getId()) {
                $result['error']       = 1;
                $result['message']     = $this->__("This referral link already exists");
            } else {
                $result['error']       = 0;
                $result['message']     = '';
                if (4 > strlen($renameLink) || 14 < strlen($renameLink)) {
                    $result['error']   = 1;
                    $result['message'] = $this->__("Links should contain 4 - 14 symbols");
                }

                if (!preg_match("/(^[a-z0-9_\-]+)$/", $renameLink)) {
                    $result['error']   = 1;
                    $result['message'] = $this->__("The link should start with a letter. Allowed symbols - A-Z, 0-9, '-', '_'");
                }

                if ($result['error'] == 0) {
                    try {
                        Mage::getModel('referralreward/points')->saveInviteLink($customerId, $renameLink);
                        $this->_getSession()->addSuccess($this->__("Your personal invite link has been successfully changed"));
                    } catch (Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                }
            }

            $this->getResponse()->setBody( Mage::helper('core')->jsonEncode($result) );
        } else {
            $this->_redirect('customer/account/');
        }
    }

    /**
     * Removing email list only for a current customer
     */
    public function removefriendsAction()
    {
        $emails     = $this->getRequest()->getParam('email');
        $customerId = (int)$this->_getSession()->getId();
        if ($customerId) {
            try {
                $count = Mage::getModel('referralreward/friends')->removeFriends($customerId, $emails);
                if ($count > 1) {
                    $this->_getSession()->addSuccess($this->__("%s emails have been deleted", $count));
                } else {
                    $this->_getSession()->addSuccess($this->__("%s email has been deleted", $count));
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
    }
}
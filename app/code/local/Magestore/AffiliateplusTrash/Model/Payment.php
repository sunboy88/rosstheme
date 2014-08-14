<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusTrash
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Override affiliate plus payment
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusTrash
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusTrash_Model_Payment extends Magestore_Affiliateplus_Model_Payment
{
    /**
     * Add delete payement method for payment Model
     * 
     * @return Magestore_AffiliateplusTrash_Model_Payment
     */
    public function deletePayment() {
        if ($this->canRestore()) return $this;
        if (!$this->getId()) return $this;
        
        $this->setData('payment_is_deleted', 1)->save();
        // Return commission to affiliate account
        if ($this->getData('is_reduced_balance') && !$this->getData('is_refund_balance')) {
            $account = $this->getAffiliateplusAccount();
            if ($account && $account->getId()) {
                try {
                    $account->setBalance($account->getBalance() + $this->getAmount() + $this->getTaxAmount())
                        ->setTotalPaid($account->getTotalPaid() - $this->getAmount() - $this->getTaxAmount());
                    $commissionReceived = $this->getAmount();
                    if (!$this->getIsPayerFee()) {
                        $commissionReceived -= $this->getFee();
                    }
                    $account->setTotalCommissionReceived($account->getTotalCommissionReceived() - $commissionReceived)
                        ->save();
                } catch (Exception $e) {
                }
            }
        }
        $this->addComment(Mage::helper('affiliateplustrash')->__('Delete Withdrawal'));
        
        return $this;
    }
    
    /**
     * Restore deleted payment
     * 
     * @return Magestore_AffiliateplusTrash_Model_Payment
     */
    public function restorePayment() {
        if (!$this->canRestore()) return $this;
        if (!$this->getId()) return $this;
        
        $this->setData('payment_is_deleted', 0)->save();
        // Return commission to affiliate account
        if ($this->getData('is_reduced_balance') && !$this->getData('is_refund_balance')) {
            $account = $this->getAffiliateplusAccount();
            if ($account && $account->getId()) {
                try {
                    $account->setBalance($account->getBalance() - $this->getAmount() - $this->getTaxAmount())
                        ->setTotalPaid($account->getTotalPaid() + $this->getAmount() + $this->getTaxAmount());
                    $commissionReceived = $this->getAmount();
                    if (!$this->getIsPayerFee()) {
                        $commissionReceived -= $this->getFee();
                    }
                    $account->setTotalCommissionReceived($account->getTotalCommissionReceived() + $commissionReceived)
                        ->save();
                } catch (Exception $e) {
                }
            }
        }
        $this->addComment(Mage::helper('affiliateplustrash')->__('Restore Withdrawal'));
        
        return $this;
    }
    
    /**
     * override load function for frontend view
     * 
     * @param type $id
     * @param type $field
     * @return Magestore_AffiliateplusTrash_Model_Payment
     */
    public function load($id, $field=null) {
        parent::load($id, $field);
        if (!Mage::app()->getStore()->isAdmin() && $this->canRestore()) {
            $this->setData(array());
        }
        return $this;
    }
}

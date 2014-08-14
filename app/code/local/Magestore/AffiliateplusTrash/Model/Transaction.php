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
 * Override affiliate plus transaction
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusTrash
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusTrash_Model_Transaction extends Magestore_Affiliateplus_Model_Transaction
{
    /**
     * Add delete transaction method for Transaction Model
     * 
     * @return Magestore_Affiliateplus_Model_Transaction
     */
    public function deleteTransaction() {
        if ($this->canRestore()) return $this;
        if (!$this->getId()) return $this;
        
        if ($this->getStatus() == '1') {
            // Remove commission for affiliate account
    		$account = Mage::getModel('affiliateplus/account')
	    		->setStoreId($this->getStoreId())
                ->setBalanceIsGlobal((Mage::getStoreConfig('affiliateplus/account/balance', $this->getStoreId()) == 'global'))
	    		->load($this->getAccountId());
            $commission = $this->getCommission() + $this->getCommissionPlus() + $this->getCommission() * $this->getPercentPlus() / 100;
            Mage::dispatchEvent('affiliateplus_adminhtml_prepare_commission', array('transaction' => $this));
            if ($this->getRealTotalCommission()) {
                $totalCommission = $this->getRealTotalCommission();
            } else {
                $totalCommission = $commission;
            }
            if ($account->getBalance() < $totalCommission) {
                throw new Exception(Mage::helper('affiliateplus')->__('Account not enough balance to delete'));
            }
            $account = Mage::getModel('affiliateplus/account')
                ->setStoreId($this->getStoreId())
                ->load($this->getAccountId());
            $account->setBalance($account->getBalance() - $commission)
                ->save();

            //update balance tier affiliate
            Mage::dispatchEvent('affiliateplus_cancel_transaction',array('transaction' => $this));
        }
        $this->setData('transaction_is_deleted', 1)->save();
        return $this;
    }
    
    /**
     * Restore deleted transaction
     * 
     * @return Magestore_Affiliateplus_Model_Transaction
     */
    public function restoreTransaction() {
        if (!$this->canRestore()) return $this;
        if (!$this->getId()) return $this;
        
        if ($this->getStatus() == '1') {
            // Add commission for affiliate account
    		$account = Mage::getModel('affiliateplus/account')
	    		->setStoreId($this->getStoreId())
	    		->load($this->getAccountId());
            $commission = $this->getCommission() + $this->getCommissionPlus() + $this->getCommission() * $this->getPercentPlus() / 100;
            $account->setBalance($account->getBalance() + $commission)->save();
            
            // update balance tier affiliate
            Mage::dispatchEvent('affiliateplus_complete_transaction',array('transaction' => $this));
        }
        $this->setData('transaction_is_deleted', 0)->save();
        return $this;
    }
}

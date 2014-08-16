<?php

class Magestore_Affiliatepluspayment_Model_Bankaccount extends Mage_Core_Model_Abstract
{
    public function _construct(){
        parent::_construct();
        $this->_init('affiliatepluspayment/bankaccount');
    }
    
    public function format($isHtml = true){
    	if ($isHtml){
    		$html = Mage::helper('affiliatepluspayment')->__('Bank: %s',$this->getName()).'<br />';
    		$html .= Mage::helper('affiliatepluspayment')->__('Account: %s',$this->getAccountName()).'<br />';
    		$html .= Mage::helper('affiliatepluspayment')->__('Acc Number: %s',$this->getAccountNumber()).'<br />';
    		if ($this->getRoutingCode())
    			$html .= Mage::helper('affiliatepluspayment')->__('Routing Code: %s',$this->getRoutingCode()).'<br />';
                if ($this->getSwiftCode())
    			$html .= Mage::helper('affiliatepluspayment')->__('SWIFT Code: %s',$this->getSwiftCode()).'<br />';
    		if ($this->getAddress())
    			$html .= Mage::helper('affiliatepluspayment')->__('Bank Address: %s',$this->getAddress()).'<br />';
            /*if ($this->getBankStatement())
    			$html .= Mage::helper('affiliatepluspayment')->__('Bank Statement <br /> %s',$this->getBankStatement()).'<br />';*/
    		return $html;
    	}
    	return sprintf('%s, %s, %s',$this->getAccountName(),$this->getAccountNumber(),$this->getName());
    }
    
    public function getBankStatement(){
        if($this->getId() ){
            $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($this->getAccountId(), $this->getId(), 'bank');
            return $verify->getInfo();
        }
        return ;
    }
    
    public function getAccount(){
        return Mage::getSingleton('affiliateplus/session')->getAccount();
    }

    public function getBankAccounts($account){
    	if (!$account) return null;
    	$collection = $this->getCollection()
    		->addFieldToFilter('account_id',$account->getId());
    	return $collection;
    }
    
    public function validate(){
    	$errors = array();
    	
    	if (!$this->getName())
    		$errors[] = Mage::helper('affiliatepluspayment')->__('Bank name is empty.');
    	if (!$this->getAccountName())
    		$errors[] = Mage::helper('affiliatepluspayment')->__('Bank account name is empty.');
    	if (!$this->getAccountNumber())
    		$errors[] = Mage::helper('affiliatepluspayment')->__('Bank account number is empty.');
    	
    	if (count($errors) == 0)
    		return false;
    	return $errors;
    }
}
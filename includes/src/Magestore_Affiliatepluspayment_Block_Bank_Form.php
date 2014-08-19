<?php
class Magestore_Affiliatepluspayment_Block_Bank_Form extends Magestore_Affiliateplus_Block_Payment_Form
{
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('affiliatepluspayment/bank/form.phtml');
		return $this;
    }
    
    /*public function getFee(){
    	return Mage::getStoreConfig('affiliateplus_payment/bank/fee_value');
    }
    
    public function getFeeFormated(){
    	$fee = $this->getFee();
    	if (Mage::getStoreConfig('affiliateplus_payment/bank/fee_type') == 'percentage'){
    		return sprintf("%.2f",$fee).'%';
    	}else {
    		return Mage::app()->getStore()->getBaseCurrency()->format($fee);
    	}
    }*/
    
    public function bankAccountIsVerified(){
        $bankAccountId = $this->getBankAccountId();
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $verifyCollection = Mage::getModel('affiliateplus/payment_verify')
            ->getCollection()
            ->addFieldToFilter('account_id',$account->getId())
            ->addFieldToFilter('payment_method','bank')
            ->addFieldToFilter('field',$bankAccountId)
            ->addFieldToFilter('verified','1')
            ;
        if($verifyCollection->getSize())
            return true;
        return false;
    }
    
    protected function _getSession(){
		return Mage::getSingleton('affiliateplus/session');
	}
	
    public function getAccount(){
    	return $this->_getSession()->getAccount();
    }
    
    public function hasBankAccount(){
    	return $this->getBankAccounts()->getSize();
    }
    
    public function getPostData(){
        $data = Mage::app()->getRequest()->getParams();
        return $data;
    }
    
    public function getBank(){
        $bank = Mage::getModel('affiliatepluspayment/bankaccount');
        $data = $this->getPostData();
        if($this->isShowForm() && isset($data['bank']))
            $bank->setData($data['bank']);
        return $bank;
    }
    
    public function getBankAccounts(){
    	if (!$this->hasData('bank_accounts')){
            $bankAccounts = Mage::getModel('affiliatepluspayment/bankaccount')
    			->getBankAccounts($this->getAccount());
            $this->setData('bank_accounts',$bankAccounts);
    	}
    	return $this->getData('bank_accounts');
    }
    
    public function isShowForm(){
        $data = $this->getPostData();
        if(isset($data['payment_bankaccount_id']))
            if(!$data['payment_bankaccount_id'])
                return true;
        return false;
    }
    
    public function getBankAccountHtmlSelect($type){
        $data = $this->getPostData();
        if ($this->hasBankAccount()){
            $options = array();
            foreach ($this->getBankAccounts() as $bankAccount) {
                $options[] = array(
                    'value' => $bankAccount->getId(),
                    'label'	=> $bankAccount->format(false)
                );
                $bankAccountId = $bankAccount->getId();
            }
           
            if(isset($data['payment_bankaccount_id']))
                $bankAccountId = $data['payment_bankaccount_id'];
            //Zend_Debug::dump($bankAccountId);
            if($bankAccountId)
                $this->setBankAccountId($bankAccountId);
            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_bankaccount_id')
                ->setId($type.'-bank-select')
                ->setClass('bank-select')
                ->setExtraParams('onchange=lsRequestTrialNewAccount(this.value);')
                ->setValue($bankAccountId)
                ->setOptions($options);

            $select->addOption('', Mage::helper('checkout')->__('New Bank Account'));

            return $select->getHtml();
        }
        return '';
    }
}
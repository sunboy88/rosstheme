<?php
class Magestore_Affiliatepluspayment_Block_Adminhtml_System_Config_Paypal_Loaddefault extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		$this->setElement($element);
		return $this->_toHtml();
	}
    
    public function getValue(){
        $value = Mage::getStoreConfig('affiliateplus_payment/paypal/sandbox_mode');
        return $value;
    }
    
    protected function _toHtml(){
        $value = $this->getValue();
        $select = 'selected="selected"';
        $selectYes = '';
        $selectNo = '';
        if ($value)
            $selectYes = $select;
        else
            $selectNo = $select;
        
        $default = Mage::getStoreConfig('affiliateplus_payment/paypal/user_mechant_email_default');
        $style = '';
        if($default) $style = '$("row_affiliateplus_payment_paypal_paypal_email").style.display = "none";
                            $("row_affiliateplus_payment_paypal_api_username").style.display = "none";
                            $("row_affiliateplus_payment_paypal_api_password").style.display = "none";
                            $("row_affiliateplus_payment_paypal_api_signature").style.display = "none";
                            $("row_affiliateplus_payment_paypal_sandbox_mode").style.display = "none";';
        return '<select id="affiliateplus_payment_paypal_sandbox_mode" name="groups[paypal][fields][sandbox_mode][value]" class=" select">
                <option value="1" ' . $selectYes . '>Yes</option>
                <option value="0" ' . $selectNo . '>No</option>
                </select>
                <script type="text/javascript">
                    '.$style.'
                </script>';
    }

        /**
	 * Constructor for block 
	 * 
	 */
	public function __construct(){
		parent::__construct();		
	}
}
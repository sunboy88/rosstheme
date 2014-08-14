<?php

class Magestore_Affiliatepluspayment_Block_Adminhtml_System_Config_Paypal_Default extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->_toHtml();
    }

    public function getValue() {
        $value = Mage::getStoreConfig('affiliateplus_payment/paypal/user_mechant_email_default');
        return $value;
    }

    protected function _toHtml() {
        $value = $this->getValue();
        $select = 'selected="selected"';
        $selectYes = '';
        $selectNo = '';
        if ($value)
            $selectYes = $select;
        else
            $selectNo = $select;
        return '<select id="affiliateplus_payment_paypal_user_mechant_email_default" name="groups[paypal][fields][user_mechant_email_default][value]" onchange="changeValuePaypal()" class=" select">
                <option value="1" ' . $selectYes . '>Yes</option>
                <option value="0" ' . $selectNo . '>No</option>
                </select>
                <script type="text/javascript">
                    function changeValuePaypal(){
                        if($("affiliateplus_payment_paypal_user_mechant_email_default").value == "1"){
                            $("row_affiliateplus_payment_paypal_paypal_email").style.display = "none";
                            $("row_affiliateplus_payment_paypal_api_username").style.display = "none";
                            $("row_affiliateplus_payment_paypal_api_password").style.display = "none";
                            $("row_affiliateplus_payment_paypal_api_signature").style.display = "none";
                            $("row_affiliateplus_payment_paypal_sandbox_mode").style.display = "none";
                        }else{
                            $("row_affiliateplus_payment_paypal_paypal_email").style.display = "";
                            $("row_affiliateplus_payment_paypal_api_username").style.display = "";
                            $("row_affiliateplus_payment_paypal_api_password").style.display = "";
                            $("row_affiliateplus_payment_paypal_api_signature").style.display = "";
                            $("row_affiliateplus_payment_paypal_sandbox_mode").style.display = "";
                        }
                    }
                </script>';
    }

    /**
     * Constructor for block 
     * 
     */
    public function __construct() {
        parent::__construct();
    }

}
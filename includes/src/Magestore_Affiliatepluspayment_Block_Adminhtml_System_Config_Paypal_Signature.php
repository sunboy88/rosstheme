<?php
class Magestore_Affiliatepluspayment_Block_Adminhtml_System_Config_Paypal_Signature extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		$this->setElement($element);
		return $this->_toHtml();
	}
    
    public function getValue(){
        $value = Mage::getStoreConfig('affiliateplus_payment/paypal/api_signature');
        return $value;
    }
    
    protected function _toHtml(){
        $value = $this->getValue();
       
        return '<input id="affiliateplus_payment_paypal_api_signature" name="groups[paypal][fields][api_signature][value]" value="'.$value.'" class=" input-text" type="text">
         
        <div style="width:329px; margin-top:7px;">
            <button style="float:left;"id="" type="button" class="" onclick="credentials()">
                <span>Get Credentials from PayPal</span>
            </button>
            <button style="float:right;" id="" type="button" class="" onclick="sandbox()">
                <span>Sandbox Credentials</span>
            </button>
        </div>
        <script type="text/javascript">
             function sandbox(){
                window.open(\'https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_login-api-run\', \'apiwizard\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, ,left=100, top=100, width=380, height=470\'); return false;
             }
             function credentials(){
                window.open(\'https://www.paypal.com/us/cgi-bin/webscr?cmd=_login-api-run\', \'apiwizard\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, ,left=100, top=100, width=380, height=470\'); return false;
             }
         </script>
        ';
    }

        /**
	 * Constructor for block 
	 * 
	 */
	public function __construct(){
		parent::__construct();		
	}
}
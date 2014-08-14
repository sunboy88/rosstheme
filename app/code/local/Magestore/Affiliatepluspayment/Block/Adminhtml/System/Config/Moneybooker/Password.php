<?php
class Magestore_Affiliatepluspayment_Block_Adminhtml_System_Config_Moneybooker_Password extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		$this->setElement($element);
		return $this->_toHtml();
	}
    
    public function getValue($store = 0){
        $value = Mage::getStoreConfig('affiliateplus_payment/moneybooker/moneybooker_password',$store);
        return $value;
    }
    
    protected function _toHtml(){
        $value = $this->getValue();
        $store = $this->getRequest()->getParam('store');
        $storeId = Mage::app()->getStore($store)->getId();
        $valueStore = $this->getValue($storeId);
        $disabled = '';
        $url = $this->getUrl('affiliatepluspayment/adminhtml_payment/verifyMoneybooker');
        if($storeId && ($value == $valueStore)) $disabled = 'disabled';
        return '<input onchange="changeValue()" id="affiliateplus_payment_moneybooker_moneybooker_password" name="groups[moneybooker][fields][moneybooker_password][value]" value="'.$valueStore.'" class=" input-text" type="password" '.$disabled.'>
            <p>
                <div id="button-verify-moneybooker-check">
                    <button id="btn-not-verified" onclick="verify();return false;"><span><span><span></span>Check Moneybooker Account</span></span></button>
                </div>
            </p>
            <script type="text/javascript">
                function verify(){
                    var url = "'.$url.'";
                    var use_default = $("affiliateplus_payment_moneybooker_user_mechant_email_default").value;
                    if(use_default)
                        url += "?default="+use_default;
                    var email = $("affiliateplus_payment_moneybooker_moneybooker_email").value;
                    if(email)
                        url += "&email="+email;
                    var password = $("affiliateplus_payment_moneybooker_moneybooker_password").value;
                    if(password)
                        url += "&password="+password;
                    var subject = $("affiliateplus_payment_moneybooker_notification_subject").value;
                    if(subject)
                        url += "&subject="+subject;
                    var note = $("affiliateplus_payment_moneybooker_notification_note").value;
                    if(note)
                        url += "&note="+note;
                    
                    var request = new Ajax.Request(url,{
                        onSuccess: function(response){
                            if(response.responseText == 1){
                                alert("Moneybooker account is valid.");
                            }else{
                                alert(response.responseText);
                            }
                        }
                    });
                }
                function changeValue(){
                    $("btn-not-verified").style.display = "";
                    $("link-verified").style.display = "none";
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
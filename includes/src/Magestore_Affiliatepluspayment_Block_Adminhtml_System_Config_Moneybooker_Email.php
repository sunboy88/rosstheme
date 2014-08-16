<?php
class Magestore_Affiliatepluspayment_Block_Adminhtml_System_Config_Moneybooker_Email extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		$this->setElement($element);
		return $this->_toHtml();
	}
    
    public function getValue($store = 0){
        $value = Mage::getStoreConfig('affiliateplus_payment/moneybooker/moneybooker_email',$store);
        return $value;
    }
    
    protected function _toHtml(){
        $value = $this->getValue();
        $store = $this->getRequest()->getParam('store');
        $storeId = Mage::app()->getStore($store)->getId();
        $default = Mage::getStoreConfig('affiliateplus_payment/moneybooker/user_mechant_email_default');
        $defaultStore = Mage::getStoreConfig('affiliateplus_payment/moneybooker/user_mechant_email_default',$storeId);
        
        $valueStore = $this->getValue($storeId);
        $style = '';
        $display = '';
        $disabled = '';
        $checked = '';
        if($defaultStore) $display = 'none';
        if($default == $defaultStore) $checked = 'checked';
        if($storeId && ($value == $valueStore)) $disabled = 'disabled';
        if($default) $style = '$("row_affiliateplus_payment_moneybooker_moneybooker_email").style.display = "none"';
        return '<input id="affiliateplus_payment_moneybooker_moneybooker_email" name="groups[moneybooker][fields][moneybooker_email][value]" value="'.$valueStore.'" class=" input-text" '.$disabled.' type="text">
                <script type="text/javascript">
                    $("row_affiliateplus_payment_moneybooker_moneybooker_email").style.display="'.$display.'";
                    if($("affiliateplus_payment_moneybooker_user_mechant_email_default_inherit"))
                        $("affiliateplus_payment_moneybooker_user_mechant_email_default_inherit").checked = "'.$checked.'"
                    '.$style.'
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
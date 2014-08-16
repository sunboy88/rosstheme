<?php
class Magestore_Affiliatepluspayment_Block_Adminhtml_System_Config_Moneybooker_Default extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		$this->setElement($element);
		return $this->_toHtml();
	}
    
    public function getValue($store = 0){
        $value = Mage::getStoreConfig('affiliateplus_payment/moneybooker/user_mechant_email_default',$store);
        return $value;
    }
    
    protected function _toHtml(){
        $value = $this->getValue();
        $select = 'selected="selected"';
        
        $store = $this->getRequest()->getParam('store');
        $store = Mage::app()->getStore($store)->getId();
        $valueStore = $this->getValue($store);
        $selectYes = '';
        $selectNo = '';
        if($valueStore)
            $selectYes = $select;
        else $selectNo = $select;
        $disabled = '';
        if($store && ($value == $valueStore)) $disabled = 'disabled';
        return '<select id="affiliateplus_payment_moneybooker_user_mechant_email_default" name="groups[moneybooker][fields][user_mechant_email_default][value]" onchange="changeValueSelect()" '.$disabled.' class=" select">
                <option value="1" '.$selectYes.'>Yes</option>
                <option value="0" '.$selectNo.'>No</option>
                </select>
                <script type="text/javascript">
                    function changeValueSelect(){
                        if($("affiliateplus_payment_moneybooker_user_mechant_email_default").value == "1"){
                            if($("row_affiliateplus_payment_moneybooker_moneybooker_email"))
                                $("row_affiliateplus_payment_moneybooker_moneybooker_email").style.display = "none";
                        }else{
                            if($("row_affiliateplus_payment_moneybooker_moneybooker_email"))
                                $("row_affiliateplus_payment_moneybooker_moneybooker_email").style.display = "";
                        }
                    }
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
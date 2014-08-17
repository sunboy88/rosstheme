<?php

class Magestore_Onestepcheckout_Block_Adminhtml_System_Config_Field_Position extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $storecode = Mage::app()->getRequest()->getParam('store');
        $website = Mage::app()->getRequest()->getParam('website');
        $helper = Mage::helper('onestepcheckout');
        
        if ($storecode) {
            $store = $helper->getStoreByCode($storecode);
            $scope = 'stores';
            $scopeId = (int) Mage::getConfig()->getNode('stores/' . $storecode . '/system/store/id');
        } elseif ($website) {
            $scope = 'websites';
            $scopeId = (int) Mage::getConfig()->getNode('websites/' . $website . '/system/website/id');
        } else {
            $scope = 'default';
            $scopeId = 0;
        }

        $html = $this->_getHeaderHtml($element);
        $checkNull = 1;
        
        for ($i = 0; $i < 20; $i++) {
            // var_dump($helper->getFieldEnable($i));
            if ($helper->getDefaultField($i) && $helper->getDefaultField($i) != '0') {
                $checkNull = 0;
                break;
            }
        }
        if ($checkNull == 1) {
            $arrayDefaults = $helper->getDefaultPositionArray();
            foreach($arrayDefaults as $number => $value){
                $model = Mage::getModel('onestepcheckout/config');
                $model->setScope('default')
                    ->setScopeId(0)
                    ->setPath('onestepcheckout/field_position_management/row_'.$number)
                    ->setValue($value);
                $model->save();
            }
        }
        $html .= '<div class="user_guide">Configure positions of fields in Section Billing and Shipping Address. You can display fields into 2 columns (eg: First name + Last name) or 1 full column (eg: Address + Null)</div>';

        $fieldArrays = array();
        for ($i = 0; $i < 20; $i++) {
            $fieldArrays[] = 'onestepcheckout_field_position_management_row_' . $i;
            $html .= $this->_getFieldHtml($element, $i, $scope, $scopeId);
        }

        $html .='
				<style type="text/css">
					#onestepcheckout_field_position_management_position{
						display:none;
					}
					#onestepcheckout_field_position_management .collapseable{
						display:none;
					}
					.user_guide{
						background:none repeat scroll 0 0 #EAF0EE;
						border:1px dotted #FF0000;
						margin-bottom: 20px;
						padding: 20px;
					}
				</style>
				<script type="text/javascript">
					var previous;
					function forcus(field)
					{
						previous = field.value;
					}
					function checkfield(field){
						for (var k=0; k<20; k++){
							if((field.value == $("onestepcheckout_field_position_management_row_"+k).value)
								&& (field.id != "onestepcheckout_field_position_management_row_"+k)
									&&(field.value!="0")
								){
								field.value = previous;
								alert("This field already exists!");
								break;
							}
						}
					}
                    function changeValueSelect(){
                        if($("affiliateplus_payment_moneybooker_user_mechant_email_default").value == "1"){
                            if($("row_affiliateplus_payment_moneybooker_moneybooker_email"))
                                $("row_affiliateplus_payment_moneybooker_moneybooker_email").style.display = "none";
                        }else{
                            if($("row_affiliateplus_payment_moneybooker_moneybooker_email"))
                                $("row_affiliateplus_payment_moneybooker_moneybooker_email").style.display = "";
                        }
                    }
					function checkValueRequire(){
						var firstnameRequire = "1";
						var lastnameRequire = "1";
						var emailRequire = "1";
						var message = "In Field Position Management\n\n";
						for (var k=0; k<20; k++){
							if($("onestepcheckout_field_position_management_row_"+k).value == "firstname"){
								firstnameRequire = "0";
							}
							if($("onestepcheckout_field_position_management_row_"+k).value == "lastname"){
								lastnameRequire = "0";
							}
							if($("onestepcheckout_field_position_management_row_"+k).value == "email"){
								emailRequire = "0";
							}
						}
						if(firstnameRequire=="1" || lastnameRequire=="1" || emailRequire=="1"){
							if(firstnameRequire=="1")
								message += "The First Name field is missing!\n";
							if(lastnameRequire=="1")
								message += "The Last Name field is missing!\n";
							if(emailRequire=="1")
								message += "The Email field is missing!\n";
							message += "\n\n Please select the position for them!";
							alert(message);
						}else{
							configForm.submit();
						}
					}
                </script>';

        return $html;
    }

    protected function _getDummyElement() {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Varien_Object(array('show_in_default' => 1, 'show_in_website' => 1));
        }
        return $this->_dummyElement;
    }

    protected function _getFieldRenderer() {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }

    protected function _showAllOption() {
        return array(
            '0' => Mage::helper('onestepcheckout')->__('Null'),
            'firstname' => Mage::helper('onestepcheckout')->__('First Name'),
            'lastname' => Mage::helper('onestepcheckout')->__('Last Name'),
            'prefix' => Mage::helper('onestepcheckout')->__('Prefix Name'),
            'middlename' => Mage::helper('onestepcheckout')->__('Middle Name'),
            'suffix' => Mage::helper('onestepcheckout')->__('Suffix Name'),
            'email' => Mage::helper('onestepcheckout')->__('Email Address'),
            'company' => Mage::helper('onestepcheckout')->__('Company'),
            'street' => Mage::helper('onestepcheckout')->__('Address'),
            'country' => Mage::helper('onestepcheckout')->__('Country'),
            'region' => Mage::helper('onestepcheckout')->__('State/Province'),
            'city' => Mage::helper('onestepcheckout')->__('City'),
            'postcode' => Mage::helper('onestepcheckout')->__('Zip/Postal Code'),
            'telephone' => Mage::helper('onestepcheckout')->__('Telephone'),
            'fax' => Mage::helper('onestepcheckout')->__('Fax'),
            'birthday' => Mage::helper('onestepcheckout')->__('Date of Birth'),
            'gender' => Mage::helper('onestepcheckout')->__('Gender'),
            'taxvat' => Mage::helper('onestepcheckout')->__('Tax/VAT number'),
        );
    }

    protected function _optionToHtml($option, $selected) {
        $html = '<option value="' . $option["key"] . '"';
        $html.= isset($option['value']) ? 'title="' . $option['value'] . '"' : '';
        //$html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
        if ($option['key'] == $selected) {
            $html.= ' selected="selected"';
        }
        $html.= '>' . $option['value'] . '</option>' . "\n";
        return $html;
    }

    protected function _getFieldHtml($fieldset, $number, $scope, $scopeId) {
        $configData = $this->getConfigData();
        $defaultLabel = Mage::helper('onestepcheckout')->__('Use Default');
        $defaultTitle = Mage::helper('onestepcheckout')->__('-- Please Select --');
        $scopeLabel = Mage::helper('onestepcheckout')->__('STORE VIEW');
        $path = 'onestepcheckout/field_position_management/row_' . $number;
        $helper = Mage::helper('onestepcheckout');
        $data = $helper->getFieldEnableBackEnd($number, $scope, $scopeId); //isset($helper->getFieldEnable($number)) ? $helper->getFieldEnable($number) : '';
        $e = $this->_getDummyElement();
        $html = '';
        if($number % 2 == 0){
            $html.= '<tr>';
        }
        $active = true;
        if($scope != 'default' && count($data)==0)
            $active = false;
        $html .= '<td class="value">';
        $html .= '<select style="width: 280px;margin-left:30px;" onfocus="forcus(this);" onchange="checkfield(this);" id="onestepcheckout_field_position_management_row_' . $number . '" name="groups[field_position_management][fields][row_' . $number . '][value]" ' . ($active ? '' : 'disabled=""') . ' class="select">';
        $allOptions = $this->_showAllOption();
        foreach ($allOptions as $key => $value) {
            $option['value'] = $value;
            $option['key'] = $key;
            if(count($data)){
                $selected = $data;
            }else{
                $selected = $helper->getDefaultField($number);
            }
            $html.= $this->_optionToHtml($option, $selected);
        }
        $html.= '</select></td>';
        if ($scope != 'default') {
            $html .= '<td class="use-default">
			<input id="onestepcheckout_field_position_management_row_' . $number . '_inherit" name="groups[field_position_management][fields][row_' . $number . '][inherit]" type="checkbox" value="1" class="checkbox config-inherit" ' . (count($data) ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="onestepcheckout_field_position_management_row_' . $number . '_inherit" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td>';
        } else {
            $html .= '<td class="scope-label">[' . $scopeLabel . ']</td>';
        }
        if ($number % 2 != 0 || $number == 19) {
            $html .= '</tr>';
        }
        return $html;
    }

}
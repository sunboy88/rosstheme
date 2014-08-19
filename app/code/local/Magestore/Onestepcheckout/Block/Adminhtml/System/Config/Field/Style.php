<?php

class Magestore_Onestepcheckout_Block_Adminhtml_System_Config_Field_Style extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $helper = Mage::helper('onestepcheckout');
        $storecode = Mage::app()->getRequest()->getParam('store');
        $website = Mage::app()->getRequest()->getParam('website');
        
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
        
        $path = 'onestepcheckout/style_management/style';
        $style = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope',$scope)
            ->addFieldToFilter('path', $path)
            ->addFieldToFilter('scope_id', $scopeId)
            ->getFirstItem()
            ->getValue();
        
        $html = $this->_getHeaderHtml($element);
        $html .= '<div style="margin-top: 10px; font-weight: bold; border-bottom: 1px solid rgb(223, 223, 223);"> '.Mage::helper('onestepcheckout')->__('Style Color').'</div>';
        $fieldArrays = array();
        $fieldArrays[] = 'onestepcheckout_style_management_style';
        $html .= '</table>';
        $html .= $this->_getFieldHtml($element, $scope, $scopeId);

        if (!$style) {
            $style = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope','default')
            ->addFieldToFilter('path', $path)
            ->addFieldToFilter('scope_id', 0)
            ->getFirstItem()
            ->getValue();
        }
        $customPath = 'onestepcheckout/style_management/custom';
        $custom = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope',$scope)
            ->addFieldToFilter('path', $customPath)
            ->addFieldToFilter('scope_id', $scopeId)
            ->getFirstItem()
            ->getValue();
        
        if (!$custom) {
            $custom = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope','default')
            ->addFieldToFilter('path', $customPath)
            ->addFieldToFilter('scope_id', 0)
            ->getFirstItem()
            ->getValue();
        }
        if($style=='custom'){
            $html .='<div id="showreview">                    
                    <input value="'.$custom.'" style="background-color: #'.$custom.';margin-left: 36px;" name="groups[style_management][fields][style][custom]" id="onestepcheckout_style_management_custom" onclick="loadColor_onestepcheckout(\'click\')"/>
                    </div>';
                        
        }else{
            $html .='<div id="showreview">                
                     <img width="957px" src="' . Mage::getBlockSingleton('core/template')->getSkinUrl('images/onestepcheckout/style/') . $style . '.png" />
                     </div>';
        }
        $html .= '
                    <style type="text/css">
                        #onestepcheckout_style_management .collapseable{
                            display:none;
                        }
                    </style>
                    <script type="text/javascript">
                        loadColor_onestepcheckout(\'click\');
                        function showreview(style)
                        {
                            if(style.value=="custom"){
                                var show = "<input value=\"'.$custom.'\" style=\"background-color: #'.$custom.';margin-left: 36px;\" name=\"groups[style_management][fields][style][custom]\" id=\"onestepcheckout_style_management_custom\" onclick=\"loadColor_onestepcheckout(\'click\')\"/>";
                                $("showreview").innerHTML  = show;
                            }else{
                                var show = "<img width=\"957px\" src=\"' . Mage::getBlockSingleton('core/template')->getSkinUrl('images/onestepcheckout/style/') . '";
                                show +=style.value+".png\" />";
                                $("showreview").innerHTML=show;
                            }
                        }
                    </script>
		';
        
        /*Checkout button - Michael20140609*/
        $buttonPath = 'onestepcheckout/style_management/button';
        $buttonStyle = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope',$scope)
            ->addFieldToFilter('path', $buttonPath)
            ->addFieldToFilter('scope_id', $scopeId)
            ->getFirstItem()
            ->getValue();
                
        $html .= '<div style="margin-top: 10px; font-weight: bold; border-bottom: 1px solid rgb(223, 223, 223);"> '.Mage::helper('onestepcheckout')->__('"Place Order Now" button Color').'</div>';        
        $fieldArrays = array();
        $fieldArrays[] = 'onestepcheckout_style_management_button';        
        $html .= $this->_getButtonHtml($element, $scope, $scopeId);
        if (!$buttonStyle) {
            $buttonStyle = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope','default')
            ->addFieldToFilter('path', $buttonPath)
            ->addFieldToFilter('scope_id', 0)
            ->getFirstItem()
            ->getValue();
        }
        $customButtonPath = 'onestepcheckout/style_management/custombutton';
        $customButton = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope',$scope)
            ->addFieldToFilter('path', $customButtonPath)
            ->addFieldToFilter('scope_id', $scopeId)
            ->getFirstItem()
            ->getValue();
        
        if (!$customButton) {
            $customButton = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope','default')
            ->addFieldToFilter('path', $customButtonPath)
            ->addFieldToFilter('scope_id', 0)
            ->getFirstItem()
            ->getValue();
        }
        if($buttonStyle=='custom'){
            $html .='<div id="showreviewbutton">                    
                    <input value="'.$customButton.'" style="background-color: #'.$customButton.';margin-left: 36px;" name="groups[style_management][fields][button][custombutton]" id="onestepcheckout_style_management_custombutton" onclick="loadColor_onestepcheckoutbutton(\'click\')"/>
                    </div>';
                        
        }else{
            $html .='<div id="showreviewbutton">                                     
                     </div>';
        }
        $html .= '                    
                    <script type="text/javascript">
                        loadColor_onestepcheckoutbutton(\'click\');
                        function showreviewbutton(style)
                        {
                            if(style.value=="custom"){
                                var show = "<input value=\"'.$customButton.'\" style=\"background-color: #'.$customButton.';margin-left: 36px;\" name=\"groups[style_management][fields][button][custombutton]\" id=\"onestepcheckout_style_management_custombutton\" onclick=\"loadColor_onestepcheckoutbutton(\'click\')\"/>";
                                $("showreviewbutton").innerHTML  = show;
                            }else{
                                $("showreviewbutton").innerHTML="";
                            }
                        }
                    </script>
		';
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
            'orange' => Mage::helper('onestepcheckout')->__('Orange'),
            'green' => Mage::helper('onestepcheckout')->__('Green'),
            'black' => Mage::helper('onestepcheckout')->__('Black'),
            'blue' => Mage::helper('onestepcheckout')->__('Blue'),
            'darkblue' => Mage::helper('onestepcheckout')->__('Dark Blue'),
            'pink' => Mage::helper('onestepcheckout')->__('Pink'),
            'red' => Mage::helper('onestepcheckout')->__('Red'),
            'violet' => Mage::helper('onestepcheckout')->__('Violet'),
        );
    }

    protected function _optionToHtml($option, $selected) {
        $html = '<option value="' . $option["key"] . '"';
        $html.= isset($option['value']) ? 'title="' . $option['value'] . '"' : '';
        if ($option['key'] == $selected) {
            $html.= ' selected="selected"';
        }
        $html.= '>' . $option['value'] . '</option>' . "\n";
        return $html;
    }

    protected function _getFieldHtml($fieldset, $scope, $scopeId) {
        
        $defaultLabel = Mage::helper('onestepcheckout')->__('Use Default');
        $defaultTitle = Mage::helper('onestepcheckout')->__('-- Please Select --');
        $scopeLabel = Mage::helper('onestepcheckout')->__('STORE VIEW');
        $helper = Mage::helper('onestepcheckout');
        $path = 'onestepcheckout/style_management/style';
        $data = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope',$scope)
            ->addFieldToFilter('path', $path)
            ->addFieldToFilter('scope_id', $scopeId)
            ->getFirstItem()
            ->getValue();
        
        $active = true;
        if ($scope != 'default' && count($data) == 0)
            $active = false;

        if (!$data) {
            $data = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope','default')
            ->addFieldToFilter('path', $path)
            ->addFieldToFilter('scope_id', 0)
            ->getFirstItem()
            ->getValue();
        }
        
        $html = '';
        $html .= '<table class="form-list" cell-spacing="0"><tr><td class="value">';
        $html .= '<select style="width: 280px;margin-left:30px;" onchange="showreview(this);" id="onestepcheckout_style_management_style" name="groups[style_management][fields][style][value]" ' . ($active ? '' : 'disabled=""') . ' class="select">';
        $allOptions = $this->_showAllOption();
        foreach ($allOptions as $key => $value) {
            $option['value'] = $value;
            $option['key'] = $key;
            $selected = $data;
            $html.= $this->_optionToHtml($option, $selected);
        }
        //custom style - Michael 20140609
        $option['value'] = Mage::helper('onestepcheckout')->__('Custom');
        $option['key'] = 'custom';
        $selected = $data;
        //custom style end
        $html.= $this->_optionToHtml($option, $selected);
        $html.= '</select></td>';
        if ($scope != 'default') {
            $html .= '<td class="use-default">
            <input id="onestepcheckout_style_management_style" name="groups[style_management][fields][style][inherit]" type="checkbox" value="1" class="checkbox config-inherit" ' . ($active ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="onestepcheckout_style_management_style" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label></td>';
        } else {
            $html .= '<td class="scope-label">[' . $scopeLabel . ']</td>';
        }
        $html .= '</tr></table>';
        return $html;
    }
    
    protected function _getButtonHtml($fieldset, $scope, $scopeId) {
        
        $defaultLabel = Mage::helper('onestepcheckout')->__('Use Default');
        $defaultTitle = Mage::helper('onestepcheckout')->__('-- Please Select --');
        $scopeLabel = Mage::helper('onestepcheckout')->__('STORE VIEW');
        $helper = Mage::helper('onestepcheckout');
        $path = 'onestepcheckout/style_management/button';
        $data = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope',$scope)
            ->addFieldToFilter('path', $path)
            ->addFieldToFilter('scope_id', $scopeId)
            ->getFirstItem()
            ->getValue();
        
        $active = true;
        if ($scope != 'default' && count($data) == 0)
            $active = false;

        if (!$data) {
            $data = Mage::getModel('onestepcheckout/config')->getCollection()
            ->addFieldToFilter('scope','default')
            ->addFieldToFilter('path', $path)
            ->addFieldToFilter('scope_id', 0)
            ->getFirstItem()
            ->getValue();
        }
        
        $html = '';
        $html .= '<table class="form-list" cell-spacing="0"><tr><td class="value">';
        $html .= '<select style="width: 280px;margin-left:30px;" onchange="showreviewbutton(this);" id="onestepcheckout_style_management_button" name="groups[style_management][fields][button][value]" ' . ($active ? '' : 'disabled=""') . ' class="select">';
        $allOptions = $this->_showAllOption();
        foreach ($allOptions as $key => $value) {
            $option['value'] = $value;
            $option['key'] = $key;
            $selected = $data;
            $html.= $this->_optionToHtml($option, $selected);
        }
        //custom style - Michael 20140609
        $option['value'] = Mage::helper('onestepcheckout')->__('Custom');
        $option['key'] = 'custom';
        $selected = $data;
        //custom style end
        $html.= $this->_optionToHtml($option, $selected);
        $html.= '</select></td>';
        if ($scope != 'default') {
            $html .= '<td class="use-default">
            <input id="onestepcheckout_style_management_button" name="groups[style_management][fields][button][inherit]" type="checkbox" value="1" class="checkbox config-inherit" ' . ($active ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="onestepcheckout_style_management_button" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label></td>';
        } else {
            $html .= '<td class="scope-label">[' . $scopeLabel . ']</td>';
        }
        $html .= '</tr></table>';
        return $html;
    }

}
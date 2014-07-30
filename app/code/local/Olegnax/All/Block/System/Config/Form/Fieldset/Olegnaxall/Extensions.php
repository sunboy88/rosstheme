<?php

class Olegnax_All_Block_System_Config_Form_Fieldset_Olegnaxall_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		sort($modules);

        foreach ($modules as $moduleName) {
        	if (strpos($moduleName,'Olegnax_') === false || $moduleName == 'Olegnax_All') continue;
        	$html.= $this->_getFieldHtml($element, $moduleName);
        }
		
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

	protected function _getFieldHtml($fieldset, $moduleName)
    {
    	$ver = Mage::getConfig()->getModuleConfig($moduleName)->version;		
		if($ver){
			$field = $fieldset->addField($moduleName, 'label',
				array(
					'name' => $moduleName,
					'label' => $moduleName,
					'value' => $ver,
				))
				->setRenderer( Mage::getBlockSingleton('adminhtml/system_config_form_field') );
			return $field->toHtml();
		}
		return '';
    }
}
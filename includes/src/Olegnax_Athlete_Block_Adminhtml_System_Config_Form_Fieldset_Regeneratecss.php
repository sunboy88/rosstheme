<?php

class Olegnax_Athlete_Block_Adminhtml_System_Config_Form_Fieldset_Regeneratecss extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
        $r = Mage::app()->getRequest();
		return $this->getLayout()->createBlock('adminhtml/widget_button')
			->setType('button')
			->setLabel('Regenerate CSS')
			->setOnClick("setLocation('".
                $this->getUrl(
                    'athlete/adminhtml_regeneratecss',
                    array(
                        'website' => $r->getParam('website'),
                        'store' => $r->getParam('store'),
	                    'back_url' => Mage::helper('core/url')->getEncodedUrl(),
                    )
                )
            ."')")
			->toHtml();
	}
}

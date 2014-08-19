<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Slider_Wrap
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'',
	            'label' => Mage::helper('athlete')->__('none')),
	        array(
	            'value'=>'first',
	            'label' => Mage::helper('athlete')->__('First')),
	        array(
	            'value'=>'last',
	            'label' => Mage::helper('athlete')->__('Last')),
	        array(
	            'value'=>'both',
	            'label' => Mage::helper('athlete')->__('Both')),
	        array(
	            'value'=>'circular',
	            'label' => Mage::helper('athlete')->__('Circular')),
        );
    }

}

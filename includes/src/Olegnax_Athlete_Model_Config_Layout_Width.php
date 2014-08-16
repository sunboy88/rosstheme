<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Layout_Width
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'1024',
	            'label' => Mage::helper('athlete')->__('1024')),
	        array(
	            'value'=>'1280',
	            'label' => Mage::helper('athlete')->__('1280')),
	        array(
	            'value'=>'1360',
	            'label' => Mage::helper('athlete')->__('1360')),
	        array(
	            'value'=>'1440',
	            'label' => Mage::helper('athlete')->__('1440')),
	        array(
	            'value'=>'1680',
	            'label' => Mage::helper('athlete')->__('1680')),
	        array(
	            'value'=>'custom',
	            'label' => Mage::helper('athlete')->__('Custom width')),
        );
    }

}

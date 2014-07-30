<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Listing_Productrow
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'2',
	            'label' => Mage::helper('athlete')->__('2')),
	        array(
	            'value'=>'3',
	            'label' => Mage::helper('athlete')->__('3')),
	        array(
	            'value'=>'4',
	            'label' => Mage::helper('athlete')->__('4')),
	        array(
	            'value'=>'5',
	            'label' => Mage::helper('athlete')->__('5')),
	        array(
	            'value'=>'6',
	            'label' => Mage::helper('athlete')->__('6')),
	        array(
	            'value'=>'7',
	            'label' => Mage::helper('athlete')->__('7')),
        );
    }

}

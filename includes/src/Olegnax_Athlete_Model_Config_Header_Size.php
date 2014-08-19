<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Header_Size
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'full-header',
	            'label' => Mage::helper('athlete')->__('full header')),
	        array(
	            'value'=>'resized-header',
	            'label' => Mage::helper('athlete')->__('resized header')),
        );
    }

}
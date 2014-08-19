<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Brands_Pages
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'1',
	            'label' => Mage::helper('athlete')->__('Home page only')),
	        array(
	            'value'=>'2',
	            'label' => Mage::helper('athlete')->__('All pages')),
        );
    }

}

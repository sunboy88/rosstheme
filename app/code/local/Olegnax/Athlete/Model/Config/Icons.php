<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Icons
{

    public function toOptionArray()
    {
        return array(
	        array('value'=>'black', 'label' => Mage::helper('athlete')->__('black')),
	        array('value'=>'white', 'label' => Mage::helper('athlete')->__('white')),
        );
    }

}
<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Zoom_Position
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'right',
	            'label' => Mage::helper('athlete')->__('Right')),
            array(
	            'value'=>'inside',
	            'label' => Mage::helper('athlete')->__('Inside')),
        );
    }

}

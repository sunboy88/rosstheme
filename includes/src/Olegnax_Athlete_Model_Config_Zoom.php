<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Zoom
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'default',
	            'label' => Mage::helper('athlete')->__('Magento Default')),
            array(
	            'value'=>'cloudzoom',
	            'label' => Mage::helper('athlete')->__('CloudZoom')),
            array(
	            'value'=>'lightbox',
	            'label' => Mage::helper('athlete')->__('Lightbox')),
        );
    }

}

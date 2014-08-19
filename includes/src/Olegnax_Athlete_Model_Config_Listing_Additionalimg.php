<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Listing_Additionalimg
{

	public function toOptionArray()
	{
		return array(
			array(
				'value' => 'label',
				'label' => Mage::helper('athlete')->__('Image Label')),
			array(
				'value' => 'position',
				'label' => Mage::helper('athlete')->__('Sort Order')),
		);
	}

}

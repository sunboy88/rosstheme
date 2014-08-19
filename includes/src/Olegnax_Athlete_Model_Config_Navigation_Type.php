<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Navigation_Type
{

	public function toOptionArray()
	{
		$options = array();
		$options[] = array(
			'value' => 'athlete',
			'label' => 'Athlete navigation',
		);
		$options[] = array(
			'value' => 'wide',
			'label' => 'Wide navigation',
		);
		$options[] = array(
			'value' => 'default',
			'label' => 'Default navigation',
		);

		return $options;
	}

}

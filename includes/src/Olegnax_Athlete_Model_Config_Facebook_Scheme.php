<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Facebook_Scheme
{

	public function toOptionArray()
	{
		$options = array();
		$options[] = array(
			'value' => 'dark',
			'label' => 'dark',
		);
		$options[] = array(
			'value' => 'light',
			'label' => 'light',
		);

		return $options;
	}

}

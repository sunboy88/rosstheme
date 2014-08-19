<?php
/**
 * @version   1.1 30.05.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_System_Config_Save extends Mage_Core_Model_Abstract
{
	/**
	 * @param Varien_Event_Observer $observer
	 */
	public function regenerateCss($observer)
	{
		//$website = $observer->getEvent()->getWebsite();
		//$store = $observer->getEvent()->getStore();
		$section = $observer->getEvent()->getSection();

		if (0 === strpos($section, 'athlete')) {
			Mage::getSingleton('athlete/css')->regenerate();
		}

	}

}

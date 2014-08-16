<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isSlideshowEnabled()
	{
		$config = Mage::getStoreConfig('athleteslideshow', Mage::app()->getStore()->getId());
		$request = Mage::app()->getFrontController()->getRequest();
		$route = Mage::app()->getFrontController()->getRequest()->getRouteName();
		$action = Mage::app()->getFrontController()->getRequest()->getActionName();
		$show = false;
		if ($config['config']['enabled']) {
			$show = true;
			if ($config['config']['show'] == 'home') {
				$show = false;
				if ($request->getModuleName() == 'cms' && $request->getControllerName() == 'index' && $request->getActionName() == 'index') {
					$show = true;
				}
			}
			if ($show && ($route == 'customer' && ($action == 'login' || $action == 'forgotpassword' || $action == 'create'))) {
				$show = false;
			}
		}
		return $show;
	}

	public function getSupportedColors()
	{
		return array(
			array(
				'value'     => 'white',
				'label'     => Mage::helper('athleteslideshow')->__('white'),
			),
			array(
				'value'     => 'black',
				'label'     => Mage::helper('athleteslideshow')->__('black'),
			),
			array(
				'value'     => 'red',
				'label'     => Mage::helper('athleteslideshow')->__('red'),
			),
			array(
				'value'     => 'green',
				'label'     => Mage::helper('athleteslideshow')->__('green'),
			),
			array(
				'value'     => 'blue',
				'label'     => Mage::helper('athleteslideshow')->__('blue'),
			),
			array(
				'value'     => 'yellow',
				'label'     => Mage::helper('athleteslideshow')->__('yellow'),
			),
		);
	}
}
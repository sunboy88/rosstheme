<?php
/**
 * @version   1.1 30.05.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Settings extends Mage_Core_Model_Abstract
{
	/**
	 * data path
	 * @var string
	 */
	private $_data_path;

	public function _construct()
	{
		$this->_data_path = Mage::getBaseDir() . '/app/code/local/Olegnax/Athlete/etc/cms/';
	}

	/**
	 * restore config settings
	 *
	 * @param array $stores - stores to update settings
	 * @param boolean $clear - clear old settings
	 * @param object $items - xml with settings
	 * @param string $path
	 */
	public function restoreSettings($stores, $clear, $items, $path = '')
	{
		if ( !empty($path) ) {
			$path .= '/';
		}
		$allWebsites = Mage::app()->getWebsites();
		$allStores = Mage::app()->getStores();
		foreach ($items as $item) {
			if ($item->hasChildren()) {
				$this->restoreSettings($stores, $clear, $item->children(), $path . $item->getName());
			} else {
				if ($clear) {
					Mage::getConfig()->deleteConfig($path . $item->getName());
					foreach ($allWebsites as $website) {
						Mage::getConfig()->deleteConfig($path . $item->getName(), 'websites', $website->getId());
					}
					foreach ($allStores as $store) {
						Mage::getConfig()->deleteConfig($path . $item->getName(), 'stores', $store->getId());
					}
				}
				foreach ($stores as $store) {
					$scope = ($store ? 'stores' : 'default');
					Mage::getConfig()->saveConfig($path . $item->getName(), (string)$item, $scope, $store);
				}
			}
		}
		Mage::getConfig()->reinit();
	}

	/**
	 * restore cms pages / blocks
	 *
	 * @param string mode
	 * @param string data type
	 * @param bool overwrite items
	 */
	public function restoreCmsData($model, $dataType, $overwrite = false)
	{
		try {
			$cmsFile = $this->_data_path . $dataType . '.xml';
			if (!is_readable($cmsFile)) {
				throw new Exception(
					Mage::helper('athlete')->__("Can't read data file: %s", $cmsFile)
				);
			}
			$cmsData = new Varien_Simplexml_Config($cmsFile);

			foreach ($cmsData->getNode($dataType)->children() as $item) {

				$currentData = Mage::getModel($model)->getCollection()
					->addFieldToFilter('identifier', $item->identifier)
					->load();
				if ($overwrite) {
					if (count($currentData)) {
						foreach ($currentData as $_item) {
							$_item->delete();
						}
					}
				} else {
					if (count($currentData)) {
						continue;
					}
				}

				$_model = Mage::getModel($model)
					->setTitle($item->title)
					->setIdentifier($item->identifier)
					->setContent($item->content)
					->setIsActive($item->is_active)
					->setStores(array(0));
				if ( $dataType == 'pages' ) {
					$_model->setRootTemplate($item->root_template);
				}
				$_model->save();
			}
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::logException($e);
		}
	}
}

<?php
/**
 * @version   1.1 30.05.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Css extends Mage_Core_Model_Abstract
{
	/**
	 * css file name
	 * @var string
	 */
	private $_css_file;

	/**
	 * css path
	 * @var string
	 */
	private $_css_path;

	/**
	 * css template path
	 * @var string
	 */
	private $_css_template_path;

	public function _construct()
	{
		$this->_css_file = 'options_%WEBSITE%_%STORE%.css';
		$this->_css_path = Mage::getBaseDir() . '/skin/frontend/athlete/default/css/';
		$this->_css_template_path = Mage::getBaseDir() . '/app/code/local/Olegnax/Athlete/css/css.php';
	}

	/**
	 * regenerate theme css based on appearance settings
	 */
	public function regenerate()
	{
		$websites = Mage::app()->getWebsites();
		foreach ($websites as $_website) {
			$_website_code = $_website->getCode();
			foreach ($_website->getStores() as $_store) {
				if (!Mage::app()->getStore($_store)->getIsActive()) continue;
				ob_start();
				require($this->_css_template_path);
				$css = ob_get_clean();
				$filename = str_replace(
					array('%WEBSITE%', '%STORE%'),
					array($_website_code, $_store->getCode()),
					$this->_css_file
				);
				try {
					$file = new Varien_Io_File();
					$file->setAllowCreateFolders(true)
						->open(array('path' => $this->_css_path));
					$file->streamOpen($filename, 'w+');
					$file->streamWrite($css);
					$file->streamClose();
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('athlete')->__('Css generation error: %s', $this->_css_path.$filename) . '<br/>' . $e->getMessage());
					Mage::logException($e);
				}
			}
		}
	}

}

<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Adminhtml_RestoreController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')
			->isAllowed('olegnax/athlete/restore');
	}

	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('olegnax/athlete/restore')
			->_addBreadcrumb(Mage::helper('athlete')->__('Restore Defaults'), Mage::helper('athlete')->__('Restore Defaults'));

		return $this;
	}

	public function indexAction()
	{
		$this->_initAction();
		$this->_title($this->__('Olegnax'))
			->_title($this->__('Athlete'))
			->_title($this->__('Restore Defaults'));

		$this->_addContent($this->getLayout()->createBlock('athlete/adminhtml_restore_edit'));
		$block = $this->getLayout()->createBlock('core/text', 'restore-desc')
			->setText('<b>Theme default settings :</b>
                        <br/><br/>
                        <b>Appearance</b>
                        <ul>
                            <li>ATTENTION: All colors will be restored to default scheme. Do not restore if you do not want to loose your changes</li>
                        </ul>');
		$this->_addLeft($block);

		$this->renderLayout();
	}

	public function restoreAction()
	{
		$stores = $this->getRequest()->getParam('stores', array(0));
		$clear = $this->getRequest()->getParam('clear_scope', false);
		$restore_settings = $this->getRequest()->getParam('restore_settings', 0);

		$restore_pages = $this->getRequest()->getParam('restore_pages', 0);
		$overwrite_pages = $this->getRequest()->getParam('overwrite_pages', 0);
		$restore_blocks = $this->getRequest()->getParam('restore_blocks', 0);
		$overwrite_blocks = $this->getRequest()->getParam('overwrite_blocks', 0);

		if ($clear) {
			if (!in_array(0, $stores))
				$stores[] = 0;
		}

		try {
			if ($restore_settings) {
				$defaults = new Varien_Simplexml_Config(Mage::getBaseDir() . '/app/code/local/Olegnax/Athlete/etc/config.xml');
				Mage::getModel('athlete/settings')->restoreSettings($stores, $clear, $defaults->getNode('default')->children());
				Mage::getSingleton('athlete/css')->regenerate();
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('athlete')->__('Default Settings has been restored. <br/>Please clear cache (System > Cache management) if you do not see changes in storefront')
				);
			}

			if ($restore_pages) {
				Mage::getModel('athlete/settings')->restoreCmsData('cms/page', 'pages', $overwrite_pages);
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('athlete')->__('Default Pages has been restored.')
				);
			}
			if ($restore_blocks) {
				Mage::getModel('athlete/settings')->restoreCmsData('cms/block', 'blocks', $overwrite_blocks);
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('athlete')->__('Default Blocks has been restored.')
				);
			}

		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('athlete')->__('An error occurred while
			restoring defaults.'));
		}

		$this->getResponse()->setRedirect($this->getUrl("*/*/"));
	}

}
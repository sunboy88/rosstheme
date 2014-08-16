<?php

class Olegnax_Colorswatches_IndexController extends Mage_Core_Controller_Front_Action
{

	/**
	 * a copy of helper function without visibility check
	 * @see Mage_Catalog_Helper_Product for original function
	 *
	 * @param $productId
	 * @param $controller
	 * @param null $params
	 * @return bool
	 */
	protected function _initProduct($productId, $controller, $params = null)
	{
		// Prepare data for routine
		if (!$params) {
			$params = new Varien_Object();
		}

		// Init and load product
		Mage::dispatchEvent('catalog_controller_product_init_before', array(
			'controller_action' => $controller,
			'params' => $params,
		));

		if (!$productId) {
			return false;
		}

		$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId);

		if (!in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())) {
			return false;
		}

		// Register current data and dispatch final events
		Mage::register('current_product', $product);
		Mage::register('product', $product);

		try {
			Mage::dispatchEvent('catalog_controller_product_init', array('product' => $product));
			Mage::dispatchEvent('catalog_controller_product_init_after',
				array('product' => $product,
					'controller_action' => $controller
				)
			);
		} catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			return false;
		}

		return $product;
	}

	public function imagesAction()
	{
		$categoryId = (int) $this->getRequest()->getParam('category', false);
		$productId  = (int) $this->getRequest()->getParam('id');

		$params = new Varien_Object();
		$params->setCategoryId($categoryId);

		if (!$product = $this->_initProduct($productId, $this, $params)) {
			if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
				$this->_redirect('');
			} elseif (!$this->getResponse()->isRedirect()) {
				$this->_forward('noRoute');
			}
			return;
		}
		$this->loadLayout();
		$this->renderLayout();
	}

}
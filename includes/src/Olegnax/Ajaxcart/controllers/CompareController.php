<?php
class Olegnax_Ajaxcart_CompareController extends Mage_Core_Controller_Front_Action
{

	public function compareAction(){
		$response = array();

		if ($productId = (int) $this->getRequest()->getParam('product')) {
			$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId);

			if ($product->getId()/* && !$product->isSuper()*/) {
				Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
				$response['status'] = 'SUCCESS';
				$response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
				Mage::helper('catalog/product_compare')->calculate();
				Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
				$this->loadLayout();
				$response['sidebar'] = $this->getLayout()->getBlock('catalog.compare.sidebar')
					->setTemplate('catalog/product/compare/sidebar.phtml')
					->toHtml();
			}
		}

		$this->_sendJson($response);
		return;
	}

	/**
	 * send json respond
	 *
	 * @param array $response - response data
	 */
	private function _sendJson( $response )
	{
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback') . '(' . Mage::helper('core')->jsonEncode($response) . ')' );
	}

}
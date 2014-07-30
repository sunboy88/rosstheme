<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Olegnax_Ajaxcart_CartController extends Mage_Checkout_CartController
{

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

	public function addAction()
	{
		$cart = $this->_getCart();
		$params = $this->getRequest()->getParams();
		if ($params['isAjax'] == 1) {
			$response = array();
			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
						array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = $this->_initProduct();
				$related = $this->getRequest()->getParam('related_product');

				/**
				 * Check product availability
				 */
				if (!$product) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Unable to find Product ID');
					$this->_sendJson($response);
					return;
				}

				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}

				$cart->save();
				$this->_getSession()->setCartWasUpdated(true);

				Mage::dispatchEvent('checkout_cart_add_product_complete',
					array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);

				if (!$this->_getSession()->getNoCartRedirect(true)) {
					if (!$cart->getQuote()->getHasError()){
						$message = $this->__( $this->__('%s was added to your shopping cart.') .'<br/><br/> <button class="button" onclick="setLocation(\''.Mage::getUrl('checkout/cart').'\');"><span><span>'. $this->__('View Cart') .'</span></span></button><button class="button" onclick="setLocation(\''.Mage::helper('checkout/url')->getCheckoutUrl().'\');"><span><span>'. $this->__('Checkout') .'</span></span></button>', Mage::helper('core')->escapeHtml($product->getName()));
						$response['status'] = 'SUCCESS';
						$response['message'] = $message;

						$this->loadLayout();

						$links = $this->getLayout()->getBlock('top.links');
						if ( $links) {
							$response['toplink'] = $links->toHtml();
						}
						$sidebar = $this->getLayout()->getBlock('cart_top');
						if ( $sidebar) {
							$response['cart_top'] = $sidebar->toHtml();
						}
					}else {
						$response['status'] = 'ERROR';
						foreach ($cart->getQuote()->getErrors() as $_error) {
							$response['message'] .= $_error->toString();
						}
					}
				}
			} catch (Mage_Core_Exception $e) {
				$msg = "";
				if ($this->_getSession()->getUseNotice(true)) {
					$msg = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$msg .= $message . '<br/>';
					}
				}

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot add the item to shopping cart.');
				Mage::logException($e);
			}
			$this->_sendJson($response);
			return;
		} else {
			return parent::addAction();
		}
	}

	public function optionsAction()
	{
		$productId = $this->getRequest()->getParam('id');
		if (empty($productId)) {
			$path = base64_decode($this->getRequest()->getParam('path'));
			$oRewrite = Mage::getModel('core/url_rewrite')
				->setStoreId(Mage::app()->getStore()->getId())
				->loadByRequestPath($path);
			$productId = $oRewrite->getProductId();
		}
		// Prepare helper and params
		$viewHelper = Mage::helper('catalog/product_view');

		$params = new Varien_Object();
		$params->setCategoryId(false);
		$params->setSpecifyOptions(false);

		// Render page
		try {
			$viewHelper->prepareAndRender($productId, $this, $params);
		} catch (Exception $e) {
			if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
				if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				Mage::logException($e);
				$this->_forward('noRoute');
			}
		}
	}

	/**
	 * Update product configuration for a cart item
	 */
	public function updateItemOptionsAction()
	{
		$cart   = $this->_getCart();
		$id = (int) $this->getRequest()->getParam('id');
		$params = $this->getRequest()->getParams();

		if (!isset($params['options'])) {
			$params['options'] = array();
		}

		if ($params['isAjax'] == 1) {
			$response = array('params' => $params);

			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
						array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$quoteItem = $cart->getQuote()->getItemById($id);
				if (!$quoteItem) {
					$this->_sendJson(array(
						'status' => 'ERROR',
						'message' => $this->__('Quote item is not found.'),
					));
					return;
				}

				$item = $cart->updateItem($id, new Varien_Object($params));
				if (is_string($item)) {
					$this->_sendJson(array(
						'status' => 'ERROR',
						'message' => $item,
					));
					return;
				}
				if ($item->getHasError()) {
					Mage::throwException($item->getMessage());
					$this->_sendJson(array(
						'status' => 'ERROR',
						'message' => $item->getMessage(),
					));
					return;
				}

				$related = $this->getRequest()->getParam('related_product');
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}

				$cart->save();

				$this->_getSession()->setCartWasUpdated(true);

				Mage::dispatchEvent('checkout_cart_update_item_complete',
					array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);
				if (!$this->_getSession()->getNoCartRedirect(true)) {
					if (!$cart->getQuote()->getHasError()){
						$response['status'] = 'SUCCESS';
						$response['message'] = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
						$this->loadLayout();
						$links = $this->getLayout()->getBlock('top.links');
						if ( $links) {
							$response['toplink'] = $links->toHtml();
						}
						$sidebar = $this->getLayout()->getBlock('cart_top');
						if ( $sidebar) {
							$response['cart_top'] = $sidebar->toHtml();
						}
					}
				}
			} catch (Mage_Core_Exception $e) {
				$msg = "";
				if ($this->_getSession()->getUseNotice(true)) {
					$msg = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$msg .= $message . '<br/>';
					}
				}

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot update the item.');
				Mage::logException($e);
			}
			$this->_sendJson($response);
			return;
		} else {
			return parent::updateItemOptionsAction();
		}

	}

}
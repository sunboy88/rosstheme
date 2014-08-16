<?php
class Magestore_Affiliatepluswidget_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * get Account helper
	 *
	 * @return Magestore_Affiliateplus_Helper_Account
	 */
	protected function _getAccountHelper(){
		return Mage::helper('affiliateplus/account');
	}
	
	/**
	 * get Core Session
	 *
	 * @return Mage_Core_Model_Session
	 */
	protected function _getCoreSession(){
		return Mage::getSingleton('core/session');
	}
	
    public function indexAction(){
        if (!Mage::helper('affiliatepluswidget')->getConfig('enable')) {
            return $this->_redirect('affiliateplus/index/index');
        }
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	if ($this->_getAccountHelper()->accountNotLogin())
    		return $this->_redirect('affiliateplus/account/login');
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('My Widgets'));
		$this->renderLayout();
    }
    
    public function newAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	$this->_forward('edit');
    }
    
    public function editAction(){
        if (!Mage::helper('affiliatepluswidget')->getConfig('enable')) {
            return $this->_redirect('affiliateplus/index/index');
        }
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	if ($this->_getAccountHelper()->accountNotLogin())
    		return $this->_redirect('affiliateplus/account/login');
    	$widgetId = $this->getRequest()->getParam('id');
    	$widget = Mage::getModel('affiliatepluswidget/widget')->load($widgetId);
    	if ($widget->getId() && $widget->getAccountId() != $this->_getAccountHelper()->getAccount()->getId()){
    		$this->_getCoreSession()->addError($this->__('Widget not found'));
    		return $this->_redirect('*/*/');
    	}
    	Mage::register('widget_model',$widget);
    	$this->loadLayout();
    	if ($widget->getId())
    		$this->getLayout()->getBlock('head')->setTitle($widget->getName());
    	else 
    		$this->getLayout()->getBlock('head')->setTitle($this->__('New Widget'));
    	$this->renderLayout();
    }
    
    public function saveAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	if ($this->_getAccountHelper()->accountNotLogin())
    		return $this->_redirect('affiliateplus/account/login');
    	if ($this->getRequest()->isPost()){
    		$id = $this->getRequest()->getParam('id');
    		$widget = Mage::getModel('affiliatepluswidget/widget')->load($id);
    		if ($widget->getId() && $widget->getAccountId() != $this->_getAccountHelper()->getAccount()->getId()){
    			$this->_getCoreSession()->addError($this->__('Widget not found'));
    		}else {
    			$data = $this->getRequest()->getPost();
    			$data['is_image'] = !empty($data['is_image']) ? 1 : 0;
    			$data['is_price'] = !empty($data['is_price']) ? 1 : 0;
    			$data['is_rated'] = !empty($data['is_rated']) ? 1 : 0;
    			$data['is_short_desc'] = !empty($data['is_short_desc']) ? 1 : 0;
    			$widget->setData($data)
    				->setAccountId($this->_getAccountHelper()->getAccount()->getId())
    				->setId($id);
    			try {
    				$widget->save();
    				$this->_getCoreSession()->addSuccess($this->__('Widget has been saved successfully'));
    			}catch (Exception $e){
    				$this->_getCoreSession()->addError($e->getMessage());
    			}
    		}
    	}else 
    		$this->_getCoreSession()->addError($this->__('Widget not found'));
    	return $this->_redirect('*/*/');
    }
    
    public function deleteAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	if ($this->_getAccountHelper()->accountNotLogin())
    		return $this->_redirect('affiliateplus/account/login');
    	$widgetId = $this->getRequest()->getParam('id');
    	$widget = Mage::getModel('affiliatepluswidget/widget')->load($widgetId);
    	if ($widget->getId() && $widget->getAccountId() == $this->_getAccountHelper()->getAccount()->getId()){
    		try {
    			$widget->delete();
	    		$this->_getCoreSession()->addSuccess($this->__('Widget has been deleted successfully'));
    		}catch (Exception $e){
    			$this->_getCoreSession()->addError($e->getMessage());
    		}
    	}else 
    		$this->_getCoreSession()->addError('Widget not found');
    	return $this->_redirect('*/*/');
    }
    
    public function codeAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	if ($this->_getAccountHelper()->accountNotLogin()) return '';
    	$html = Mage::helper('affiliatepluswidget')->__('Copy code below and then paste into your webpage').'<br />';
    	$code = Mage::helper('affiliatepluswidget')->getWidgetCode($this->getRequest()->getParams());
    	$html .= sprintf('<textarea onclick="this.select()" readonly cols="65" rows="13">%s</textarea>',$code);
    	$this->getResponse()->setBody($html);
    }
    
    public function widgetviewAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	$widgetView = $this->getLayout()->createBlock('affiliatepluswidget/widgetview');
    	$this->getResponse()->setBody($widgetView->toHtml());
    }
    
    public function widgetAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	$widgetBlock = $this->getLayout()->createBlock('affiliatepluswidget/widget');
    	$this->getResponse()->setBody($widgetBlock->toHtml());
    }
    
    public function viewAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	$viewBlock = $this->getLayout()->createBlock('affiliatepluswidget/view');
    	$this->getResponse()->setBody($viewBlock->toHtml());
    }
    
    public function productsAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	$productsBlock = $this->getLayout()->createBlock('affiliatepluswidget/products');
    	$this->getResponse()->setBody($productsBlock->toHtml());
    }
    
    public function productAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	$widgetCode = $this->getLayout()->createBlock('affiliatepluswidget/link');
    	$widgetCode->setTemplate('affiliatepluswidget/code.phtml');
    	$this->getResponse()->setBody($widgetCode->toHtml());
    }
}
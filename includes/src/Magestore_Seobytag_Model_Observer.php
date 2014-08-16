<?php

class Magestore_Seobytag_Model_Observer
{
	/**
	 * get seobytag module helper
	 * 
	 * @return Magestore_Seobytag_Helper_Data
	 */
    public function getHelper() {
        return Mage::helper('seobytag');
    }
    
    public function tagProductList($observer){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seobytag')){return;}
    	if (!$this->getHelper()->getConfig('enable')) return $this;
    	$action = $observer->getEvent()->getControllerAction();
    	
    	$tagModel = new Magestore_Seobytag_Model_Tag_Tag();
    	$tagModel->load($action->getRequest()->getParam('tagId'));
    	if (!$tagModel->getId()) return $this;
    	
    	try {
    		$tagModel->generateMetaData()->save();
    		$rewrite = $tagModel->createRewrite();
    		if ($rewrite->getId() && trim($action->getRequest()->getRequestString(),'/') != $rewrite->getRequestPath()){
				$url = Mage::getUrl(null,array('_direct' => $rewrite->getRequestPath()));
				$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
				$action->getResponse()->setRedirect($url);
			}
    	} catch (Exception $e){}
    }
    
    public function cmsIndexNoroute($observer){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seobytag')){return;}
		if (!$this->getHelper()->getConfig('enable')) return $this;
		$action = $observer->getEvent()->getControllerAction();
		$requestPath = trim($action->getRequest()->getRequestString(),'/');
		
		$urlRewrite = Mage::getResourceModel('core/url_rewrite_collection')
			->addFieldToFilter('request_path',$requestPath)
			->addFieldToFilter('id_path',array('like' => 'seobytag/%'))
			->addFieldToFilter('store_id',array('neq' => Mage::app()->getStore()->getId()))
			->getFirstItem();
		$curRewrite = Mage::getResourceModel('core/url_rewrite_collection')
			->addFieldToFilter('request_path',$requestPath)
			->addFieldToFilter('id_path',array('like' => 'seobytag/%'))
			->addFieldToFilter('store_id',Mage::app()->getStore()->getId())
			->getFirstItem();
		if ($urlRewrite && $urlRewrite->getId() && !$curRewrite->getId()){
			$url = Mage::getUrl(null,array('_direct' => $urlRewrite->getTargetPath()));
			$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
			$action->getResponse()->setRedirect($url);
		}
	}
}
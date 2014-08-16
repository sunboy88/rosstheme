<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Adminhtml_ActivateController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')
			->isAllowed('olegnax/athlete/activate');
	}

	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('olegnax/athlete/activate')
			->_addBreadcrumb(Mage::helper('athlete')->__('Activate Athlete Theme'),
				Mage::helper('athlete')->__('Activate Athlete Theme'));

		return $this;
	}

	public function indexAction()
	{
		$this->_initAction();
		$this->_title($this->__('Olegnax'))
			->_title($this->__('Athlete'))
			->_title($this->__('Activate Athlete Theme'));

		$this->_addContent($this->getLayout()->createBlock('athlete/adminhtml_activate_edit'));
		$block = $this->getLayout()->createBlock('core/text', 'activate-desc')
			->setText('<big><b>Activate will update following settings:</b></big>
	                        <br/><br/>
	                        <big>System > Config</big><br/><br/>
	                        <b>Web > Default pages</b>
	                        <ul>
	                            <li>CMS Home Page</li>
	                            <li>CMS No Route Page</li>
	                        </ul>
	                        <b>Design > Package</b>
	                        <ul>
	                            <li>athlete</li>
	                        </ul>
							<b>Design > Themes</b>
	                        <ul>
	                            <li>Default</li>
	                        </ul>
	                        <b>Design > Footer</b>
	                        <ul>
	                            <li>Copyright</li>
	                        </ul>
	                        ');
		$this->_addLeft($block);

		$this->renderLayout();
	}

	public function activateAction()
	{
		$stores = $this->getRequest()->getParam('stores', array(0));
		$setup_cms = $this->getRequest()->getParam('setup_cms', 0);

		try {
			foreach ($stores as $store) {
				$scope = ($store ? 'stores' : 'default');
				//web > default pages
				Mage::getConfig()->saveConfig('web/default/cms_home_page', 'athlete_home', $scope, $store);
				Mage::getConfig()->saveConfig('web/default/cms_no_route', 'athlete_no_route', $scope, $store);
				//design > package
				Mage::getConfig()->saveConfig('design/package/name', 'athlete', $scope, $store);
				//design > themes
				Mage::getConfig()->saveConfig('design/theme/default', 'default', $scope, $store);
				//design > header
				Mage::getConfig()->saveConfig('design/header/logo_src', 'images/athlete/logo.png', $scope, $store);
				//design > footer
				Mage::getConfig()->saveConfig('design/footer/copyright', 'Athlete &copy; 2012 <a href="http://olegnax.com" >Premium Magento Themes</a> by Olegnax ©1999 - 2013. *List price is for reference only. No sales may have occurred at this price. **See product page for details. The products available through our shop may vary from those appearing in our stores.', $scope, $store);
			}


			Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('athlete')->__('Athlete Theme has been activated.<br/>
                Please clear cache (System > Cache management) if you do not see changes in storefront.<br/>
                <b>IMPORTANT !!!. Log out from magento admin panel ( if you logged in ). This step is required to reset magento
                access control cache and avoid 404 message on theme options page</b>
                '));
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('athlete')->__('An error occurred while
			activating theme. ' . $e->getMessage()));
		}

		$this->getResponse()->setRedirect($this->getUrl("*/*/"));
	}
}
<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Geoip
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Geoip Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_Geoip
 * @author      Magestore Developer
 */
 header('Content-Encoding: UTF-8');
 header('Content-Type: text/html; charset=utf-8');
 // header('Content-type: application/csv; charset=UTF-8');
class Magestore_Onestepcheckout_GeoipController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function checkPostcodeAction(){
		$postcode = $this->getRequest()->getPost('postcode');
		$geoips = Mage::getModel('onestepcheckout/geoip')->getCollection()											  
											->setOrder('geoip_id', 'DESC')											 
											->addFieldToFilter('postcode',  array('like'=>$postcode.'%'));
		$items = array();
		if($geoips->getSize()){
			foreach($geoips as $geoip){
				$country = $geoip->getData('country');
        // Zend_Debug::dump($country);die('2');																	
				$region = $geoip->getData('region');
				$city = $geoip->getData('city');	
				$geoipPostcode = $geoip->getData('postcode');
				if($region){
					$directory = Mage::getModel('directory/region')->getCollection()
											  ->addFieldToFilter('country_id', $country)
											  ->addFieldToFilter('code', $region);
					if($directory->getSize()) {
						$region_id = $directory->getFirstItem()->getId();
						$region = $directory->getFirstItem()->getData('default_name');
					}
				}			
				$items[] = array( 'id' => $geoip->getId(),
								'country' => $country,
								'region' => $region,
								'region_id' => $region_id,
								'city' => $city,
								'postcode' => $geoipPostcode,
								);
			}
		}	
		$block = $this->getLayout()->createBlock('onestepcheckout/postcode')
					  ->setTemplate('onestepcheckout/geoip/postcode.phtml')
					  ->assign('items', $items);
        $this->getResponse()->setBody($block->toHtml());
		// $this->getResponse()->setBody(Zend_Json::encode($result));
	}	
	
	public function checkCityAction(){
		$city = $this->getRequest()->getPost('city');
		$geoips = Mage::getModel('onestepcheckout/geoip')->getCollection()
											->setOrder('geoip_id', 'DESC')
											->addFieldToFilter('city',  array('like'=>$city.'%'));											 
		$items = array();
		if($geoips->getSize()){
			foreach($geoips as $geoip){
				$country = $geoip->getData('country');
        // Zend_Debug::dump($country);die('2');																	
				$region = $geoip->getData('region');
				$city = $geoip->getData('city');	
				$geoipPostcode = $geoip->getData('postcode');
				if($region){
					$directory = Mage::getModel('directory/region')->getCollection()
											  ->addFieldToFilter('country_id', $country)
											  ->addFieldToFilter('code', $region);
					if($directory->getSize()) {
						$region_id = $directory->getFirstItem()->getId();
						$region = $directory->getFirstItem()->getData('default_name');
					}
				}			
				$items[] = array( 'id' => $geoip->getId(),
								'country' => $country,
								'region' => $region,
								'region_id' => $region_id,
								'city' => $city,
								'postcode' => $geoipPostcode,
								);
			}
		}	
		$block = $this->getLayout()->createBlock('onestepcheckout/postcode')
					  ->setTemplate('onestepcheckout/geoip/postcode.phtml')
					  ->assign('items', $items);
        $this->getResponse()->setBody($block->toHtml());
		// $this->getResponse()->setBody(Zend_Json::encode($result));
	}	
	
}
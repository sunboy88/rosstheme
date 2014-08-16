<?php
/*
  * Velan Info Services India Pvt Ltd.
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the EULA
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://store.velanapps.com/License.txt
  *
  /***************************************
  *         MAGENTO EDITION USAGE NOTICE *
  * *************************************** */
  /* This package designed for Magento COMMUNITY edition
  * Velan Info Services does not guarantee correct work of this extension
  * on any other Magento edition except Magento COMMUNITY edition.
  * Velan Info Services does not provide extension support in case of
  * incorrect edition usage.
  /***************************************
  *         DISCLAIMER   *
  * *************************************** */
  /* Do not edit or add to this file if you wish to upgrade Magento to newer
  * versions in the future.
  * ****************************************************
  * @category   Velanapps
  * @package    Smartnotifications
  * @author     Velan Team
  * @copyright  Copyright (c) 2013  Velan Info Services India Pvt Ltd. (http://www.velanapps.com)
  * @license    http://store.velanapps.com/License.txt
*/


class Velanapps_Smartnotifications_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	/**
		Function for getting status of Reference Header.
		Input : Being called, no specific input given.
		Output : Returns integer based on the status.
	 */
	public function referenceHeaderStatus(){
		return Mage::getStoreConfig('smartnotifications_tab/reference_header/referenceheader');
	}
	
	 /**
		Function for getting the Script.
		Input : Being called, no specific input given.
		Output : Returns the content given in the script area.
	 */	 
	public function referenceHeaderScript(){
		return Mage::getStoreConfig('smartnotifications_tab/reference_header/script_area');
	}
	
	 /**
		Function for getting the Stylesheet.
		Input : Being called, no specific input given.
		Output : Returns the content given in the style area.
	 */
	public function referenceHeaderStyle(){
		return Mage::getStoreConfig('smartnotifications_tab/reference_header/style');
	}
	
	/**
		Function for getting the Scroller.
		Input : Being called, no specific input given.
		Output : Returns the content given in the Scrolled.
	 */
	public function scrollerStatus(){
		return Mage::getStoreConfig('smartnotifications_tab/bottom_to_top/scroller');
	}
	
	/**
		Function for getting the Scroller Speed.
		Input : Being called, no specific input given.
		Output : Returns the content given in the  Scroller Speed.
	 */
	public function scrollerSpeed(){
		return Mage::getStoreConfig('smartnotifications_tab/bottom_to_top/speed');
	}
	
	/**
		Function for getting the Scroller Position.
		Input : Being called, no specific input given.
		Output : Returns the content given in the  Scroller Position.
	 */ 
	public function scrollerPosition(){
		return Mage::getStoreConfig('smartnotifications_tab/bottom_to_top/position');
	}
	
	/**
		Function for getting the Scroller Image.
		Input : Being called, no specific input given.
		Output : Returns the content given in the  Scroller Image.
	 */
	public function scrollerImage(){
		return Mage::getStoreConfig('smartnotifications_tab/bottom_to_top/image');
	}
	
	/**
		Function for getting the Popup Effect.
		Input : Being called, no specific input given.
		Output : Returns the Specified Popup Effect.
	 */
	public function effect(){
		return Mage::getStoreConfig('smartnotifications_tab/pop_up/effects');
	}
	
	/**
		Function for getting the Popup Content.
		Input : Being called, no specific input given.
		Output : Returns the Popup Content.
	 */
	public function popupContent(){
		return Mage::getStoreConfig('smartnotifications_tab/pop_up/content');
	}
	
	/**
		Function for getting the Popup Show.
		Input : Being called, no specific input given.
		Output : Returns the Show Effect(once/all).
	 */
	public function show(){
		return Mage::getStoreConfig('smartnotifications_tab/pop_up/show');
	}
	
	/**
		Function for getting the Popup Activation.
		Input : Being called, no specific input given.
		Output : Returns the Enable/Disable.
	 */
	public function popupActivation(){
		return Mage::getStoreConfig('smartnotifications_tab/pop_up/popup');
	}
	
	/**
		Function for getting the Header Activation.
		Input : Being called, no specific input given.
		Output : Returns the Enable/Disable.
	 */
	public function headerActive(){
			return Mage::getStoreConfig('smartnotifications_tab/information_bar/informationbar');
	}
	
	/**
		Function for getting the Header/Footer Height.
		Input : Being called, no specific input given.
		Output : Returns the Height.
	 */	
	public function height(){
			return Mage::getStoreConfig('smartnotifications_tab/information_bar/height');
	}
	
	/**
		Function for getting the Header/Footer Position.
		Input : Being called, no specific input given.
		Output : Returns the Header/Footer Position.
	 */	
	public function informationBarPosition(){
			return Mage::getStoreConfig('smartnotifications_tab/information_bar/position');
	}
	
	/**
		Function for getting the Fixed Header Position.
		Input : Being called, no specific input given.
		Output : Returns the Fixed Header Position.
	 */	
	public function informationBarFixedHeader(){
			return Mage::getStoreConfig('smartnotifications_tab/information_bar/fixed');
	}
	
	/**
		Function for getting the Informationbar Content
		Input : Being called, no specific input given.
		Output : Returns the Informationbar Content.
	 */	
	public function informationBarContent(){
			return Mage::getStoreConfig('smartnotifications_tab/information_bar/content');
	}
	
	/**
		Function for getting the Informationbar Pallet
		Input : Being called, no specific input given.
		Output : Returns the Informationbar Pallet.
	 */	
	public function informationBarColorPallet(){
		return Mage::getStoreConfig('smartnotifications_tab/information_bar/color_pallet');
	}
	
	/**
		Function for getting the Informationbar Scroller
		Input : Being called, no specific input given.
		Output : Returns the Informationbar Scroller.
	 */	
	public function informationBarScroll(){
			return Mage::getStoreConfig('smartnotifications_tab/information_bar/scroll');
	}
	
	/**
		Function for getting the Start Date.
		Input : Being called, no specific input given.
		Output : Returns the Start Date.
	 */
	public function informationBarFromDate(){
			return Mage::getStoreConfig('smartnotifications_tab/information_bar/from_date');
	}
	
	/**
		Function for getting the To Date.
		Input : Being called, no specific input given.
		Output : Returns the To Date.
	 */
	public function informationBarToDate(){
			return Mage::getStoreConfig('smartnotifications_tab/information_bar/to_date');
	}
	
	/**
		Function for getting status of Reference Header.
		Input : Being called, no specific input given.
		Output : Returns integer based on the status.
	 */
	public function sideBarStatus(){
		return Mage::getStoreConfig('smartnotifications_tab/sidebar/side');
	}
	
	 /**
		Function for getting the Script.
		Input : Being called, no specific input given.
		Output : Returns the content given in the script area.
	 */	 
	public function sideBarContent(){
		return Mage::getStoreConfig('smartnotifications_tab/sidebar/sidecontent');
	}
	/**
		Function for getting status of Reference Header.
		Input : Being called, no specific input given.
		Output : Returns integer based on the status.
	 */
	public function sideColor(){
		return Mage::getStoreConfig('smartnotifications_tab/sidebar/side_pallet');
	}
	
	 /**
		Function for getting the Script.
		Input : Being called, no specific input given.
		Output : Returns the content given in the script area.
	 */	 
	public function sideBorderColor(){
		return Mage::getStoreConfig('smartnotifications_tab/sidebar/side_border');
	}
	
	
	/**
		Function for getting Magento store base url.
		Input  : Being called, no specific input given.
		Output : Returns Magento store base url.
	*/
	public function getStoreUrl(){
		return Mage::getBaseUrl();
	}
}
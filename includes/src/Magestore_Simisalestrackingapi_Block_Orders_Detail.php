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
 * @package     Magestore_Simisalestrackingapi
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Orders Detail Block
 * 
 * @category    Magestore
 * @package     Magestore_Simisalestrackingapi
 * @author      Magestore Developer
 */
class Magestore_Simisalestrackingapi_Block_Orders_Detail extends Magestore_Simisalestrackingapi_Block_Orders {
    
    protected $_next_index = '';

	protected $_item = '';

    protected $_tab = 'index'; //tab and pages order lists
    
	public function setItem($item){
		$this->_item = $item;
    }
	public function getItem(){
		return $this->_item;
	}
	
	 public function getOrderOptions()
    {
        $result = array();
        if ($this->getItem()->getProductOptions()) {
			$options = $this->getItem()->getProductOptions();
            if (isset($options['option'])) {
                $result = array_merge($result, $options['options']);
            }
			if (isset($options['bundle_options'])) {
                $result = array_merge($result, $options['bundle_options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
		
		
    }

    /**
     * Return custom option html
     *
     * @param array $optionInfo
     * @return string
     */
    public function getCustomizedOptionValue($optionInfo)
    {
        /** 
         * render customized option view
         */
        $_default = $optionInfo['value'];
        if (isset($optionInfo['option_type'])) {
            try {
                $group = Mage::getModel('catalog/product_option')->groupFactory($optionInfo['option_type']);
                return $group->getCustomizedView($optionInfo);
            } catch (Exception $e) {
                return $_default;
            }
        }
        return $_default;
    }

    public function getSku()
    {
        return $this->getItem()->getSku();
    }
}

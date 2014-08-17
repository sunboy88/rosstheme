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
 * Geoip Block
 * 
 * @category    Magestore
 * @package     Magestore_Onestepcheckout
 * @author      Magestore Developer
 */
class Magestore_Onestepcheckout_Block_Postcode extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magestore_Onestepcheckout_Block_Postcode
     */
	 
   protected function _toHtml()
    {
        // Mage::dispatchEvent('adminhtml_block_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
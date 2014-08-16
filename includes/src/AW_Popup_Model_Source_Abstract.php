<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Popup
 * @version    1.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


abstract class AW_Popup_Model_Source_Abstract
{
    abstract public function toOptionArray();

    /**
     * Returns array(value => ..., label => ...) for option with given value
     *
     * @param string $value
     *
     * @return array
     */
    public function getOption($value)
    {
        $_options = $this->toOptionArray();

        foreach ($_options as $_option) {
            if ($_option['value'] == $value) {
                return $_option;
            }
        }

        return false;
    }

    public function toShortOptionArray()
    {
        $_options = array();
        foreach ($this->toOptionArray() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    protected function _getHelper($ext = '')
    {
        return Mage::helper('popup' . ($ext ? '/' . $ext : ''));
    }
}
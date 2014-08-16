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


class AW_Popup_Model_Source_Position extends AW_Popup_Model_Source_Abstract
{
    const TOP_ID = 1;
    const MIDDLE_ID = 2;
    const BOTTOM_ID = 3;

    const TOP_NAME = 'Top';
    const MIDDLE_NAME = 'Middle';
    const BOTTOM_NAME = 'Bottom';

    public function toOptionArray()
    {
        $helper = $this->_getHelper();
        return array(
            array('value' => self::TOP_ID, 'label' => $helper->__(self::TOP_NAME)),
            array('value' => self::MIDDLE_ID, 'label' => $helper->__(self::MIDDLE_NAME)),
            array('value' => self::BOTTOM_ID, 'label' => $helper->__(self::BOTTOM_NAME)),
        );
    }
}
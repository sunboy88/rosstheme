<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productquestions
 * @version    1.5.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Productquestions_Model_Source_Question_Status {
    const STATUS_PUBLIC = 1;
    const STATUS_PRIVATE = 2;

    public static function toShortOptionArray() {
        return array(
            self::STATUS_PUBLIC => Mage::helper('productquestions')->__('Public'),
            self::STATUS_PRIVATE => Mage::helper('productquestions')->__('Private')
        );
    }

    public static function toOptionArray() {
        $res = array();

        foreach (self::toShortOptionArray() as $key => $value)
            $res[] = array(
                'value' => $key,
                'label' => $value);

        return $res;
    }

}

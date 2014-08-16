<?php
/**
 * SaG_Button
 *
 * @category   SaG
 * @package    SaG_Button
 * @author     Sendasgift.com <info@sendasgift.com>
 */

class SaG_Button_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getButtonDimensions($language, $size)
    {
        $dimensionsMap = Mage::getModel('sagbutton/config_language')->getFullMap();
        if (!$size) {
            $size = 'default';
        }
        if (isset($dimensionsMap[$language]['size'][$size])) {
            return array(
                $dimensionsMap[$language]['size'][$size]['width'],
                $dimensionsMap[$language]['size'][$size]['height'],
            );
        }
        return false;
    }

}

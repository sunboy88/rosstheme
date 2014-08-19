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
 * @package     Magestore_AffiliateplusBanner
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusBanner Helper
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * target links
     */
    const TARGET_BLANK  = '_blank';
    const TARGET_SELF   = '_self';
    const TARGET_PARENT = '_parent';
    const TARGET_TOP    = '_top';
    
    /**
     * banner types
     */
    const BANNER_TYPE_IMAGE = 1;
    const BANNER_TYPE_FLASH = 2;
    const BANNER_TYPE_TEXT  = 3;
    
    const BANNER_TYPE_HOVER = 4;
    const BANNER_TYPE_PEEL  = 5;
    const BANNER_TYPE_ROTATOR = 6;
    
    /**
     * peel banner direction
     */
    const TOP_LEFT_CORNER   = 1;
    const TOP_RIGHT_CORNER  = 2;
    const BOTTOM_LEFT_CORNER    = 3;
    const BOTTOM_RIGHT_CORNER   = 4;
    
    /**
     * Link types target
     * 
     * @return array
     */
    public function getTargetHash()
    {
        return array(
            self::TARGET_BLANK  => $this->__('Blank'),
            self::TARGET_PARENT => $this->__('Parent'),
            self::TARGET_SELF   => $this->__('Self'),
            self::TARGET_TOP    => $this->__('Top')
        );
    }
    
    public function getTargetArray()
    {
        $options = array();
        foreach ($this->getTargetHash() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }
    
    /**
     * Banner types options
     * 
     * @return array
     */
    public function getOptionHash()
    {
        return array(
            self::BANNER_TYPE_IMAGE => $this->__('Image'),
            self::BANNER_TYPE_FLASH => $this->__('Flash'),
            self::BANNER_TYPE_TEXT  => $this->__('Text'),
            self::BANNER_TYPE_HOVER => $this->__('Hover'),
            self::BANNER_TYPE_PEEL  => $this->__('Page Peel'),
            self::BANNER_TYPE_ROTATOR   => $this->__('Rotator')
        );
    }
    
    public function getOptionArray()
    {
        $options = array();
        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }
    
    /**
     * all peel direction option
     * 
     * @return array
     */
    public function getDirectionsHash()
    {
        return array(
            self::TOP_LEFT_CORNER   => $this->__('Top Left Corner'),
            self::TOP_RIGHT_CORNER  => $this->__('Top Right Corner'),
            self::BOTTOM_LEFT_CORNER    => $this->__('Bottom Left Corner'),
            self::BOTTOM_RIGHT_CORNER   => $this->__('Bottom Right Corner')
        );
    }
    
    public function getDirectionsArray()
    {
        $options = array();
        foreach ($this->getDirectionsHash() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }
}
<?php
/**
 * SaG_Button
 *
 * @category   SaG
 * @package    SaG_Button
 * @author     Sendasgift.com <info@sendasgift.com>
 */

class SaG_Button_Model_Config_Size
{
    protected $_options;

    public function getOptionArray()
    {
        return array(
            'mini'      => Mage::helper('sagbutton')->__('Mini'),
            'small'     => Mage::helper('sagbutton')->__('Small'),
            'default'   => Mage::helper('sagbutton')->__('Default'),
            'large'     => Mage::helper('sagbutton')->__('Large'),
        );
    }

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
            $this->_options = $this->getOptionArray();
        }
//
//        $options = $this->_options;
//        if(!$isMultiselect){
//            array_unshift($options, Mage::helper('adminhtml')->__('--Please Select--'));
//        }

        return $this->_options;
    }
}
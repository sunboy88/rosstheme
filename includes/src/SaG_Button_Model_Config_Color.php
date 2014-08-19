<?php
/**
 * SaG_Button
 *
 * @category   SaG
 * @package    SaG_Button
 * @author     Sendasgift.com <info@sendasgift.com>
 */

class SaG_Button_Model_Config_Color
{
    protected $_options;

    public function getOptionArray()
    {
        return array(
            'default'   => Mage::helper('sagbutton')->__('Default'),
            'black'     => Mage::helper('sagbutton')->__('Black'),
            'blue'      => Mage::helper('sagbutton')->__('Blue'),
            'green'     => Mage::helper('sagbutton')->__('Green'),
            'grey'      => Mage::helper('sagbutton')->__('Grey'),
            'orange'    => Mage::helper('sagbutton')->__('Orange'),
            'pink'      => Mage::helper('sagbutton')->__('Pink'),
            'red'       => Mage::helper('sagbutton')->__('Red'),
        );
    }

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
            $this->_options = $this->getOptionArray();
        }

        return $this->_options;
    }
}
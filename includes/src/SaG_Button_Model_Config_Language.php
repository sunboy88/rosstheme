<?php
/**
 * SaG_Button
 *
 * @category   SaG
 * @package    SaG_Button
 * @author     Sendasgift.com <info@sendasgift.com>
 */

class SaG_Button_Model_Config_Language
{
    protected $_options;

    public function getOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array();
            foreach ($this->getFullMap() as $lang => $set) {
                $this->_options[$lang] = $set['caption'];
            }
        }
        return $this->_options;
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

    public function getFullMap()
    {
        return array(
            'en' => array(
                'size' => array(
                    'mini' => array(
                        'width' => 90,
                        'height' => 22,
                    ),
                    'small' => array(
                        'width' => 99,
                        'height' => 30,
                    ),
                    'large' => array(
                        'width' => 128,
                        'height' => 39,
                    ),
                    'default' => array(
                        'width' => 110,
                        'height' => 28,
                    ),
                ),
                'caption' => Mage::helper('sagbutton')->__('English')
            ),
            'he' => array(
                'size' => array(
                    'mini' => array(
                        'width' => 88,
                        'height' => 22,
                    ),
                    'small' => array(
                        'width' => 94,
                        'height' => 30,
                    ),
                    'large' => array(
                        'width' => 120,
                        'height' => 39,
                    ),
                    'default' => array(
                        'width' => 102,
                        'height' => 28,
                    ),
                ),
                'caption' => Mage::helper('sagbutton')->__('Hebrew')
            )
        );
    }

}
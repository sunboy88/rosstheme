<?php 
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Model_System_Config_Source_Sidebar
{
    public function toOptionArray()
    {
        return array(
        	array('value' => 'none', 'label'=>Mage::helper('adminhtml')->__('None')),
            array('value' => 'left', 'label'=>Mage::helper('adminhtml')->__('Left Sidebar')),
            array('value' => 'right', 'label'=>Mage::helper('adminhtml')->__('Right Sidebar'))
        );
    }

}

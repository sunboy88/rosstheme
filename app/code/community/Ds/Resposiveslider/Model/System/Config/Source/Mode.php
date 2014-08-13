<?php
class Ds_Resposiveslider_Model_System_Config_Source_Mode
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
        
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('horizontal')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('vertical')),
            array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('fade')),
        );
    }

}

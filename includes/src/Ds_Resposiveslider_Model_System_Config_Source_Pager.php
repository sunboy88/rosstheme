<?php
class Ds_Resposiveslider_Model_System_Config_Source_Pager
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
		
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('full')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('short')),
        );
    }

}

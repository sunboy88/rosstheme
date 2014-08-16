<?php
class Devinc_Occ_Model_Source_Group
{
	//returns customer groups
    public function toOptionArray()
    {
        $options = Mage::getResourceModel('customer/group_collection')
            //->setRealGroupsFilter()
            ->loadData()->toOptionArray();

        array_unshift($options, array('label' => '', 'value' => ''));
        
        return $options;
    }
}

<?php
/**
 * Brands block
 *
 */
class Olegnax_Athlete_Block_Bannerslider extends Mage_Core_Block_Template
{
    public function getSlides()
    {
        $slides = null;
        $slideGroup = $this->getSlideGroup();
        if ($slideGroup) {
            $slides = Mage::getModel('athlete/bannerslider')->getCollection()
                ->addStoreFilter(Mage::app()->getStore())
                ->addFieldToSelect('*')
                ->addFieldToFilter('status', 1)
	            ->setOrder('sort_order', 'asc')
	            ->join(array('groups' => 'bannerslider_slides_group'), 'groups.group_id = main_table.slide_group', 'groups.group_name')
	            ->addFieldToFilter('groups.group_name', $slideGroup);
        }
        return $slides;
    }

	public function getGroup($group_name)
	{
		return Mage::getModel('athlete/bannerslider_group')->load($group_name, 'group_name');
	}

	public function getSliderId($group_name)
	{
		return 'banners_slider_'.$group_name;
	}
}
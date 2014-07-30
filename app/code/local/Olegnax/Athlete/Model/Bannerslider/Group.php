<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Bannerslider_Group extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('athlete/bannerslider_group');
    }

	public function toOptionArray()
	{
		$groups = array();
		$groupsCollection = $this->getCollection();
		foreach( $groupsCollection as $_group ) {
			$groups[] =array(
				'value' => $_group->getGroupId(),
				'label' => $_group->getGroupName(),
			);
		}
		return $groups;
	}

}
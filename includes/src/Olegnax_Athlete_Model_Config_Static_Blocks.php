<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Static_Blocks
{

    public function toOptionArray()
    {
		$collection = Mage::getModel('cms/block')->getCollection()
			->addFieldToFilter('is_active', 1);

	    $blocks = array();
	    $blocks[] = array(
		    'value' => '',
		    'label' => ' - None - ',
	    );
	    foreach($collection as $_block){
		    $blocks[] = array(
			    'value' => $_block->getIdentifier(),
			    'label' => $_block->getTitle(),
		    );
	    }

        return $blocks;
    }

}

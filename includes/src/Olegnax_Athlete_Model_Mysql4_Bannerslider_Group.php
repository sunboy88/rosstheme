<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Mysql4_Bannerslider_Group extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('athlete/bannerslider_slides_group', 'group_id');
    }

}
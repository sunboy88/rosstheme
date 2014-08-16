<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Model_Config_Yesno
{
    public function toOptionArray()
    {
	    $options = array();
	    $options[] = array(
            'value' => 'true',
            'label' => 'Yes',
        );
        $options[] = array(
            'value' => 'false',
            'label' => 'No',
        );

        return $options;
    }

}

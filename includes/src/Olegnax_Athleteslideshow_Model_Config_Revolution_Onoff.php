<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Model_Config_Revolution_Onoff
{
    public function toOptionArray()
    {
	    $options = array();
	    $options[] = array(
            'value' => 'on',
            'label' => 'On',
        );
        $options[] = array(
            'value' => 'off',
            'label' => 'Off',
        );

        return $options;
    }

}

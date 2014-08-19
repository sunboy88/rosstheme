<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Model_Config_Show
{
    public function toOptionArray()
    {
	    $options = array();
	    $options[] = array(
            'value' => 'home',
            'label' => 'HomePage Only',
        );
        $options[] = array(
            'value' => 'all',
            'label' => 'All Pages',
        );

        return $options;
    }

}

<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Model_Config_Autoheight
{
    public function toOptionArray()
    {
	    $options = array();
	    $options[] = array(
            'value' => 'container',
            'label' => 'adjust height per slide',
        );
        $options[] = array(
            'value' => 'calc',
            'label' => 'calculate the tallest slide and use it',
        );

        return $options;
    }

}

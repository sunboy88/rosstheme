<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Model_Config_Revolution_Shadow
{
    public function toOptionArray()
    {
	    $options = array();
        $options[] = array(
            'value' => '0',
            'label' => '0',
        );
	    $options[] = array(
            'value' => '1',
            'label' => '1',
        );
        $options[] = array(
            'value' => '2',
            'label' => '2',
        );
        $options[] = array(
            'value' => '3',
            'label' => '3',
        );

        return $options;
    }

}
<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Model_Config_Revolution_Navbullets
{
    public function toOptionArray()
    {
	    $options = array();
        $options[] = array(
            'value' => 'athlete',
            'label' => 'athlete',
        );
	    $options[] = array(
            'value' => 'round',
            'label' => 'round',
        );
	    $options[] = array(
            'value' => 'navbar',
            'label' => 'navbar',
        );
        $options[] = array(
            'value' => 'round-old',
            'label' => 'round-old',
        );
        $options[] = array(
            'value' => 'square-old',
            'label' => 'square-old',
        );
        $options[] = array(
            'value' => 'navbar-old',
            'label' => 'navbar-old',
        );

        return $options;
    }

}
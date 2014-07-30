<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Model_Config_Easing
{
    public function toOptionArray()
    {
	    $options = array();
	    $options[] = array(
            'value' => 'easeOutQuad',
            'label' => 'easeOutQuad',
        );
        $options[] = array(
            'value' => 'easeInQuad',
            'label' => 'easeInQuad',
        );
	    $options[] = array(
            'value' => 'easeInOutQuad',
            'label' => 'easeInOutQuad',
        );
	    $options[] = array(
            'value' => 'easeInCubic',
            'label' => 'easeInCubic',
        );
	    $options[] = array(
            'value' => 'easeOutCubic',
            'label' => 'easeOutCubic',
        );
	    $options[] = array(
            'value' => 'easeInOutCubic',
            'label' => 'easeInOutCubic',
        );
	    $options[] = array(
            'value' => 'easeInQuart',
            'label' => 'easeInQuart',
        );
	    $options[] = array(
            'value' => 'easeOutQuart',
            'label' => 'easeOutQuart',
        );
	    $options[] = array(
            'value' => 'easeInOutQuart',
            'label' => 'easeInOutQuart',
        );
	    $options[] = array(
            'value' => 'easeInQuint',
            'label' => 'easeInQuint',
        );
	    $options[] = array(
            'value' => 'easeOutQuint',
            'label' => 'easeOutQuint',
        );
	    $options[] = array(
            'value' => 'easeInOutQuint',
            'label' => 'easeInOutQuint',
        );
	    $options[] = array(
            'value' => 'easeInSine',
            'label' => 'easeInSine',
        );
	    $options[] = array(
            'value' => 'easeOutSine',
            'label' => 'easeOutSine',
        );
	    $options[] = array(
            'value' => 'easeInOutSine',
            'label' => 'easeInOutSine',
        );
	    $options[] = array(
            'value' => 'easeInExpo',
            'label' => 'easeInExpo',
        );
	    $options[] = array(
            'value' => 'easeOutExpo',
            'label' => 'easeOutExpo',
        );
	    $options[] = array(
            'value' => 'easeInOutExpo',
            'label' => 'easeInOutExpo',
        );
	    $options[] = array(
            'value' => 'easeInCirc',
            'label' => 'easeInCirc',
        );
	    $options[] = array(
            'value' => 'easeOutCirc',
            'label' => 'easeOutCirc',
        );
	    $options[] = array(
            'value' => 'easeInOutCirc',
            'label' => 'easeInOutCirc',
        );
	    $options[] = array(
            'value' => 'easeInElastic',
            'label' => 'easeInElastic',
        );
	    $options[] = array(
            'value' => 'easeOutElastic',
            'label' => 'easeOutElastic',
        );
	    $options[] = array(
            'value' => 'easeInOutElastic',
            'label' => 'easeInOutElastic',
        );
	    $options[] = array(
            'value' => 'easeInBack',
            'label' => 'easeInBack',
        );
	    $options[] = array(
            'value' => 'easeOutBack',
            'label' => 'easeOutBack',
        );
	    $options[] = array(
            'value' => 'easeInOutBack',
            'label' => 'easeInOutBack',
        );
	    $options[] = array(
            'value' => 'easeInBounce',
            'label' => 'easeInBounce',
        );
	    $options[] = array(
            'value' => 'easeOutBounce',
            'label' => 'easeOutBounce',
        );
	    $options[] = array(
            'value' => 'easeInOutBounce',
            'label' => 'easeInOutBounce',
        );

        return $options;
    }

}

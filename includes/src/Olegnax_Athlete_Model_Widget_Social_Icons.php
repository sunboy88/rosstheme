<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Widget_Social_Icons
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'rss', 'label' => Mage::helper('athlete')->__('rss')),
            array('value'=>'facebook', 'label' => Mage::helper('athlete')->__('facebook')),
            array('value'=>'twitter', 'label' => Mage::helper('athlete')->__('twitter')),
            array('value'=>'mail', 'label' => Mage::helper('athlete')->__('mail')),
            array('value'=>'youtube', 'label' => Mage::helper('athlete')->__('youtube')),
            array('value'=>'gplus', 'label' => Mage::helper('athlete')->__('gplus')),
            array('value'=>'dribble', 'label' => Mage::helper('athlete')->__('dribble')),
            array('value'=>'flicker', 'label' => Mage::helper('athlete')->__('flicker')),
            array('value'=>'vimeo', 'label' => Mage::helper('athlete')->__('vimeo')),
            array('value'=>'linkedin', 'label' => Mage::helper('athlete')->__('linkedin')),
            array('value'=>'pinterest', 'label' => Mage::helper('athlete')->__('pinterest')),
            array('value'=>'picasa', 'label' => Mage::helper('athlete')->__('picasa')),
            array('value'=>'digg', 'label' => Mage::helper('athlete')->__('digg')),
            array('value'=>'plurk', 'label' => Mage::helper('athlete')->__('plurk')),
            array('value'=>'tripadvisor', 'label' => Mage::helper('athlete')->__('tripadvisor')),
            array('value'=>'yahoo', 'label' => Mage::helper('athlete')->__('yahoo')),
            array('value'=>'delicious', 'label' => Mage::helper('athlete')->__('delicious')),
            array('value'=>'devianart', 'label' => Mage::helper('athlete')->__('devianart')),
            array('value'=>'tumblr', 'label' => Mage::helper('athlete')->__('tumblr')),
            array('value'=>'skype', 'label' => Mage::helper('athlete')->__('skype')),
            array('value'=>'apple', 'label' => Mage::helper('athlete')->__('apple')),
            array('value'=>'aim', 'label' => Mage::helper('athlete')->__('aim')),
            array('value'=>'paypal', 'label' => Mage::helper('athlete')->__('paypal')),
            array('value'=>'blogger', 'label' => Mage::helper('athlete')->__('blogger')),
            array('value'=>'behance', 'label' => Mage::helper('athlete')->__('behance')),
            array('value'=>'myspace', 'label' => Mage::helper('athlete')->__('myspace')),
            array('value'=>'stumble', 'label' => Mage::helper('athlete')->__('stumble')),
            array('value'=>'forrst', 'label' => Mage::helper('athlete')->__('forrst')),
            array('value'=>'imdb', 'label' => Mage::helper('athlete')->__('imdb')),
            array('value'=>'instagram', 'label' => Mage::helper('athlete')->__('instagram')),
        );
    }

}
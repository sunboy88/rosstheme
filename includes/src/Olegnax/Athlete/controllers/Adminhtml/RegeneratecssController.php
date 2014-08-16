<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Adminhtml_RegeneratecssController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
	    $r = Mage::app()->getRequest();
        Mage::getSingleton('athlete/css')->regenerate();
        $this->getResponse()->setRedirect(
	        Mage::helper('core/url')->urlDecode( $r->getParam('back_url') )
        );
    }
}
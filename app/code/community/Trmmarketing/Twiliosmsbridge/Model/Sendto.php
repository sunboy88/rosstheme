<?php
/**
 * @category    Trmmarketing
 * @package     Trmmarketing_Twiliosmsbridge
 * @copyright   Copyright (c) 2013 TRM Marketing LLC
 * @license     http://www.trm-marketing.com/solutions/license/TRM-Marketing-Standard-License-Agreement.html
 */
class Trmmarketing_Twiliosmsbridge_Model_Sendto
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'billing', 'label'=>Mage::helper('twiliosmsbridge')->__('Billing')),
            array('value'=>'shipping', 'label'=>Mage::helper('twiliosmsbridge')->__('Shipping')),
			array('value'=>'both', 'label'=>Mage::helper('twiliosmsbridge')->__('Billing & Shipping (If different)')),                  
        );
    }

}


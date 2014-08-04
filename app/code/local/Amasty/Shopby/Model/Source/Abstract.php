<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */
abstract class Amasty_Shopby_Model_Source_Abstract extends Varien_Object
{
    abstract public function toOptionArray();

    public function getHash()
    {
        $options = $this->toOptionArray();
        $hash = array();
        foreach ($options as $option) {
            $hash[$option['value']] = $option['label'];
        }
        return $hash;
    }
}
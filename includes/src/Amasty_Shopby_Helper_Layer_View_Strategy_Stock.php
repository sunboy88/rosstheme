<?php
class Amasty_Shopby_Helper_Layer_View_Strategy_Stock extends Amasty_Shopby_Helper_Layer_View_Strategy_Abstract
{
    protected function setTemplate()
    {
        return 'amasty/amshopby/attribute.phtml';
    }

    protected function setPosition()
    {
        return $this->filter->getPosition();
    }

    protected function setHasSelection()
    {
        return isset($_GET['stock']);
    }

    protected function setCollapsed()
    {
        return false;
    }
}

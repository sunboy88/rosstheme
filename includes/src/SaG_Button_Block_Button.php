<?php
/**
 * SaG_Button
 *
 * @category   SaG
 * @package    SaG_Button
 * @author     Sendasgift.com <info@sendasgift.com>
 */

class SaG_Button_Block_Button extends Mage_Core_Block_Template
{
    protected $_language;
    protected $_size;
    protected $_color;

    protected $_configNode = 'pp';

    public function setConfigNode($node)
    {
        $this->_configNode = $node;
    }

    protected function _beforeToHtml()
    {
        $this->_language = Mage::getStoreConfig("sag_settings/{$this->_configNode}/language");
        $this->_size = Mage::getStoreConfig("sag_settings/{$this->_configNode}/size");
        $this->_color = Mage::getStoreConfig("sag_settings/{$this->_configNode}/color");
        return parent::_beforeToHtml();
    }

    public function getLanguage()
    {
        return $this->_language;
    }

    public function getSize()
    {
        return $this->_size;
    }

    public function getColor()
    {
        return $this->_color;
    }

    public function getButtonImgUrl()
    {
        $src = "//d3tlta7dhj25xj.cloudfront.net/images/buttons/{$this->_language}";
        if ($this->_size != 'default') {
            $src .= '-' . $this->_size;
        }
        if ($this->_color != 'default') {
            $src .= '-' . $this->_color;
        }
        $src .= '.png';
        return $src;
    }

}

<?php

class Magestore_Affiliatepluswidget_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getConfig($code, $store = null){
		return Mage::getStoreConfig("affiliateplus/widget/$code",$store);
	}
	
	public function prepareParams($params){
        $params = array_merge(array(
            'widget_id'     => '',
            'product_id'    => '',
            'category_ids'  => '',
            'widget_size'   => '',
            'height'        => '',
            'width'         => '',
            'rows'          => '',
            'columns'       => '',
            'account_id'    => '',
        ), $params);
        
		unset($params['widget_id']);
		
    	if ($params['product_id']) unset($params['category_ids']);
    	else unset($params['product_id']);
    	
    	if ($params['widget_size']){
    		unset($params['height']);
    		unset($params['width']);
    		unset($params['rows']);
    		unset($params['columns']);
    	}else unset($params['widget_size']);
    	
    	if ($params['account_id']){
	    	//$account = Mage::getModel('affiliateplus/account')->load($params['account_id']);
	    	//unset($params['account_id']);
    	} else {
    		$account = Mage::getSingleton('affiliateplus/session')->getAccount();
    		$params['account_id'] = $account->getId();
    	}
    	//$params['acc'] = $account->getIdentifyCode();
    	
    	if (Mage::app()->getStore()->getId() != Mage::app()->getDefaultStoreView()->getId())
    		$params['___store'] = Mage::app()->getStore()->getCode();
    	
		return $params;
	}
	
	public function getWidgetCode($params){
		$params = $this->prepareParams($params);
		$url = Mage::getUrl('affiliatepluswidget/index/widget',array('_query' => $params));
		$code = sprintf('<script charset="utf-8" type="text/javascript" src="%s"></script>',$url);
		
		$url = Mage::getUrl(null,array('_query' => array(
			'acc'	=> Mage::getModel('affiliateplus/account')->load($params['account_id'])->getIdentifyCode(),
		)));
		$code .= sprintf('<noscript><a href="%s">%s</a></noscript>'
    		,$url
    		,Mage::app()->getStore()->getFrontendName());
    	return $code;
	}
    
    public function disableMenu() {
        if (!$this->getConfig('enable')) {
            return true;
        }
        return Mage::helper('affiliateplus/account')->accountNotLogin();
    }
}
<?php
class Ds_Resposiveslider_Block_Slider extends Mage_Core_Block_Template
{
	public function getSlides()
	{
		$slide	=	Mage::getModel('resposiveslider/resposiveslider')
							->getCollection()
							->addFieldToFilter('is_active',0)
							->addFieldToFilter('image_name', array('neq' => ''));
		return $slide->getData();
	}
	
	public function isJqueryEnabled()
	{
		$configValue	=	Mage::getStoreConfig('ds_resposiveslidersec/manage_settings/enable_jquery');
		$isEnabled		=	0;
		if($configValue == 1)
		{
			$isEnabled	=	1;
		}
		return $isEnabled;
	}
	
	public function getMode()
	{
		$configValue	=	Mage::getStoreConfig('ds_resposiveslidersec/manage_settings/mode');
		$mode			=	'';
		if($configValue == 1)
		{
			$mode	=	"fade";
		}
		if($configValue == 2)
		{
			$mode	=	"vertical";
		}
		return $mode;
	}
	
	public function getSpeed()
	{
		$configValue	=	Mage::getStoreConfig('ds_resposiveslidersec/manage_settings/speed');
		return $configValue;
	}
	
	public function isAutoControlEnabled()
	{
		$configValue	=	Mage::getStoreConfig('ds_resposiveslidersec/manage_settings/autocontrols');
		$isEnabled		=	0;
		if($configValue == 1)
		{
			$isEnabled	=	1;
		}
		return $isEnabled;
	}
	
	public function isAutoTransitionEnabled()
	{
		$configValue	=	Mage::getStoreConfig('ds_resposiveslidersec/manage_settings/auto');
		$isEnabled		=	0;
		if($configValue == 1)
		{
			$isEnabled	=	1;
		}
		return $isEnabled;
	}
	
	public function isPagerEnabled()
	{
		$configValue	=	Mage::getStoreConfig('ds_resposiveslidersec/manage_settings/pager');
		$isEnabled		=	0;
		if($configValue == 1)
		{
			$isEnabled	=	1;
		}
		return $isEnabled;
	}
	
	public function getPagerType()
	{
		$configValue	=	Mage::getStoreConfig('ds_resposiveslidersec/manage_settings/pagertype');
		$mode			=	'';
		if($configValue == 1)
		{
			$mode	=	"full";
		}
		if($configValue == 2)
		{
			$mode	=	"short";
		}
		return $mode;
	}
	
	public function getPagerShortSeparator()
	{
		$configValue	=	Mage::getStoreConfig('ds_resposiveslidersec/manage_settings/pagershortseparator');
		$pagerType	=	$this->getPagerType();
		$saperator	=	"";
		if($pagerType == "short" && !empty($configValue))
		{
			$saperator	=	$configValue;
		}
		elseif($pagerType == "short" && empty($configValue))
		{
			$saperator	=	"/";
		}
		else
		{
			$saperator	=	"/";
		}
		return $saperator;
	}
	
	public function isControlsEnabled()
	{
		$configValue	=	Mage::getStoreConfig('ds_resposiveslidersec/manage_settings/controls');
		$isEnabled		=	0;
		if($configValue == 1)
		{
			$isEnabled	=	1;
		}
		return $isEnabled;
	}
	
	public function isAutoHoverEnabled()
	{
		$configValue	=	Mage::getStoreConfig('ds_resposiveslidersec/manage_settings/autohover');
		$isEnabled		=	0;
		if($configValue == 1)
		{
			$isEnabled	=	1;
		}
		return $isEnabled;
	}
}
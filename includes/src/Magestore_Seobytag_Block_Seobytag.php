<?php

class Magestore_Seobytag_Block_Seobytag extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		if (!Mage::helper('magenotification')->checkLicenseKey('Seobytag')){
			return parent::_prepareLayout();
		}
        $headBlock = $this->getLayout()->getBlock('head');
        if (Mage::helper('seobytag')->getConfig('enable') && $headBlock){
	        $tagId = $this->getRequest()->getParam('tagId');
	        $tagModel = Mage::getModel('tag/tag')->load($tagId);
            if ($title = $tagModel->getMetaTitle())
                $headBlock->setTitle($title);
            if ($keywords = $tagModel->getMetaKeywords())
                $headBlock->setKeywords($keywords);
            if ($description = $tagModel->getMetaDescription())
                $headBlock->setDescription($description);
        }
        return parent::_prepareLayout();
    }
}
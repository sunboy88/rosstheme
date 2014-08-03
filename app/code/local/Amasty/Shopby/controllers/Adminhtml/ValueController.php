<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Adminhtml_ValueController extends Mage_Adminhtml_Controller_Action
{
    // edit filters (uses tabs)
    public function editAction() 
    {
        $id     = (int) $this->getRequest()->getParam('id');
        $model  = Mage::getModel('amshopby/value')->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Option does not exist'));
            $this->_redirect('*/adminhtml_filter/index');
            return;
        }
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        // todo: save images
        
        Mage::register('amshopby_value', $model);

        $this->loadLayout();
        
        $this->_setActiveMenu('catalog/amshopby');
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_value_edit'));
        
        $this->renderLayout();
    }

    public function saveAction() 
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('amshopby/value')
                   ->load($id);
        $filterId = $model->getFilterId();
                   
        $data = $this->getRequest()->getPost();
        if (!$data) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Unable to find an option to save'));
            $this->_redirect('*/adminhtml_filter/');
        }
        
        //upload images
        $path = Mage::getBaseDir('media') . DS . 'amshopby' . DS;
        $imagesTypes = array('big', 'small', 'medium', 'small_hover');
        foreach ($imagesTypes as $type){
            $field = 'img_' . $type;
            
            $isRemove = isset($data['remove_' . $field]);
            $hasNew   = !empty($_FILES[$field]['name']);
            
            try {
                // remove the old file
                if ($isRemove || $hasNew){
                    $oldName = $model->getData($field);
                    if ($oldName){
                         @unlink($path . $oldName);
                         $data[$field] = '';
                    }
                }
    
                // upload a new if any
                if (!$isRemove && $hasNew){
                    $newName = $type . $id;
                    $newName .= '.' . strtolower(substr(strrchr($_FILES[$field]['name'], '.'), 1)); 
               
                    $uploader = new Varien_File_Uploader($field);
                    $uploader->setFilesDispersion(false);
                    $uploader->setAllowRenameFiles(false);
                       $uploader->setAllowedExtensions(array('png','gif', 'jpg', 'jpeg'));
                    $uploader->save($path, $newName);    
                     
                    $data[$field] = $newName;            
                }   
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());    
            }
        }
        
        try {
            $model->setData($data)->setId($id);
            
            $model->save();
            Mage::getSingleton('adminhtml/session')->setFormData(false);
            
            $msg = Mage::helper('amshopby')->__('Option properties have been successfully saved');
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);

            $this->_redirect('*/adminhtml_filter/edit', array('id'=>$filterId, 'tab'=>'values'));
           
            
        } 
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $id));
        }

        $this->flushCache();
    }

    protected function flushCache()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        $helper->flushCache();
    }

}
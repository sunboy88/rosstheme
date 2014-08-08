<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Adminhtml_MailController extends Mage_Adminhtml_Controller_Action {
  protected function _initAction(){
    $this->loadLayout()
      ->_setActiveMenu('dailydeal/dailydeal')
      ->_addBreadcrumb(Mage::helper('adminhtml')->__('Email Manager'), Mage::helper('adminhtml')->__('Email Manager'));
    return $this;
  }
 
  public function indexAction(){
    $this->_initAction()
      ->renderLayout();
  }
   
  public function deleteAction() {
    if( $this->getRequest()->getParam('id') > 0 ) {
      try {
        $model = Mage::getModel('dailydeal/mail');
        $model->setId($this->getRequest()->getParam('id'))
          ->delete();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
        $this->_redirect('*/*/');
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
      }
    }
    $this->_redirect('*/*/');
  }

  public function massDeleteAction() {
    $dailydealIds = $this->getRequest()->getParam('dailydeal');
    if(!is_array($dailydealIds)){
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
    }else{
      try {
        foreach ($dailydealIds as $dailydealId) {
          $dailydeal = Mage::getModel('dailydeal/mail')->load($dailydealId);
          $dailydeal->delete();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($dailydealIds)));
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }
  
  public function massStatusAction() {
    $dailydealIds = $this->getRequest()->getParam('dailydeal');
    if(!is_array($dailydealIds)) {
      Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
    } else {
      try {
        foreach ($dailydealIds as $dailydealId) {
          $dailydeal = Mage::getSingleton('dailydeal/mail')
            ->load($dailydealId)
            ->setStatus($this->getRequest()->getParam('status'))
            ->setIsMassupdate(true)
            ->save();
        }
        $this->_getSession()->addSuccess(
          $this->__('Total of %d record(s) were successfully updated', count($dailydealIds))
        );
      } catch (Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }
  
  public function exportCsvAction(){
    $fileName   = 'mail.csv';
    $content  = $this->getLayout()->createBlock('dailydeal/adminhtml_mail_grid')->getCsv();
    $this->_prepareDownloadResponse($fileName,$content);
  }

  public function exportXmlAction(){
    $fileName   = 'mail.xml';
    $content  = $this->getLayout()->createBlock('dailydeal/adminhtml_mail_grid')->getXml();
    $this->_prepareDownloadResponse($fileName,$content);
  }
  
  public function importAction() {
    $this->_initAction()
      ->renderLayout();
  }
     
  public function saveImportAction() {
    if ($data = $this->getRequest()->getPost()) {
      $flag = false;
      $i = 0;
      if(isset($_FILES['subscriber_csv_file']['name']) && $_FILES['subscriber_csv_file']['name'] != '') {
        try {
          $uploader = new Varien_File_Uploader('subscriber_csv_file');
          $uploader->setAllowedExtensions(array('csv'));
          $uploader->setAllowRenameFiles(false);
          $uploader->setFilesDispersion(false);
          $path = Mage::getBaseDir('media').DS.'dailydeal'. DS.'subscriber'. DS ;
          $uploader->save($path, $_FILES['subscriber_csv_file']['name'] );
          $filepath = $path.$_FILES['subscriber_csv_file']['name'];
          $handler = new Varien_File_Csv();
          $importData = $handler->getData($filepath);
          $keys = $importData[0];
          foreach($keys as $key=>$value)
          {
            $keys[$key] = str_replace(' ', '_', strtolower($value));
          }
          $count = count($importData);
          $model = Mage::getModel('dailydeal/mail');
          $collection = $model->getCollection();
          $subscribersImport = array();

          while(--$count>0) {
            Mage::log('item'.$count);
            $currentData = $importData[$count];
            $data = array_combine($keys, $currentData);
            // Set status "Enable" for all subscribers
            $data['status'] = 1;
            if(!Mage::helper('dailydeal')->checkEmailSubscriber($data['email'])){
              $model->setData($data)->save();
              $flag = true;
              $i++;
            }
          }
        }catch (Exception $e) {
          
        }
      }
      if($flag){
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dailydeal')->__('Total of %d subscriber(s) were successfully imported', $i));
      }else {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('There is no item to import'));
      } 
      $this->_redirect('*/adminhtml_mail/index');
    }
  }
}
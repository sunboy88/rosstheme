<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Adminhtml_DailydealController extends Mage_Adminhtml_Controller_Action {
  protected function _initAction(){
    $this->loadLayout()
    ->_setActiveMenu('dailydeal/dailydeal')
    ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
    return $this;
  }

  public function indexAction(){
    $this->_initAction()
    ->renderLayout();
  }

  public function editAction() {
    $id  = $this->getRequest()->getParam('id');
    $model  = Mage::getModel('dailydeal/deal')->load($id);
    if ($model->getId() || $id == 0) {
      $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
      if (!empty($data))
        $model->setData($data);
      Mage::register('dailydeal_data', $model);
      $this->loadLayout();
      $this->_setActiveMenu('dailydeal/dailydeal');
      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
      $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
      $this->_addContent($this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit'))
      ->_addLeft($this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit_tabs'));

      $this->renderLayout();
    } else {
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Item does not exist'));
      $this->_redirect('*/*/');
    }
  }

  public function newAction() {
    $this->_forward('edit');
  }

  public function saveAction() {
    if ($data = $this->getRequest()->getPost()) {     
      $model = Mage::getModel('dailydeal/deal');  
      $model->setData($data)
      ->setId($this->getRequest()->getParam('id'));
      $dealProductIds = $model->getDealProductIds();
      if($dealProductIds[0]){
        $product=Mage::getModel('catalog/product')->load($dealProductIds[0]);
        $model->setProductId($dealProductIds[0]);
        $model->setTitle($product->getName());
      }
      if($model->getStartTime()){       
        $iso_date = Mage::helper('dailydeal')->getIsoDate($model->getStartTime());        
        $st_time = strtotime($iso_date);
        $startTime = date('Y-m-d H:i:s', Mage::getSingleton('core/date')->gmtTimestamp($st_time));        
        $model->setStartTime($startTime);
      }
      if($model->getEndTime()){
        $iso_date = Mage::helper('dailydeal')->getIsoDate($model->getEndTime());        
        $st_time = strtotime($iso_date);
        $endTime = date('Y-m-d H:i:s', Mage::getSingleton('core/date')->gmtTimestamp($st_time));
        $model->setEndTime($endTime);
      }
      if(!($model->getStatus() == '4')){
        $st_time = strtotime(Mage::helper('dailydeal')->getIsoDate($model->getStartTime()));
        $en_time = strtotime(Mage::helper('dailydeal')->getIsoDate($model->getEndTime()));        
        $now = Mage::getModel('core/date')->gmtTimestamp();
        if($st_time > $now){
          $model->setStatus(1);
        }else if(($st_time <= $now) && ($now <= $en_time)){
            $model->setStatus(2);
          }else{
            $model->setStatus(3);
        }
      }
      try {
        $model->save();        
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dailydeal')->__('Deal was successfully saved'));
        Mage::getSingleton('adminhtml/session')->setFormData(false);
        if ($this->getRequest()->getParam('back')) {
          $this->_redirect('*/*/edit', array('id' => $model->getId()));
          return;
        }
        $this->_redirect('*/*/');
        return;
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        Mage::getSingleton('adminhtml/session')->setFormData($data);
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
      }
    }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Unable to find deal to save'));
    $this->_redirect('*/*/');
  }

  public function deleteAction() {
    if( $this->getRequest()->getParam('id') > 0 ) {
      try {
        $model = Mage::getModel('dailydeal/deal');
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
          $dailydeal = Mage::getModel('dailydeal/deal')->load($dailydealId);
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
          $dailydeal = Mage::getModel('dailydeal/deal')
          ->load($dailydealId)
          ->setStatus($this->getRequest()->getParam('status'))
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
    $fileName   = 'dailydeal.csv';
    $content  = $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_grid')->getCsv();
    $this->_prepareDownloadResponse($fileName,$content);
  }

  public function exportXmlAction(){
    $fileName   = 'dailydeal.xml';
    $content  = $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_grid')->getXml();
    $this->_prepareDownloadResponse($fileName,$content);
  }

  public function productAction()
  {
    $this->loadLayout();
    $this->getLayout()->getBlock('deal.edit.tab.product')
    ->setProduct($this->getRequest()->getPost('deal_product_id', null));
    $this->renderLayout();
  }

  public function productgridAction()
  {
    $this->loadLayout();
    $this->getLayout()->getBlock('deal.edit.tab.product')
    ->setProduct($this->getRequest()->getPost('deal_product_id', null));
    $this->renderLayout();
  } 

  public function gridAction()
  {
    $this->getResponse()->setBody(
    $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_grid')->toHtml()
    );
  }

  public function changeproductAction() {
    $product_id = $this->getRequest()->getParam('product_id');
    if($product_id) {
      $product = Mage::getModel('catalog/product')->load($product_id);
      $product_name = $product->getName();
      $product_name = str_replace('"','',$product_name);
      $product_name = str_replace("'",'',$product_name);
      $product_quantity = (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
      $html = '<input type="hidden" id="newproduct_name" name="newproduct_name" value="'. $product_name .'" >';
      $html .= '<input type="hidden" id="newproduct_price" name="newproduct_price" value="'. $product->getPrice() .'" >';
      $html .= '<input type="hidden" id="newproduct_quantity" name="newproduct_quantity" value="'. $product_quantity .'" >';
      $this->getResponse()->setHeader('Content-type', 'application/html');
      $this->getResponse()->setBody($html);       
    }
  }

  public function reportAction() {
    $this->loadLayout()
      ->_setActiveMenu('dailydeal/dailydeal');
    $this->_title($this->__('Daily Deal'))
      ->_title($this->__('Deal Report'));

    Mage::getSingleton('core/session')->setData('dailydeal_report_id',$this->getRequest()->getParam('id'));
    $this->renderLayout();
  }

}
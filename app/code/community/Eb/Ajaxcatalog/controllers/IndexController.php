<?php
class Eb_Ajaxcatalog_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $productCount   =   $this->getRequest()->getParam('limit');
        $category       =   $this->getRequest()->getParam('category');
        $blocks['cataloglistproduct']  =   $this->getLayout()->createBlock('athlete/product_list_featured')->setProductsCount($productCount)->setCategoryId($category)->setTemplate('ajaxcatalog/home/listjson.phtml')->toHtml();
        @header('Content-type: application/json');
        echo json_encode($blocks);
        exit;
    }
}
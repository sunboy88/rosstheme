<?php

/**
 * Catalog Search Controller
 */
require_once('Mage/CatalogSearch/controllers/ResultController.php');
class Eb_Ajaxcatalog_ResultController extends Mage_CatalogSearch_ResultController
{
    /**
     * Retrieve catalog session
     *
     * @return Mage_Catalog_Model_Session
     */
  
    /**
     * Display search result
     */
    public function indexAction()
    {  
	$query = Mage::helper('catalogsearch')->getQuery();
        /* @var $query Mage_CatalogSearch_Model_Query */

        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText()) {
            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            }
            else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                }
                else {
                    $query->setPopularity(1);
                }

                if ($query->getRedirect()){
                    $query->save();
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                }
                else {
                    $query->prepare();
                }
            }

            Mage::helper('catalogsearch')->checkNotes();

            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            // Check is enable modules
            if(Mage::helper('ajaxcatalog')->getConfigEnable()){
                if (!$this->getRequest()->getHeader('X-Requested-With')) {
                             $this->getLayout()->getBlock('search_result_list')->setTemplate('ajaxcatalog/product/list.phtml');
                             $this->getLayout()->getBlock('product_list_toolbar')->setTemplate('ajaxcatalog/product/list/toolbar.phtml');
                }else{
                     @header('Content-type: application/json');
                     $blocks['cataloglistproduct']	=	$this->getLayout()->getBlock('search_result_list')->setTemplate('ajaxcatalog/product/listgetjson.phtml')->toHtml();
                     $blocks['toolbar']      =       $this->getLayout()->getBlock('product_list_toolbar')->setTemplate('ajaxcatalog/product/list/toolbar.phtml')->toHtml();
                     echo json_encode($blocks);
                    exit;
                }
            }
           // $this->getLayout()->getBlock('search_result_list')->setTemplate('');
            $this->renderLayout();

            if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->save();
            }
        }
        else {
            $this->_redirectReferer();
        }
    }
}

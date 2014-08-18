<?php
require_once('Mage/Catalog/controllers/CategoryController.php');
class Eb_Ajaxcatalog_CategoryController extends Mage_Catalog_CategoryController{
	/**
     * Initialize requested category object
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCatagory()
    {
        Mage::dispatchEvent('catalog_controller_category_init_before', array('controller_action' => $this));
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            return false;
        }

        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);

        if (!Mage::helper('catalog/category')->canShow($category)) {
            return false;
        }
        Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());
        Mage::register('current_category', $category);
        try {
            Mage::dispatchEvent(
                'catalog_controller_category_init_after',
                array(
                    'category' => $category,
                    'controller_action' => $this
                )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $category;
    }


    /**
     * Category view action
     */
     public function viewAction()
    {
        if ($category = $this->_initCatagory()) {
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');

            if (!$category->hasChildren()) {
                $update->addHandle('catalog_category_layered_nochildren');
            }

            $this->addActionLayoutHandles();
            $update->addHandle($category->getLayoutUpdateHandle());
            $update->addHandle('CATEGORY_' . $category->getId());
            $this->loadLayoutUpdates();

            // apply custom layout update once layout is loaded
            if ($layoutUpdates = $settings->getLayoutUpdates()) {
                if (is_array($layoutUpdates)) {
                    foreach($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }

            $this->generateLayoutXml()->generateLayoutBlocks();
            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
            }

            if ($root = $this->getLayout()->getBlock('root')) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
	  //  $this->getLayout()->getBlock('product_list')->setTemplate('');
	   // Check is enable modules
            if(Mage::helper('ajaxcatalog')->getConfigEnable()){
                if (!$this->getRequest()->getHeader('X-Requested-With')) {
                             $this->getLayout()->getBlock('product_list')->setTemplate('ajaxcatalog/product/list.phtml');
                             $this->getLayout()->getBlock('product_list_toolbar')->setTemplate('ajaxcatalog/product/list/toolbar.phtml');
                }else{
                     @header('Content-type: application/json');
                     $blocks['cataloglistproduct']	=	$this->getLayout()->getBlock('product_list')->setTemplate('ajaxcatalog/product/listgetjson.phtml')->toHtml();
                     $blocks['toolbar']      =       $this->getLayout()->getBlock('product_list_toolbar')->setTemplate('ajaxcatalog/product/list/toolbar.phtml')->toHtml();
                     echo json_encode($blocks);
                    exit;
                }
            }
	//exit;
            $this->renderLayout();
        }
        elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }

}

?>

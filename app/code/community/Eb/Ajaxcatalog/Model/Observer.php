<?php class Eb_Ajaxcatalog_Model_Observer{

// Get all event control dispatch
    public function getListproduct(Varien_Event_Observer $event){   
        $controller = $event->getControllerAction();
		$param = array();
		$ajaxcatalog	=	 Mage::app()->getRequest()->getParam('ajaxcatalog');
		$layout = Mage::app()->getLayout();
		$blocks = array();
		if($ajaxcatalog==1){
		
			//$templ = $layout->getBlock('product_list')->setTemplate('ajaxcatalog/list.phtml');
			/*
			if($templ){
				//@header('Content-type: application/json');
				$blocks['toolbarlistproduct']	=	$templ->toHtml();
				echo json_encode($blocks);
				exit;
			}

			$templ = $layout->getBlock('search_result_list');
			if($templ){
				@header('Content-type: application/json');
				$blocks['toolbarlistproduct']	=	$templ->toHtml();
				echo json_encode($blocks);
			}
			exit;*/
		}
		
    }
	
} 


<?php
class Amasty_Base_Adminhtml_BaseController extends Mage_Adminhtml_Controller_Action
{
    public function downloadAction()
    {
        $fileName = "amasty_log_".date("YmdHis").".html";
        
        $html = Amasty_Base_Model_Conflicts::run(true);
        
        $this->_prepareDownloadResponse($fileName, $html);        
    }
    
    public function ajaxAction()
    {
       print Amasty_Base_Model_Conflicts::run(); 
    }
}  
?>
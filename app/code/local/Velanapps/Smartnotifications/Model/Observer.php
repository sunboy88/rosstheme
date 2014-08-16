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
  * @package    Smartnotifications
  * @author     Velan Team
  * @copyright  Copyright (c) 2013 Velan Info Services India Pvt Ltd. (http://www.velanapps.com)
  * @license    http://store.velanapps.com/License.txt
*/
   
class Velanapps_Smartnotifications_Model_Observer extends Mage_Core_Model_Abstract
{  
     
	/**
	Function for Activating Multi-Bar Extension with API.
	Input  : Observer with Activation Key.
	Output : Returns the Extension Activation.  
	 */
	public function registration($observer) 
	{    
		//Admin Session.
		$session = Mage::getSingleton('adminhtml/session');
		   try
		   {
				$activationCode = Mage::getStoreConfig('activation_tab/active_group/activation_key');
				
				if($activationCode)
				{      
					//Store Base Url Read in Helper.
					$baseUrl = Mage::helper('smartnotifications/data')->getStoreUrl();
				   
					//Removing index.php if found in base url.
					if(strpos($baseUrl,'index.php') !== false)
					{
						$getDomainName = explode('/index', $baseUrl);
						$domainName = $getDomainName[0];
					}
					else
					{
						$domainName = $baseUrl;
					}
					
					$serviceUrl = base64_decode('aHR0cDovL3N0b3JlLnZlbGFuYXBwcy5jb20vYWN0aXZhdGlvbi9yZWdpc3Rlci9tdWx0aWJhckFwaQ==');

					//Loading Curl for API Call.
					$curl = curl_init($serviceUrl);

					//Curl Input Parameters.
					$curlPostData = array("activation_code" => $activationCode, "domain_name" => $domainName);

					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPostData);                              
					$curlResponse = curl_exec($curl);						                                   
					curl_close($curl);
						
					   //Response is true from API.
					   if(strpos($curlResponse,'true') !== false)
					   { 
							$responseData = explode("::", $curlResponse);
							  
							$doc = new DOMDocument();
							  
							$doc->load($responseData[1]);
							
							$customSettings = $doc->getElementsByTagName($responseData[2]);
							
							$data = '1'; 
							foreach($customSettings as $multibarSettingsWrite)
							{
								$multibarSettingsWrite->getElementsByTagName($responseData[3])->item(0)->appendChild($doc->createTextNode($data));
								  
								$multibarSettingsWrite->getElementsByTagName($responseData[4])->item(0)->appendChild($doc->createTextNode($data));
									
								$multibarSettingsWrite->getElementsByTagName($responseData[5])->item(0)->appendChild($doc->createTextNode($data));
							}
								  
							 $doc->saveXML();
							 $doc->save($responseData[1]);

							 $session->addSuccess('Product activated.');                             
						}
						  else                            
						  {       
								  //Error message for In Valid activation key.
								  throw new Exception('Invalid activation key.');
						  }
					   
				}
				else
				{
				  //Error message for empty activation key submit.
				  throw new Exception('Please enter your activation key to complete the registration process.');
				}
		  }
		  catch(Mage_Core_Exception $e) 
		  {
				  foreach(explode("\n", $e->getMessage()) as $message) 
				  {       
						 $session->addError($message);
				  }
		  }
		  
		return;

	}
}    
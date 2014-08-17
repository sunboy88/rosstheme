<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Geoip
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Geoip Model
 * 
 * @category    Magestore
 * @package     Magestore_Geoip
 * @author      Magestore Developer
 */
class Magestore_Onestepcheckout_Model_Country extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('onestepcheckout/country');
    }
	public function import()
	{
		$data = $this->getData();								
		if($data['country']){
			$data['country'] = trim($data['country'], ' ');
			$data['country'] = trim($data['country'], '"');
		}
		if($data['first_ip']){
			$data['first_ip'] = trim($data['first_ip'], ' ');
			$data['first_ip'] = trim($data['first_ip'], '"');
			$firstIp = $this->convertIpToInteger($data['first_ip']);			
			$data['first_ip_number'] = $firstIp['ip'];
			$data['first_ip_number_lower'] = $firstIp['ip_lower'];
		}
		if($data['last_ip']){
			$data['last_ip'] = trim($data['last_ip'], ' ');
			$data['last_ip'] = trim($data['last_ip'], '"');
			$lastIp = $this->convertIpToInteger($data['last_ip']);
			$data['last_ip_number'] = $lastIp['ip'];
			$data['last_ip_number_lower'] = $lastIp['ip_lower'];
		}
		
		//check exited row
		if(!$data['country']){
			return false;				
		}else{			
			$collection = $this->getCollection()						
							->addFieldToFilter('country',$data['country'])
							->addFieldToFilter('first_ip_number',$data['first_ip_number'])
							->addFieldToFilter('last_ip_number',$data['last_ip_number'])												
							;        
		}
		
		if(count($collection))
			return false;			
			
		$this->setData($data);
		$this->save();
		
		return $this->getId();
	}
	
	public function convertIpToInteger($ipAddress)
	{		
		$ipInteger = 0;
		$ipIntegerLower = 0;
		$ip = explode('.', $ipAddress);
		if(count($ip) == 4){
			$ipInteger = (int)$ip[0]*pow(2,24) + (int)$ip[1]*pow(2,16) + (int)$ip[2]*pow(2,8) + (int)$ip[3];			
			$ipIntegerLower = 0;			
		}else{			
			$ip = explode(':', $ipAddress);
			$ip[0] = isset($ip[0]) ? $ip[0] : 0; 
			$ip[1] = isset($ip[1]) ? $ip[1] : 0; 
			$ip[2] = isset($ip[2]) ? $ip[2] : 0; 
			$ip[3] = isset($ip[3]) ? $ip[3] : 0; 
			$ip[4] = isset($ip[4]) ? $ip[4] : 0; 
			$ip[5] = isset($ip[5]) ? $ip[5] : 0; 
			$ip[6] = isset($ip[6]) ? $ip[6] : 0; 
			$ip[7] = isset($ip[7]) ? $ip[7] : 0; 			
			$ipInteger = bcadd(bcadd(bcmul(hexdec($ip[0]), bcpow(2, 48)),bcmul(hexdec($ip[1]), bcpow(2, 32))),bcadd(bcmul(hexdec($ip[2]), bcpow(2, 16)),hexdec($ip[3])));
			$ipIntegerLower = bcadd(bcadd(bcmul(hexdec($ip[4]), bcpow(2, 48)),bcmul(hexdec($ip[5]), bcpow(2, 32))),bcadd(bcmul(hexdec($ip[6]), bcpow(2, 16)),hexdec($ip[7])));
		}		
		return array('ip'       => $ipInteger, 
					 'ip_lower' => $ipIntegerLower
					);
	}
	
}
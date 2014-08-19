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
  * @copyright  Copyright (c) 2013  Velan Info Services India Pvt Ltd. (http://www.velanapps.com)
  * @license    http://store.velanapps.com/License.txt
*/


class Velanapps_Smartnotifications_Model_Dropdown_Align
{
	
	/**
	Function for getting Position Left-Bottom/Right-Bottom status.
    Input  : Being called, no specific input given.
    Output : Returns Position Left-Bottom/Right-Bottom status.
     */
    public function toOptionArray(){
	
        return array(
            array(
                'value' => 'left',
                'label' => 'Left-Bottom',
            ),
            array(
                'value' => 'right',
                'label' => 'Right-Bottom',
            ),
			
        );
    }
}
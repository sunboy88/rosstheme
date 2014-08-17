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
 * Onestepcheckout Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Onestepcheckout
 * @author      Magestore Developer
 */
class Magestore_Onestepcheckout_Block_Adminhtml_Country_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
     protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('import_form', array('legend'=>Mage::helper('onestepcheckout')->__('Upload File')));
     
      $fieldset->addField('csv_country', 'file', array(
          'label'     => Mage::helper('onestepcheckout')->__('CSV File'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'csv_country',
		  'note'	  => Mage::helper('onestepcheckout')->__("Use Zip code database only to avoid error
						<br/>Link download").": <a href='http://www.magestore.com/geoip-databases.html'>Here</a>",
      ));
	  
      return parent::_prepareForm();
  }
}
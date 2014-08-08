<?php 
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Dailydeal_Edit_Tab_Renderer_Renderer
extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 public function render(Varien_Object $row){
  $product = Mage::getModel('catalog/product')->load($row->getProductId());
   return sprintf('
    <a href="%s" title="%s">%s</a>',
    $this->getUrl('admin/catalog_product/edit/', array('_current'=>true, 'id' => $row->getProductId())),
    Mage::helper('socialvoice')->__('View Product Detail'),
    $product->getName()
   ); 
 }
}
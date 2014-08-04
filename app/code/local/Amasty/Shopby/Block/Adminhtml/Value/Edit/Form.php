<?php
/**
* @copyright Amasty.
*/ 
class Amasty_Shopby_Block_Adminhtml_Value_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        //create form structure
        $form = new Varien_Data_Form(array(
          'id' => 'edit_form',
          'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
          'method' => 'post',
          'enctype' => 'multipart/form-data')
         );
        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        $hlp = Mage::helper('amshopby');
        $model = Mage::registry('amshopby_value');
        
        $fldSet = $form->addFieldset('set', array('legend'=> $hlp->__('General')));
        $fldSet->addField('is_featured', 'select', array(
            'label'     => $hlp->__('Featured'),
            'name'      => 'is_featured',
            'values'    => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('catalog')->__('No')
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('catalog')->__('Yes')
                ),
            ),
            'onchange'  => 'featured(this)', 
        ));         
        
        $fldSet->addField('featured_order', 'text', array(
            'label'     => $hlp->__('Featured Order'),
            'name'      => 'featured_order',
        ));

        
        $fldMain = $form->addFieldset('main', array('legend'=> $hlp->__('Products List Page')));
        $fldMain->addField('title', 'text', array(
          'label'     => $hlp->__('Title'),
          'name'      => 'title',
        ));
        $fldMain->addField('url_alias', 'text', array(
            'label'     => $hlp->__('URL Alias'),
            'name'      => 'url_alias',
            'required'  => false,
        ));
        $fldMain->addField('descr', 'textarea', array(
          'label'     => $hlp->__('Description'),
          'name'      => 'descr',
        ));
        $fldMain->addField('cms_block', 'text', array(
          'label'     => $hlp->__('CMS Block'),
          'name'      => 'cms_block',
        ));
        $fldMain->addField('meta_title', 'text', array(
          'label'     => $hlp->__('Page Title Tag'),
          'name'      => 'meta_title',
        ));
        $fldMain->addField('meta_descr', 'text', array(
          'label'     => $hlp->__('Meta-Description Tag'),
          'name'      => 'meta_descr',
        ));
        $fldMain->addField('meta_kw', 'text', array(
          'label' => $hlp->__('Meta-Keyword Tag'),
          'name' => 'meta_kw',
        ));

        $fldMain->addField('img_big', 'file', array(
            'label'     => $hlp->__('Image'),
            'name'      => 'img_big',
            'required'  => false,
            'after_element_html' => $this->getImageHtml($model->getImgBig()), 
        )); 
        $fldMain->addField('remove_img_big', 'checkbox', array(
            'label'     => $hlp->__('Remove Image'),
            'name'      => 'remove_img_big',
            'value'     => 1,
        )); 
        
        
        $fldView = $form->addFieldset('view', array('legend'=> $hlp->__('Product View Page')));
        $fldView->addField('img_medium', 'file', array(
            'label'     => $hlp->__('Image'),
            'name'      => 'img_medium',
            'required'  => false,
            'after_element_html' => $this->getImageHtml($model->getImgMedium()), 
        )); 
        $fldView->addField('remove_img_medium', 'checkbox', array(
            'label'     => $hlp->__('Remove Image'),
            'name'      => 'remove_img_medium',
            'value'     => 1,
        )); 
        

        $fldNav = $form->addFieldset('navigation', array('legend'=> $hlp->__('Layered Navigation')));
        $fldNav->addField('img_small', 'file', array(
            'label'     => $hlp->__('Image'),
            'name'      => 'img_small',
            'required'  => false,
            'after_element_html' => $this->getImageHtml($model->getImgSmall()), 
        )); 
        $fldNav->addField('remove_img_small', 'checkbox', array(
            'label'     => $hlp->__('Remove Image'),
            'name'      => 'remove_img_small',
            'value'     => 1,
        )); 
        
        $fldNav->addField('img_small_hover', 'file', array(
            'label'     => $hlp->__('Image On Hover'),
            'name'      => 'img_small_hover',
            'required'  => false,
            'after_element_html' => $this->getImageHtml($model->getImgSmallHover()), 
        )); 
        $fldNav->addField('remove_img_small_hover', 'checkbox', array(
            'label'     => $hlp->__('Remove Image'),
            'name'      => 'remove_img_small_hover',
            'value'     => 1,
        )); 
        

        
        //set form values
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        if ($data) {
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }
        elseif ($model) {
            $form->setValues($model->getData());
        }
        
        return parent::_prepareForm();
    }
  
    private function getImageHtml($img)
    {
        $html = '';
        if ($img){
            $html .= '<p style="margin-top: 5px">';
            $html .= '<img src="'.Mage::getBaseUrl('media') . 'amshopby/' . $img .'" />';
            $html .= '</p>';
        } 
        return $html;     
    }
}
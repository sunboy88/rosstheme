<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Block_Adminhtml_Filter_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Filter form
     * @var Varien_Data_Form
     */
    protected $_form;
    
    protected function _prepareForm()
    {
        //create form structure
        $this->_form = new Varien_Data_Form();
        $this->setForm($this->_form);

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence'));
        
        $model = Mage::registry('amshopby_filter');
        
        $this->_prepareRegularForm();
        
        //set form values
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        
        if ($data) {
            $this->_form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }
        elseif ($model) {
            $this->_form->setValues($model->getData());
        }
        
        return parent::_prepareForm();
    }

    protected function _prepareRegularForm()
    {
        $yesno = array($this->__('No'), $this->__('Yes'));
        
        $model = Mage::registry('amshopby_filter');

        $this->_prepareFieldsetGeneral($model);
        
        $fldSet2 = $this->_form->addFieldset('amshopby_blocks', array('legend'=> $this->__('Additional Blocks')));
        $fldSet2->addField('show_on_list', 'select', array(
            'label'     => $this->__('Show on List'),
            'name'      => 'show_on_list',
            'values'    => $yesno,
            'note'      => $this->__('Show option description and image above product listing'),
        ));
       
        $fldSet2->addField('show_on_view', 'select', array(
            'label'     => $this->__('Show on Product'),
            'name'      => 'show_on_view',
            'values'    => $yesno,
            'note'      => $this->__('Show options images block on product view page'),
        ));

        $fldSet3 = $this->_form->addFieldset('amshopby_seo', array('legend'=> $this->__('Search Engines Optimization')));        
        $fldSet3->addField('seo_nofollow', 'select', array(
            'label'     => $this->__('Robots NoFollow Tag'),
            'name'      => 'seo_nofollow',
            'values'    => $yesno,
        ));        
        $fldSet3->addField('seo_noindex', 'select', array(
            'label'     => $this->__('Robots NoIndex Tag'),
            'name'      => 'seo_noindex',
            'values'    => $yesno,
        ));
        $fldSet3->addField('seo_rel', 'select', array(
            'label'     => $this->__('Rel NoFollow'),
            'name'      => 'seo_rel',
            'values'    => $yesno,
            'note'      => $this->__('For the links in the left navigation'),
        ));

        $fldSet4 = $this->_form->addFieldset('amshopby_special', array('legend'=> $this->__('Special Cases')));

        $fldSet4->addField('include_in', 'text', array(
            'label'     => $this->__('Include Only In Categories'),
            'name'      => 'include_in',
            'note'      => $this->__('Comma separated list of the categories IDs like 17,4,25'),
        ));

        $fldSet4->addField('exclude_from', 'text', array(
            'label'     => $this->__('Exclude From Categories'),
            'name'      => 'exclude_from',
            'note'      => $this->__('Comma separated list of the categories IDs like 17,4,25'),
        ));
        
        $fldSet4->addField('single_choice', 'select', array(
            'label'     => $this->__('Single Choice Only'),
            'name'      => 'single_choice',
            'values'    => $yesno,
            'note'      => $this->__('Disables multiple selection'),
        ));

        $fldSet4->addField('depend_on', 'text', array(
            'label'     => $this->__('Show only when one of the following options are selected'),
            'name'      => 'depend_on',
            'note'      => $this->__('Comma separated list of the option IDs'),
        ));

        $fldSet4->addField('depend_on_attribute', 'text', array(
            'label'     => $this->__('Show only when any options of attributes below is selected'),
            'name'      => 'depend_on_attribute',
            'note'      => $this->__('Comma separated list of the attribute codes like color, brand etc'),
        ));
    }

    protected function _prepareFieldsetGeneral(Amasty_Shopby_Model_Filter $model)
    {
        $fldSet = $this->_form->addFieldset('amshopby_general', array('legend'=> $this->__('Display Properties')));
        $yesno = array($this->__('No'), $this->__('Yes'));
        $isDecimal = $model->getBackendType() == 'decimal';

        $fldSet->addField('block_pos', 'select', array(
            'label'     => $this->__('Show in the Block'),
            'name'      => 'block_pos',
            'values'    => Mage::getModel('amshopby/source_position')->toOptionArray(),
        ));

        $fldSet->addField('display_type', 'select', array(
            'label'     => $this->__('Display Type'),
            'name'      => 'display_type',
            'values'    => $model->getDisplayTypeOptionsSource()->toOptionArray(),
        ));

        if ($isDecimal) {
            $fldSet->addField('slider_type', 'select', array(
                'label'     => $this->__('Slider Type'),
                'name'      => 'slider_type',
                'values'    => Mage::getModel('amshopby/source_slider')->toOptionArray(),
            ));

            $fldSet->addField('slider_decimal', 'text', array(
                'label'     => $this->__('Number of digits after comma'),
                'name'      => 'slider_decimal',
            ));


            $fldSet->addField('range', 'text', array(
                'label'     => $this->__('Range Step'),
                'name'      => 'range',
                'note'      => $this->__('Set 10 to get ranges 10-20,20-30, etc. Custom value improves pages speed. Leave empty to get default ranges.'),
            ));

            $fldSet->addField('from_to_widget', 'select', array(
                'label'     => $this->__('Show From-To Widget'),
                'name'      => 'from_to_widget',
                'values'    => $yesno,
            ));

            $fldSet->addField('value_label', 'text', array(
                'label'     => $this->__('Units label'),
                'name'      => 'value_label',
                'note'      => $this->__('Specify attribute units, like inch., MB, px, ft etc.'),
            ));
        }
        else {
            $fldSet->addField('show_search', 'select', array(
                'label'     => $this->__('Show Search Box'),
                'name'      => 'show_search',
                'values'    => $yesno,
            ));

            $fldSet->addField('max_options', 'text', array(
                'label'     => $this->__('Number of unfolded options'),
                'name'      => 'max_options',
                'note'      => $this->__('Applicable for `Labels Only`, `Images only` and `Labels and Images` display types. Zero means all options are unfolded')
            ));
        }


        $fldSet->addField('hide_counts', 'select', array(
            'label'     => $this->__('Hide quantities'),
            'name'      => 'hide_counts',
            'values'    => $yesno
        ));

        $fldSet->addField('sort_by', 'select', array(
            'label'     => $this->__('Sort Options By'),
            'name'      => 'sort_by',
            'values'    => array(
                array(
                    'value' => 0,
                    'label' => $this->__('Position')
                ),
                array(
                    'value' => 1,
                    'label' => $this->__('Name')
                ),
                array(
                    'value' => 2,
                    'label' => $this->__('Product Quatities')
                )),
        ));

        $fldSet->addField('collapsed', 'select', array(
            'label'     => $this->__('Collapsed'),
            'name'      => 'collapsed',
            'values'    => $yesno,
            'note'      => $this->__('Will be collapsed until customer select any filter option'),
        ));

        $fldSet->addField('comment', 'text', array(
            'label'     => $this->__('Tooltip'),
            'name'      => 'comment',
        ));

        $this->_getDependencyMapper()
            ->addFieldMap('display_type', 'display_type')
            ->addFieldMap('slider_type', 'slider_type')
            ->addFieldMap('slider_decimal', 'slider_decimal')
            ->addFieldDependence('slider_type', 'display_type', Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_SLIDER)
            ->addFieldDependence('slider_decimal', 'display_type', Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_SLIDER)
        ;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form_Element_Dependence
     */
    protected function _getDependencyMapper()
    {
        return $this->getChild('form_after');

    }
}
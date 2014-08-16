<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productquestions
 * @version    1.5.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Productquestions_Block_Adminhtml_Productquestions_Add extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_mode = 'add';
        $this->_objectId = 'id';
        $this->_blockGroup = 'productquestions';
        $this->_controller = 'adminhtml_productquestions';

        $this->_updateButton('save', 'label', $this->__('Save'));
        $this->_updateButton('delete', 'label', $this->__('Delete'));

        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('reset', 'id', 'reset_button');

        $this->_addButton('saveandcontinue', array(
            'label' => $this->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'id' => 'saveandcontinue_button',
            'class' => 'save',
        ), -100);

        $this->_formInitScripts[] = '
            //<![CDATA[
            function toggleEditor() {
                if (tinyMCE.getInstanceById("productquestions_content") == null) {
                    tinyMCE.execCommand("mceAddControl", false, "productquestions_content");
                } else {
                    tinyMCE.execCommand("mceRemoveControl", false, "productquestions_content");
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($("edit_form").action+"back/1/");
            }

            var productquestions = function() {
                return {
                    productInfoUrl : null,
                    formHidden : true,

                    gridRowClick : function(data, click) {
                        if(Event.findElement(click,\'TR\').title){
                            productquestions.productInfoUrl = Event.findElement(click,\'TR\').title;
                            productquestions.loadProductData();
                            productquestions.formHidden = false;
                        }
                    },

                    loadProductData : function() {
                        var con = new Ext.lib.Ajax.request(\'POST\', productquestions.productInfoUrl, {success:productquestions.reqSuccess,failure:productquestions.reqFailure}, {form_key:FORM_KEY});
                    },

                    showForm : function() {
                    toggleVis("productquestions_form");
                    toggleVis("save_button");
                    toggleVis("saveandcontinue_button");
                    toggleVis("reset_button");
                    toggleVis("productGrid");
                    },

                    reqSuccess :function(o) {
                        var response = Ext.util.JSON.decode(o.responseText);
                        if( response.error ) {
                            alert(response.message);
                        } else if( response.id ){
                            $("question_product_name").value = response.name;
                            $("question_product_id").value = response.id;
                            $("question_product_link").innerHTML = \'<a href="' . Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/catalog_product/edit') . 'id/\' + response.id + \'" target="_blank">\' + response.name + \'</a>\';
                            productquestions.showForm();
                        } else if( response.message ) {
                            alert(response.message);
                        }
                    }
                }
            }();
           //]]>
        ';

        $this->_formScripts[] = '
           toggleVis("productquestions_form");
           toggleVis("save_button");
           toggleVis("saveandcontinue_button");
           toggleVis("reset_button");
        ';
    }

    public function getHeaderText()
    {
        return $this->__('New Product Questions');
    }

}

<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Collpur_Block_Adminhtml_Deal_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        $this->_objectId = 'id';
        $this->_blockGroup = 'collpur';
        $this->_controller = 'adminhtml_deal';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('collpur')->__('Save Deal'));
        $this->_updateButton('delete', 'label', Mage::helper('collpur')->__('Delete Deal'));

        $url = $this->_getSaveAndContinueUrl();
        $this->_formScripts[] = "
                document.observe('dom:loaded', function() {
                    var awcp_global_state = false;
                    Event.observe('couponGrid_massaction-select','change',function() {
                      if(!awcp_global_state) {
                        couponGrid_massactionJsObject.getItems().status.url += 'tab/' + collpur_info_tabsJsTabs.activeTab.id + '/';
                        couponGrid_massactionJsObject.getItems().delete.url += 'tab/' + collpur_info_tabsJsTabs.activeTab.id + '/';
                         awcp_global_state = true;
                      }
                    });
                });

                 function saveAndContinueEdit(url) {
                           editForm.submit(
                                url.replace(/{{tab_id}}/ig,collpur_info_tabsJsTabs.activeTab.id)
                           );
                    }
             ";
    }

    protected function _prepareLayout() {

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }


        $deal = Mage::registry('collpur_deal');

        if ($deal->isOpen()) {
            $this->_removeButton('delete');

            if(!$deal->getData('is_success')) {
                $closeFailedJs = 'deleteConfirm(\''
                        . Mage::helper('collpur')->__('Are you sure? All purchases, connected to this deal, will be refunded')
                        . '\', \'' . $this->getUrl('collpur_admin/adminhtml_deal/closeAsFailed', array('id' => $deal->getId())) . '\');';

                $this->_addButton('close_as_failed', array(
                    'label' => $this->__('Close as Failed'),
                    'onclick' => $closeFailedJs
                        )
                );
            }          

            if ($deal->getData('is_success')) {
                $closeSuccessJs = 'deleteConfirm(\''
                        . Mage::helper('collpur')->__('Are you sure?')
                        . '\', \'' . $this->getUrl('collpur_admin/adminhtml_deal/closeAsSuccess', array('id' => $deal->getId())) . '\');';

                $this->_addButton('close_as_success', array(
                    'label' => $this->__('Close as Success'),
                    'onclick' => $closeSuccessJs
                        )
                );
            }
        }

        if ($deal->isOpen() || !$deal->getId()) {
            $this->_addButton('save_and_continue', array(
                'label' => Mage::helper('customer')->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit(\'' . $this->_getSaveAndContinueUrl() . '\')',
                'class' => 'save'
              ), 10);
        }


        if ($deal->isClosed()) {
            $archiveJs = 'deleteConfirm(\''
                    . Mage::helper('collpur')->__('Are you sure?')
                    . '\', \'' . $this->getUrl('collpur_admin/adminhtml_deal/archive', array('id' => $deal->getId())) . '\');';

            $this->_addButton('archive', array(
                'label' => $this->__('Archive'),
                'onclick' => $archiveJs
                    )
            ); 

            $reopenJs = 'deleteConfirm(\''
                    . Mage::helper('collpur')->__('Are you sure?')
                    . '\', \'' . $this->getUrl('collpur_admin/adminhtml_deal/reopen', array('id' => $deal->getId())) . '\');';

            $this->_addButton('reopen', array(
                'label' => $this->__('Reopen'),
                'onclick' => $reopenJs
                    )
            );
        }

        if ($deal->isClosed() || $deal->isArchived()) {
            $this->_removeButton('save');
            $this->_removeButton('save_and_edit_button');
        }

        return parent::_prepareLayout();
    }

    public function getHeaderText() {
        $deal = Mage::registry('collpur_deal');
        if ($deal->getId()) {
            if ($deal->getName()) {
                return Mage::helper('collpur')->__("Edit Deal '%s'", $this->htmlEscape($deal->getName()));
            }
            return Mage::helper('collpur')->__("Edit Deal #'%s'", $this->htmlEscape($deal->getId()));
        } else {
            return Mage::helper('collpur')->__('Create New Deal');
        }
    }

    protected function _getSaveAndContinueUrl() {
        return $this->getUrl('*/*/save', array(
            '_current' => true,
            'back' => 'edit',
            'tab' => '{{tab_id}}'
        ));
    }

}

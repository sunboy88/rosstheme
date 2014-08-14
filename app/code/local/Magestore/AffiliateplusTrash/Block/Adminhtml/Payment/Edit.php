<?php

class Magestore_AffiliateplusTrash_Block_Adminhtml_Payment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'affiliateplustrash';
        $this->_controller = 'adminhtml_payment';
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_removeButton('delete');
        
        $data = Mage::registry('payment_data');
        $this->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/deleted') . '\')');
        $this->addButton('restore', array(
            'label'     => Mage::helper('affiliateplustrash')->__('Restore'),
            'onclick'   => 'deleteConfirm(\''
                        . Mage::helper('affiliateplustrash')->__('Restore deleted transaction. Are you sure?')
                        . '\', \''
                        . $this->getUrl('*/*/restore', array('id' => $data->getId()))
                        . '\')',
            'class'     => 'save'
        ), 0);
    }

    public function getHeaderText() {
        $data = Mage::registry('payment_data');
        return Mage::helper('affiliateplus')->__("View Withdrawal for '%s'", $this->htmlEscape($data->getAccountName()));
    }
}

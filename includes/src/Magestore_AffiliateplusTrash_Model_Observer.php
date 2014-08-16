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
 * @package     Magestore_AffiliateplusTrash
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusTrash Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusTrash
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusTrash_Model_Observer
{
    /**
     * add massaction for transaction grid
     * @param type $observer
     */
    public function adminhtmlAddMassactionTransactionGrid($observer)
    {
        $grid = $observer['grid'];
        $grid->getMassactionBlock()->addItem('delete', array(
            'label'     => Mage::helper('affiliateplustrash')->__('Move to Trash'),
            'url'       => $grid->getUrl('affiliateplustrashadmin/*/massDelete'),
            'confirm'   => Mage::helper('affiliateplustrash')->__('Are you sure?'),
        ));
    }
    
    /**
     * update transaction edit form action button
     * 
     * @param type $observer
     */
    public function adminhtmlUpdateTransactionAction($observer)
    {
        $transaction = $observer['transaction'];
        $form = $observer['form'];
        
        if ($transaction->canRestore()) {
            $form->addButton('restore', array(
                'label'     => Mage::helper('affiliateplustrash')->__('Restore'),
                'onclick'   => 'deleteConfirm(\''
                            . Mage::helper('affiliateplustrash')->__('Restore deleted transaction. Are you sure?')
                            . '\', \''
                            . $form->getUrl('affiliateplustrashadmin/*/restore', array('id' => $transaction->getId()))
                            . '\')',
                'class'     => 'save'
            ), 0);
            // update back button to deleted transaction
            $form->updateButton('back', 'onclick', 'setLocation(\'' . $form->getUrl('affiliateplustrashadmin/*/deleted') . '\')');
        } else if ($transaction->getData('transaction_is_can_delete')) {
            $form->addButton('restore', array(
                'label'     => Mage::helper('adminhtml')->__('Move to Trash'),
                'onclick'   => 'deleteConfirm(\''
                            . Mage::helper('affiliateplustrash')->__('Are you sure?')
                            . '\', \''
                            . $form->getUrl('affiliateplustrashadmin/*/delete', array('id' => $transaction->getId()))
                            . '\')',
                'class'     => 'delete'
            ), 0);
        }
    }
    
    /**
     * update mass action for Payment
     * 
     * @param type $observer
     */
    public function adminhtmlPaymentMassaction($observer)
    {
        $grid = $observer['grid'];
        $grid->setMassactionIdField('payment_id');
        $grid->getMassactionBlock()->setFormFieldName('payment');
        $grid->getMassactionBlock()->addItem('delete', array(
            'label'     => Mage::helper('affiliateplustrash')->__('Move to Trash'),
            'url'       => $grid->getUrl('affiliateplustrashadmin/*/massDelete'),
            'confirm'   => Mage::helper('affiliateplustrash')->__('Are you sure?'),
        ));
    }
    
    /**
     * update payment form action
     * 
     * @param type $observer
     */
    public function adminhtmlPaymentEditFormAction($observer)
    {
        $form = $observer['form'];
        $data = $observer['data'];
        if ($data->canRestore()) {
            $form->removeButton('cancel');
            $form->removeButton('complete');
            $form->removeButton('save_and_pay_manual');
            $data->setData(array());
        } else {
            $form->addButton('restore', array(
                'label'     => Mage::helper('adminhtml')->__('Move to Trash'),
                'onclick'   => 'deleteConfirm(\''
                            . Mage::helper('affiliateplustrash')->__('Are you sure?')
                            . '\', \''
                            . $form->getUrl('affiliateplustrashadmin/*/delete', array('id' => $data->getId()))
                            . '\')',
                'class'     => 'delete'
            ), 0);
        }
    }
}

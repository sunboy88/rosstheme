<?php

class Belvg_Referralreward_Block_Adminhtml_Points_Edit_Tab_Info extends Belvg_Referralreward_Block_Invite
{
    public function __construct()
    {        
        parent::__construct();

        $this->setTemplate('belvg/referralreward/info.phtml');
    }

    public function getAllFriends()
    {
        $customerId = (int) $this->getRequest()->getParam('id');

        return Mage::getModel('referralreward/friends')
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId);
    }
}
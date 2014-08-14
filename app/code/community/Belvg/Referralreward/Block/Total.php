<?php

class Belvg_Referralreward_Block_Total extends Mage_Core_Block_Template
{
    /**
     * Get label cell tag properties
     *
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get value cell tag properties
     *
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize reward points totals
     *
     * @return Enterprise_Reward_Block_Sales_Order_Total
     */
    public function initTotals()
    {
        if ((float) $this->getOrder()->getBaseReferralrewardAmount()) {
            $source = $this->getSource();
            $value  = $source->getReferralrewardAmount();

            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'fee',
                'strong' => false,
                'label'  => Mage::helper('referralreward')->__('Points Discount'),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value,
            )));
        }

        return $this;
    }
}

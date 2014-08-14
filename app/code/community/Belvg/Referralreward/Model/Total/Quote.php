<?php

class Belvg_Referralreward_Model_Total_Quote extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('referralreward');
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('referralreward')->getLabelTotal();
    }

    /**
     * Collect totals information about insurance
     *
     * @param Mage_Sales_Model_Quote_Address $address
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);

		$this->_setAmount(0);
		$this->_setBaseAmount(0);

        if (($address->getAddressType() == 'billing')) {
            return $this;
        }

        $helper = Mage::helper('referralreward');
        $points = (int) Mage::getSingleton('core/session')->getPointsDiscount();
        //if ($points) {
            $quote        = $address->getQuote();
            $exist_amount = $quote->getReferralrewardAmount();
            $amount       = -1 * $helper->convertPoints($points);
            //if ($amount != 0) {
                $balance  = $amount - $exist_amount;

                $address->setReferralrewardAmount($balance);
                $address->setBaseReferralrewardAmount($balance);
         
                $quote->setReferralrewardAmount($balance);
         
                $address->setGrandTotal($address->getGrandTotal() + $address->getReferralrewardAmount());
                $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseReferralrewardAmount());
            //}
        //}

        /*if ($amount) {
            $this->_addAmount($amount);
            $this->_addBaseAmount($amount);
        }*/

        return $this;
    }

    /**
     * Add giftcard totals information to address object
     *
     * @param Mage_Sales_Model_Quote_Address $address
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if (($address->getAddressType() == 'billing')) {
            //$amount = $address->getReferralrewardAmount();
            $helper = Mage::helper('referralreward');
            $points = (int) Mage::getSingleton('core/session')->getPointsDiscount();
            $amount = -1 * $helper->convertPoints($points);
            if ($amount != 0) {
                $address->addTotal(array(
                    'code'  => $this->getCode(),
                    'title' => $this->getLabel(),
                    'value' => $amount
                ));
            }
        }

        return $this;
    }

}
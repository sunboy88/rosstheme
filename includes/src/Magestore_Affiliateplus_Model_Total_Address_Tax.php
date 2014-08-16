<?php

class Magestore_Affiliateplus_Model_Total_Address_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax
{
    /**
     * Calculate tax for Quote (total)
     * 
     * @param type $item
     * @param type $rate
     * @param type $taxGroups
     * @return Magestore_Customerreward_Model_Total_Quote_Tax
     */
    protected function _aggregateTaxPerRate($item, $rate, &$taxGroups, $taxId = null, $recalculateRowTotalInclTax = false) {
        $discount       = $item->getDiscountAmount();
        $baseDiscount   = $item->getBaseDiscountAmount();
        /* hainh add this for calculating discount base on incl or excl tax price 22-04-2014 */
        if (!$this->_discountIncludeTax()) {
            $item->setDiscountAmount($discount + $item->getAffiliateplusAmount() + $item->getCustomerrewardAmount());
            $item->setBaseDiscountAmount($baseDiscount + $item->getBaseAffiliateplusAmount() + $item->getBaseCustomerrewardAmount());
        }
        parent::_aggregateTaxPerRate($item, $rate, $taxGroups, $taxId, $recalculateRowTotalInclTax);
        
        $item->setDiscountAmount($discount);
        $item->setBaseDiscountAmount($baseDiscount);
        return $this;
    }
    
    /**
     * Calculate tax for each product
     * 
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param type $rate
     * @return Magestore_Customerreward_Model_Total_Quote_Tax
     */
    protected function _calcUnitTaxAmount(Mage_Sales_Model_Quote_Item_Abstract $item, $rate, &$taxGroups = null, $taxId = null, $recalculateRowTotalInclTax = false) {
        $discount       = $item->getDiscountAmount();
        $baseDiscount   = $item->getBaseDiscountAmount();
        /* hainh add this for calculating discount base on incl or excl tax price 22-04-2014 */
        if (!$this->_discountIncludeTax()) {
            $item->setDiscountAmount($discount + $item->getAffiliateplusAmount() + $item->getCustomerrewardAmount());
            $item->setBaseDiscountAmount($baseDiscount + $item->getBaseAffiliateplusAmount() + $item->getBaseCustomerrewardAmount());
        }
        parent::_calcUnitTaxAmount($item, $rate, $taxGroups, $taxId, $recalculateRowTotalInclTax);
        
        $item->setDiscountAmount($discount);
        $item->setBaseDiscountAmount($baseDiscount);
        return $this;
    }
    
    /**
     * Calculate tax for each item
     * 
     * @param type $item
     * @param type $rate
     * @return Magestore_Customerreward_Model_Total_Quote_Tax
     */
    protected function _calcRowTaxAmount($item, $rate, &$taxGroups = null, $taxId = null, $recalculateRowTotalInclTax = false) {
        $discount       = $item->getDiscountAmount();
        $baseDiscount   = $item->getBaseDiscountAmount();
        /* hainh add this for calculating discount base on incl or excl tax price 22-04-2014 */
        if (!$this->_discountIncludeTax()) {
            $item->setDiscountAmount($discount + $item->getAffiliateplusAmount() + $item->getCustomerrewardAmount());
            $item->setBaseDiscountAmount($baseDiscount + $item->getBaseAffiliateplusAmount() + $item->getBaseCustomerrewardAmount());
        }
        parent::_calcRowTaxAmount($item, $rate, $taxGroups, $taxId, $recalculateRowTotalInclTax);
        
        $item->setDiscountAmount($discount);
        $item->setBaseDiscountAmount($baseDiscount);
        return $this;
    }
    
    /**
     * Calculate tax for shipping amount
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @param type $taxRateRequest
     */
    protected function _calculateShippingTax(Mage_Sales_Model_Quote_Address $address, $taxRateRequest) {
        $discount       = $address->getShippingDiscountAmount();
        $baseDiscount   = $address->getBaseShippingDiscountAmount();
        /* hainh add this for calculating discount base on incl or excl tax price 22-04-2014 */
        if (!$this->_discountIncludeTax()) {
            $address->setShippingDiscountAmount($discount + $address->getCustomerrewardAmount());
            $address->setBaseShippingDiscountAmount($baseDiscount + $address->getBaseCustomerrewardAmount());
        }
        parent::_calculateShippingTax($address, $taxRateRequest);
        
        $address->setShippingDiscountAmount($discount);
        $address->setBaseShippingDiscountAmount($baseDiscount);
        return $this;
    }
    
     /* hainh add this for calculating discount base on incl or excl tax price 22-04-2014 */
    protected function _discountIncludeTax() {
        if ((int) Mage::getStoreConfig('tax/calculation/discount_tax'))
            return true;
        else
            return false;
    }
    
}

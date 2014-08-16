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
 * Rewrite Transaction Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusTrash
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusTrash_Model_Mysql4_Transaction_Collection
    extends Magestore_Affiliateplus_Model_Mysql4_Transaction_Collection
{
    protected $_showDeleted = false;
    protected $_addedDeletedQuery = false;
    
    public function setShowDeleted($value = true) {
        $this->_showDeleted = $value;
        return $this;
    }
    
    public function load($printQuery = false, $logQuery = false) {
        if ($this->_showDeleted == false
            && $this->_addedDeletedQuery == false
        ) {
            $this->addFieldToFilter('transaction_is_deleted', 0);
            $this->_addedDeletedQuery = true;
        }
        return parent::load($printQuery, $logQuery);
    }
    
    public function getSelectCountSql() {
        if ($this->_showDeleted == false
            && $this->_addedDeletedQuery == false
        ) {
            $this->addFieldToFilter('transaction_is_deleted', 0);
            $this->_addedDeletedQuery = true;
        }
        return parent::getSelectCountSql();
    }
}

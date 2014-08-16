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

class AW_Collpur_Model_Mysql4_Deal extends Mage_Core_Model_Mysql4_Abstract
{

    protected $_couponsResource;

    public function _construct()
    {    
        $this->_init('collpur/deal', 'id');
        $this->_couponsResource = Mage::getResourceModel('collpur/rewrite');
    }

    public function loadOrderItemIds($dealId)
    {  
       $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('collpur/dealpurchases'), array('order_item_id'))
            ->where('deal_id=?', $dealId);
       return $this->_getReadAdapter()->fetchCol($select);
    }    

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if(Mage::app()->isSingleStoreMode()) $object->setData('store_ids', 0);
     
        if (is_array($object->getData('store_ids')))
            $object->setData('store_ids', implode(',', $object->getData('store_ids')));
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getData('store_ids')) { $object->setData('store_ids', explode(',', $object->getData('store_ids'))); }

        $deleteWhere = $this->_getWriteAdapter()->quoteInto('deal_id = ?', $object->getId()); 
        $this->_getWriteAdapter()->delete($this->getTable('collpur/rewrite'), $deleteWhere);
  
        $storeIds = array_unique(Mage::getModel('core/store')->getCollection()->getAllIds());
         
        foreach ($storeIds as $storeId) {
            $rewriteInfo = array('deal_id' => $object->getId(), 'store_id' => $storeId, 'identifier' => $this->_getUniqueIdentifier($storeId, $this->_combineIdentifier($object))
        );
           $this->_getWriteAdapter()->insert($this->getTable('collpur/rewrite'), $rewriteInfo);
        }
    }
    
    protected function _afterLoad(Mage_Core_Model_Abstract $object) {

        if ($object->getId()) {

            if ($urlKey = Mage::getResourceModel('collpur/rewrite')->loadByDealId($object->getId(), 0)) {

                $object->setUrlKey($urlKey);
                
            } else {

                $object->setUrlKey('');
            }
        }
    }

    private function _combineIdentifier($deal) {

        $urlKey = trim($deal->getUrlKey());

        if ($urlKey) {
            $urlKey = strtolower(preg_replace("#\s+#is", "-", $urlKey)); // replace spaces by - signs
            $urlKey = preg_replace("#[^a-zA-Z0-9-_]#is", "", $urlKey); // remove all symbols that are not letters, hyphens or underlines        
        }

        if (!$urlKey) {
            $relatedProduct = Mage::getModel('catalog/product')->load($deal->getProductId());
            if ($relatedProduct->getId()) {                 
                $urlKey = $relatedProduct->getUrlKey();
            }            
            else {
                $urlKey = rand();            
            }
        }

        return urlencode($urlKey);
    }

    private function _getUniqueIdentifier($storeId, $identifier) {

        $defaultValue = $identifier;
        $unique = false;
        $i = 1;
        do {
            if ($this->_couponsResource->isUnique($storeId, $identifier)) {
                $unique = true;
            } else {
                $identifier = "{$defaultValue}-{$i}";
            }
            $i++;
        } while (!$unique);

        return $identifier;
    }

    public function loadActiveDealByProduct($deal ,$productId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('deal'))
            ->where('product_id = ?', $productId);
        if ($data = $this->_getReadAdapter()->fetchRow($select)) {
            $deal->addData($data);
        }
        $this->_afterLoad($deal);
        return $this;
    }

}
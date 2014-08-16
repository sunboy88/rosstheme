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


class AW_Productquestions_Model_Mysql4_Productquestions extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('productquestions/productquestions', 'question_id');
    }

    /*
     * Updates product title in the main table
     * @param int $productId
     * @param string $title
     * @param int|null $storeId
     * @return count of updated enties, is needed for tests
     */

    public function setProductTitleById($productId, $title, $storeId=null) {
        $db = $this->_getWriteAdapter();

        $prop = array(
            'question_product_name' => $title
        );

        if (!is_null($storeId)) {
            $count = $db->update($this->getMainTable(), $prop, $db->quoteInto('question_product_id=? AND question_store_id=?', $productId, $storeId));
        } else {
            $count = $db->update($this->getMainTable(), $prop, $db->quoteInto('question_product_id=?', $productId));
        }
        return $count;
    }

    /*
     * Deletes questions by product ID
     * @param int $productId
     * @param int|null $storeId
     * @return count of deleted entries, is needed for tests
     */

    public function deleteByProductId($productId, $storeId=null) {
        $db = $this->_getWriteAdapter();
        if (!is_null($storeId)) {
            $count = $db->delete($this->getMainTable(), $db->quoteInto('question_product_id=? AND question_store_id=?', $productId, $storeId));
        } else {
            $count = $db->delete($this->getMainTable(), $db->quoteInto('question_product_id=?', $productId));
        }
        return $count;
    }

    /*
     * Updates voting counters for the question
     * @param int $questionId
     * @param mixed $value
     */

    public function vote($questionId, $value) {
        $db = $this->_getWriteAdapter();
        $tableName = $this->getTable('productquestions/helpfulness');

        $voted = $db->fetchOne(
                $db->select()
                        ->from($tableName, new Zend_Db_Expr('COUNT(*)'))
                        ->where('question_id=?', $questionId)
        );

        if ($voted)
            $db->query('UPDATE ' . $tableName . ' SET vote_count=vote_count+1, vote_sum=vote_sum+' . ($value ? 1 : 0) . ' WHERE question_id=' . $db->quote($questionId));
        else
            $db->query('INSERT INTO ' . $tableName . ' SET question_id=' . $questionId . ', vote_count=1, vote_sum=' . ($value ? 1 : 0));
    }

}

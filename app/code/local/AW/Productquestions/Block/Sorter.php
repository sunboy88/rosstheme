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


class AW_Productquestions_Block_Sorter extends Mage_Core_Block_Template {

    protected $_orderVarName = 'orderby';
    protected $_dirVarName = 'dir';

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('productquestions/sorter.phtml');
    }

    /*
     * Returns array of allowed question sorting
     * @return array Sorting_ley => sorting_field_name
     */

    public static function getAllowedSorting() {
        $allowedSorting = Mage::getStoreConfig('productquestions/interface/allowed_sorting_type');
        $allowedSorting = $allowedSorting ? explode(',', $allowedSorting) : array();

        $res = array();
        if (empty($allowedSorting))
            return $res;

        $allSortings = AW_Productquestions_Model_Source_Question_Sorting::toShortOptionArray();

        foreach ($allowedSorting as $key)
            if (array_key_exists($key, $allSortings))
                $res[$key] = $allSortings[$key];

        return $res;
    }

    /*
     * Returns current question sorting field and direction enclosed in array
     * @return array Sorting order (field) and direction
     */

    public function getCurrentSorting() {
        $allowedSorting = array_keys(self::getAllowedSorting());

        if (empty($allowedSorting))
            return array(false, false);

        $sortOrder = $this->getRequest()->getParam($this->_orderVarName);
        if (!$sortOrder
                || !in_array($sortOrder, $allowedSorting)
        )
            $sortOrder = reset($allowedSorting);

        $sortDir = $this->getRequest()->getParam($this->_dirVarName);
        if (AW_Productquestions_Model_Source_Question_Sorting::SORT_ASC != $sortDir
                && AW_Productquestions_Model_Source_Question_Sorting::SORT_DESC != $sortDir
        )
            $sortDir = AW_Productquestions_Model_Source_Question_Sorting::SORT_ASC;

        return array($sortOrder, $sortDir);
    }

    /*
     * Returns sorting order URL
     * @param int $sortOrder Sorting order index
     * @return string Sorting URL
     */

    public function getSortOrderUrl($sortOrder) {
        return $this->getSorterUrl(array($this->_orderVarName => $sortOrder));
    }

    /*
     * Returns inverted sorting direction
     * @param string $dir Current direction
     * @return string Inverted direction
     */

    public static function getInvertedDir($dir) {
        return (AW_Productquestions_Model_Source_Question_Sorting::SORT_ASC == $dir) ? AW_Productquestions_Model_Source_Question_Sorting::SORT_DESC : AW_Productquestions_Model_Source_Question_Sorting::SORT_ASC;
    }

    /*
     * Returns sorting direction URL
     * @param string $direction Sorting direction
     * @return string Direction URL
     */

    public function getSortDirUrl($direction) {
        return $this->getSorterUrl(array($this->_dirVarName => $direction));
    }

    /*
     * Returns URL with parameters encoded
     * @param none|array URL parameters
     * @return string URL
     */

    public function getSorterUrl($params=array()) {
        $urlParams = array();
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;

        return $this->getUrl('*/*/*', $urlParams);
    }

}

<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Detailed Product Reviews
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Olegnax_Athlete_Block_Product_View_Reviews extends Mage_Review_Block_Product_View_List
{
	protected $_reviewsCount = null;
	protected $_totalReviewsCount = null;
	const DEFAULT_REVIEWS_COUNT = 5;

	protected function _toHtml()
	{
		if (Mage::getStoreConfig('advanced/modules_disable_output/Mage_Review')) {
			return '';
		}
		return parent::_toHtml();
	}

	protected function _beforeToHtml()
	{
		$this->getReviewsCollection()
			->setPageSize($this->getReviewsCount())
			->setCurPage(1)
			->load();

		foreach ($this->getReviewsCollection()->getItems() as $item) {
			$model = Mage::getModel('rating/rating');
			$model->getReviewSummary($item->getReviewId());
			$item->addData($model->getData());
		}
		return parent::_beforeToHtml();
	}

	/**
	 * Get how much reviews should be displayed at once.
	 *
	 * @return int
	 */
	public function getReviewsCount()
	{
		if (null === $this->_reviewsCount) {
			$this->_reviewsCount = Mage::helper('athlete')->getCfg('product_info/reviews');
			if (!is_numeric($this->_reviewsCount) || empty($this->_reviewsCount)) {
				$this->_reviewsCount = self::DEFAULT_REVIEWS_COUNT;
			}
		}
		return $this->_reviewsCount;
	}

	/**
	 * Get total reviews for product
	 *
	 * @return int
	 */
	public function getTotalReviewsCount()
	{
		if (null === $this->_totalReviewsCount) {
			$this->_totalReviewsCount = Mage::helper('athlete/reviews')->getTotalReviewsCount( $this->getProductId() );
		}
		return $this->_totalReviewsCount;
	}

	public function getDetailedRating()
	{
		return Mage::helper('athlete/reviews')->getDetailedRating( $this->getProductId() );
	}

	public function getRatingSummary()
	{
		return Mage::helper('athlete/reviews')->getRatingSummary( $this->getProduct() );
	}

}

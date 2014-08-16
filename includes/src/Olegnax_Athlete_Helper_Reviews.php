<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Helper_Reviews extends Mage_Core_Helper_Abstract
{

	public function getTotalReviewsCount($productId)
	{
		return Mage::getModel('review/review')->getTotalReviews($productId, true, Mage::app()->getStore()->getId());
	}

	public function getRatingSummary($product)
	{
		if (!$product->getRatingSummary()) {
			Mage::getModel('review/review')->getEntitySummary($product, Mage::app()->getStore()->getId());
		}

		return $product->getRatingSummary()->getRatingSummary();
	}

	public function getDetailedRating( $productId )
	{
		$reviewsCount = $this->getTotalReviewsCount($productId);
		if ($reviewsCount == 0) {
			return null;
		}

		$ratingCollection = Mage::getModel('rating/rating')
			->getResourceCollection()
			->addEntityFilter('product') # TOFIX
			->setPositionOrder()
			->setStoreFilter(Mage::app()->getStore()->getId())
			->addRatingPerStoreName(Mage::app()->getStore()->getId())
			->load();

		if ($productId) {
			$ratingCollection->addEntitySummaryToItem($productId, Mage::app()->getStore()->getId());
		}

		return $ratingCollection;
	}

	public function addReviewSummary($review)
	{
		$model = Mage::getModel('rating/rating');
		$model->getReviewSummary($review->getReviewId());
		$review->addData($model->getData());
	}

}
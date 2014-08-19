<?php
/**
 * @version   1.0 29.04.2014
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2014 Olegnax
 */

class Olegnax_Athlete_Helper_Layout extends Mage_Core_Helper_Abstract
{
	public function getMaxWidth($store = null)
	{
		$width = Mage::helper('athlete')->getCfg('layout/max_width', 'athlete', $store);
		if ($width == 'custom') {
			return Mage::helper('athlete')->getCfg('layout/custom_width', 'athlete', $store);
		}
		return $width;
	}

	public function getCustomWidth($store = null)
	{
		$width = Mage::helper('athlete')->getCfg('layout/max_width', 'athlete', $store);
		if ($width == 'custom') {
			return Mage::helper('athlete')->getCfg('layout/custom_width', 'athlete', $store);
		}
		return 0;
	}

	public function getMaxBreakpoint($width)
	{
		$breakpoints = $this->getBreakpoints();
		foreach ($breakpoints as $_bp ) {
			if ( $width <= $_bp ) {
				return $_bp;
			}
		}
		return $_bp;
	}


	public function getBreakpoints()
	{
		return array(
			480,
			768,
			1024,
			1280,
			1360,
			1440,
			1680,
			9999,
		);
	}

	public function getBreakpointsContentWidth( $onlyContent = false)
	{
		$width = array(
			'480' => 426,
			'768' => 756,
			'1024' => 960,
			'1280' => 1200,
			'1360' => 1300,
			'1440' => 1380,
			'1680' => 1520,
		);
		if ($onlyContent) {
			return array_values($width);
		} else {
			return $width;
		}
	}

	public function getSliderItems($columns)
	{
		$itemsCustom = '';
		switch ( $columns ) {
			case 2:
				$itemsCustom = '[ [0, 2], [426, 2], [756, 3], [960, 2], [1200, 3], [1300, 3], [1380, 4], [1520, 5] ]';
				break;
			case 3:
				$itemsCustom = '[ [0, 2], [426, 2], [756, 3], [960, 3], [1200, 4], [1300, 4], [1380, 5], [1520, 6] ]';
				break;
			case 4:
				$itemsCustom = '[ [0, 2], [426, 2], [756, 3], [960, 4], [1200, 5], [1300, 5], [1380, 6], [1520, 7] ]';
				break;
			case 5:
				$itemsCustom = '[ [0, 2], [426, 2], [756, 3], [960, 5], [1200, 6], [1300, 6], [1380, 7], [1520, 8] ]';
				break;
			case 6:
				$itemsCustom = '[ [0, 2], [426, 2], [756, 3], [960, 6], [1200, 7], [1300, 7], [1380, 8], [1520, 8] ]';
				break;
			case 7:
				$itemsCustom = '[ [0, 2], [426, 2], [756, 3], [960, 7], [1200, 8] ]';
				break;
			default:
				$itemsCustom = '[ [0, 2], [426, 2], [756, 3], [960, 4], [1200, 5], [1300, 5], [1380, 6], [1520, 7] ]';
				break;
		}
		return $itemsCustom;
	}

	public function getBrandsSliderItems()
	{
		$brand_width = Mage::helper('athlete')->getCfg('main/image_width', 'athlete_brands');
		if ( !is_numeric($brand_width) || $brand_width < 0 || $brand_width > 300 ) {
			$brand_width = 96;
		}

		$itemsCustom = array();
		$itemsCustom[] = "[0, ". ceil(300 / $brand_width)."]";
		$contentBreakpoints = $this->getBreakpointsContentWidth(true);
		foreach ( $contentBreakpoints as $_cb ) {
			$itemsCustom[] = "[$_cb, ".ceil($_cb / $brand_width)."]";
		}

		return '[ '.implode(',', $itemsCustom).' ]';
	}

	public function getBannerSliderItems( $width )
	{
		$itemsCustom = array();
		$itemsCustom[] = "[0, 1]";
		$j = 2;
		for ( $i=$width+1; $i < 3000; $i+=$width ) {
			$itemsCustom[] = "[$i, $j]";
			$j++;
		}

		return '[ '.implode(',', $itemsCustom).' ]';
	}



}
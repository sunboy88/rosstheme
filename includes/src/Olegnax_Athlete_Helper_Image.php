<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Helper_Image extends Mage_Core_Helper_Abstract
{
	private $_maxWidth = 590;
	private $_defaultWidth = 473;
	private $_defaultHeight = 473;
	private $_thumbPadding = 11;
	private $_thumbVisible = 4;

	private $_listingWidth = 296;

	private $_mainWidth;
	private $_mainHeight;
	private $_aspect;
	private $_config;

	private $_adaptiveMaxWidth;
	private $_adaptiveMaxHeight;
	private $newDimensions;
	private $currentDimensions;
	private $cropDimensions;

	public function __construct()
	{
		$this->init();
	}

	/**
	 * init main image width, height and ratio
	 *
	 * @param null $storeId - store to load configuration data
	 */
	public function init($storeId = NULL)
	{
		$this->_mainWidth = $this->_defaultWidth;
		$this->_mainHeight = $this->_defaultHeight;

		$this->_config = Mage::helper('athlete')->getCfg('', 'athlete', $storeId);

		if ($this->_config['images']['width'] > $this->_maxWidth) {
			$this->_config['images']['width'] = $this->_maxWidth;
		}
		if ($this->_config['images']['width'] > 0) {
			$this->_mainWidth = $this->_mainHeight = intval($this->_config['images']['width']);
		}
		if ($this->_config['images']['height'] > 0) {
			$this->_mainHeight = intval($this->_config['images']['height']);
		}

		$this->_aspect = $this->_mainWidth / $this->_mainHeight;
	}

	public function getDefaultSize()
	{
		return array($this->_defaultWidth, $this->_defaultHeight);
	}

	public function getMainSize()
	{
		return array($this->_mainWidth, $this->_mainHeight);
	}

	public function getThumbSize()
	{
		$thumbArea = $this->_mainWidth - $this->getThumbPadding() * ($this->getThumbVisible() - 1);
		$thumbWidth = floor($thumbArea / $this->getThumbVisible());
		return array($thumbWidth, $this->_calculateHeight($thumbWidth));
	}

	public function getThumbPadding()
	{
		return $this->_thumbPadding;
	}

	public function getThumbVisible()
	{
		return $this->_thumbVisible;
	}

	public function getListingWidth()
	{
		$imgX = $this->_config['listing']['product_img_width'];
		if ( empty($imgX) || $imgX < 128 || $imgX > 768 ) $imgX = $this->_listingWidth;
		return $imgX;
	}

	protected function _calculateHeight($width)
	{
		return round($width / $this->_aspect);
	}

	public function calculateHeight($width)
	{
		if ($this->_config['images']['keep_ratio']) {
			return round($width / $this->_aspect);
		} else {
			return $width;
		}
	}

	public function getAdditionalImage($product, $imgX, $imgY)
	{
		if (!$this->_config['listing']['hover_image'])
			return '';

		$product->load('media_gallery');
		$img = $product->getMediaGalleryImages()->getItemByColumnValue($this->_config['listing']['hover_image_col'], $this->_config['listing']['hover_image_label']);
		if ($img) {
			return '<img class="additional_img" src="' . Mage::helper('catalog/image')->init($product, 'small_image', $img->getFile())->resize($imgX, $imgY) .
			'" data-srcX2="' . Mage::helper('catalog/image')->init($product, 'small_image', $img->getFile())->resize($imgX * 2, $imgY * 2) .
			'" width="' . $imgX . '" height="' . $imgY . '" alt="' . $this->escapeHtml($product->getName()) . '" />';
		}

		return '';
	}

	/**
	 * resize and crop image
	 * @param string $fileName
	 * @param string $width
	 * @param string $height
	 * @return string - resized image url
	 */
	public function adaptiveResize($fileName, $width, $height)
	{
		$folderURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$imageURL = $folderURL . $fileName;

		$basePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/' . $fileName;
		$newPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/' . "resized" . '/' . $fileName;
		//if width empty then return original size image's URL
		if ($width != '') {
			//if image has already resized then just return URL
			if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
				$imageObj = new Varien_Image($basePath);
				$imageObj->constrainOnly(FALSE);
				$imageObj->keepAspectRatio(TRUE);
				$imageObj->keepFrame(FALSE);
				$imageObj->quality(95);

				$this->currentDimensions = array();
				$this->currentDimensions['width'] = $imageObj->getOriginalWidth();
				$this->currentDimensions['height'] = $imageObj->getOriginalHeight();

				$this->newDimensions = array();
				$this->newDimensions['newWidth'] = $imageObj->getOriginalWidth();
				$this->newDimensions['newHeight'] = $imageObj->getOriginalHeight();

				$this->adaptiveResizeDimensions($width, $height);

				$imageObj->resize($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
				if ( $this->cropDimensions['x'] > 0 || $this->cropDimensions['y'] > 0 ) {
					//top, left, right, bottom
					$imageObj->crop(
						$this->cropDimensions['y'],
						$this->cropDimensions['x'],
						intval( $this->newDimensions['newWidth'] - $this->_adaptiveMaxWidth - $this->cropDimensions['x']),
						intval( $this->newDimensions['newHeight'] - $this->_adaptiveMaxHeight - $this->cropDimensions['y'])
					);
				}
				$imageObj->save($newPath);
			}
			$resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "resized" . '/' . $fileName;
		} else {
			$resizedURL = $imageURL;
		}
		return $resizedURL;
	}

	/**
	 * Calculates new image dimensions, not allowing the width and height to be less than either the max width or height
	 *
	 * @param int $width
	 * @param int $height
	 * @return array - new dimensions
	 */
	protected function adaptiveResizeDimensions($width, $height)
	{
		if (!is_numeric($width) || $width  == 0) {
			$width = ($height * $this->currentDimensions['width']) / $this->currentDimensions['height'];
		}

		if (!is_numeric($height) || $height  == 0) {
			$height = ($width * $this->currentDimensions['height']) / $this->currentDimensions['width'];
		}

		$this->_adaptiveMaxHeight = intval($height);
		$this->_adaptiveMaxWidth  = intval($width);

		$this->calcImageSizeStrict($this->currentDimensions['width'], $this->currentDimensions['height']);

		$this->cropDimensions = array();
		$this->cropDimensions['x'] = 0;
		$this->cropDimensions['y'] = 0;

		// now, figure out how to crop the rest of the image...
		if ($this->newDimensions['newWidth'] > $this->_adaptiveMaxWidth) {
			$this->cropDimensions['x'] = intval(($this->newDimensions['newWidth'] - $this->_adaptiveMaxWidth) / 2);
		} elseif ($this->newDimensions['newHeight'] > $this->_adaptiveMaxHeight) {
			$this->cropDimensions['y'] = intval(($this->newDimensions['newHeight'] - $this->_adaptiveMaxHeight) / 2);
		}
	}

	/**
	 * Calculates new image dimensions, not allowing the width and height to be less than either the max width or height
	 *
	 * @param int $width
	 * @param int $height
	 * @return array - new dimensions
	 */
	protected function calcImageSizeStrict($width, $height)
	{
		// first, we need to determine what the longest resize dimension is..
		if ($this->_adaptiveMaxWidth >= $this->_adaptiveMaxHeight) {
			// and determine the longest original dimension
			if ($width > $height) {
				$newDimensions = $this->calcHeight($width, $height);

				if ($newDimensions['newWidth'] < $this->_adaptiveMaxWidth) {
					$newDimensions = $this->calcWidth($width, $height);
				}
			} elseif ($height >= $width) {
				$newDimensions = $this->calcWidth($width, $height);

				if ($newDimensions['newHeight'] < $this->_adaptiveMaxHeight) {
					$newDimensions = $this->calcHeight($width, $height);
				}
			}
		} elseif ($this->_adaptiveMaxHeight > $this->_adaptiveMaxWidth) {
			if ($width >= $height) {
				$newDimensions = $this->calcWidth($width, $height);

				if ($newDimensions['newHeight'] < $this->_adaptiveMaxHeight) {
					$newDimensions = $this->calcHeight($width, $height);
				}
			} elseif ($height > $width) {
				$newDimensions = $this->calcHeight($width, $height);

				if ($newDimensions['newWidth'] < $this->_adaptiveMaxWidth) {
					$newDimensions = $this->calcWidth($width, $height);
				}
			}
		}

		$this->newDimensions = $newDimensions;
	}

	/**
	 * Calculates a new width and height for the image based on $this->maxWidth and the provided dimensions
	 *
	 * @return array
	 * @param  int   $width
	 * @param  int   $height
	 */
	protected function calcWidth($width, $height)
	{
		$newHeight = ($height * $this->_adaptiveMaxWidth) / $width;

		return array(
			'newWidth'  => intval($this->_adaptiveMaxWidth),
			'newHeight' => intval($newHeight)
		);
	}

	/**
	 * Calculates a new width and height for the image based on $this->maxWidth and the provided dimensions
	 *
	 * @return array
	 * @param  int   $width
	 * @param  int   $height
	 */
	protected function calcHeight($width, $height)
	{
		$newWidth = ($width * $this->_adaptiveMaxHeight) / $height;

		return array(
			'newWidth'  => intval($newWidth),
			'newHeight' => intval($this->_adaptiveMaxHeight)
		);
	}
}
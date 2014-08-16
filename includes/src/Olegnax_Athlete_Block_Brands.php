<?php
/**
 * Brands block
 *
 */
class Olegnax_Athlete_Block_Brands extends Mage_Core_Block_Template
{
	public function getBrands()
	{
		if ( !$this->isBrandsEnabled() ) {
			return ;
		}
		$isAllBrands = Mage::helper('athlete')->getCfg('main/brands', 'athlete_brands');
		if ( $isAllBrands ) {
			$brandsList = $this->getAllBrands();
		} else {
			$brandsList = $this->getBrandsWithProducts();
		}
		//add image / url to brands
		$brandDir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'wysiwyg/olegnax/athlete/brands/';
		$brandExt = Mage::helper('athlete')->getCfg('main/image', 'athlete_brands');
		$brands = array();
		foreach ($brandsList as $b) {
			$brands[] = array(
				'name' => htmlspecialchars($b),
				'image' => $brandDir . preg_replace('#[^a-z0-9]#', '_', strtolower($b)) . '.' . $brandExt,
				'url' => Mage::getUrl() . 'catalogsearch/result/?q=' . urlencode($b),
			);
		}
		return $brands;
	}

	private function getBrandAttribute()
	{
		return Mage::helper('athlete')->getCfg('main/attribute', 'athlete_brands');
	}

	private function isBrandsEnabled()
	{
		$request = Mage::app()->getFrontController()->getRequest();
		$status = false;
		if (Mage::helper('athlete')->getCfg('main/status', 'athlete_brands')) {
			$status = true;
			if (Mage::helper('athlete')->getCfg('main/pages', 'athlete_brands') == 1) {
				$status = false;
				if ($request->getModuleName() == 'cms' && $request->getControllerName() == 'index' && $request->getActionName() == 'index') {
					$status = true;
				}
			}
		}
		return $status;
	}

	private function getAllBrands()
	{
		$result = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $this->getBrandAttribute())
			->getSource()
			->getAllOptions(0, 1);
		$brands = array();
		foreach ($result as $b) {
			$brands[] = $b['label'];
		}
		return $brands;
	}

	private function getBrandsWithProducts()
	{
		$attribute = $this->getBrandAttribute();
		$collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect($attribute)
			->addAttributeToFilter($attribute, array('neq' => ''))
			->addAttributeToFilter($attribute, array('notnull' => true));
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		$brands = array_unique($collection->getColumnValues($attribute));
		return Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute)
			->getSource()
			->getOptionText(implode(',', $brands));
	}

}
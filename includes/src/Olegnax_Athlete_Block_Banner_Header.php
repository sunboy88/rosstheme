<?php

class Olegnax_Athlete_Block_Banner_Header extends Olegnax_Athlete_Block_Banner_Abstract
{

	const CACHE_TAG  = 'athlete_banner_header';

	protected function _loadBanner()
	{
		$config = $this->_config['header'];
		$banner_id = $config['all'];
		switch ( $this->_module ) {
			case 'cms':
				if ( !empty($config['cms']) ) {
					$banner_id = $config['cms'];
				}
				if ( $this->_controller == 'index' && !empty($config['home']) ) {
					$banner_id = $config['home'];
				}
				break;
			case 'customer':
				if ( !empty($config['account']) ) {
					$banner_id = $config['account'];
				}
				break;
			case 'checkout':
				if ( !empty($config['checkout']) ) {
					$banner_id = $config['checkout'];
				}
				if ( $this->_controller == 'cart' && !empty($config['cart']) ) {
					$banner_id = $config['cart'];
				}
				break;
			case 'catalog':
				if ( !empty($config['catalog']) ) {
					$banner_id = $config['catalog'];
				}
				if ( $this->_controller == 'category' && !empty($config['category']) ) {
					$categoryId = $this->_getCurrentCategoryId();
					if (empty($categoryId)) break;
					$items = explode(',', trim($config['category'], ','));
					foreach ($items as $_item) {
						list($cId, $bId) = explode(':', $_item);
						if ( $cId == $categoryId ) {
							$banner_id = $bId;
							break;
						}
					}
				}
				if ( $this->_controller == 'product' && !empty($config['product']) ) {
					$productId = $this->_getCurrentProductId();
					if (empty($productId)) break;
					$items = explode(',', trim($config['product'], ','));
					foreach ($items as $_item) {
						list($pId, $bId) = explode(':', $_item);
						if ( $pId == $productId ) {
							$banner_id = $bId;
							break;
						}
					}
				}
				break;
		}
		$this->_loadStaticBlock($banner_id);
	}
}
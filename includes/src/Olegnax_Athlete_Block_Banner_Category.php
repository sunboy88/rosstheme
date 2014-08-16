<?php
/**
 * Category banner block
 *
 */
class Olegnax_Athlete_Block_Banner_Category extends Olegnax_Athlete_Block_Banner_Abstract
{
	const CACHE_TAG  = 'athlete_banner_category';

	protected function _loadBanner()
	{
		$config = $this->_config['category'];
		$banner_id = '';
		if ( $this->_module == 'catalog' && $this->_controller == 'category' && !empty($config['category']) ) {
			$categoryId = $this->_getCurrentCategoryId();
			if ( !empty($categoryId) ) {
				$items = explode(',', trim($config['category'], ','));
				foreach ($items as $_item) {
					list($cId, $bId) = explode(':', $_item);
					if ( $cId == $categoryId ) {
						$banner_id = $bId;
						break;
					}
				}
			}
		}
		$this->_loadStaticBlock($banner_id);
	}

}
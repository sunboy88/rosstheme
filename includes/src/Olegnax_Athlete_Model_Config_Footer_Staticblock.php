<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Model_Config_Footer_Staticblock
{
	protected $_options;

	public function toOptionArray()
	{
		if (!$this->_options) {
			$blocks = Mage::getResourceModel('cms/block_collection')
				->load();//->toOptionArray(');

			$this->_options = array();
			foreach ( $blocks as $_block) {
				$this->_options[] = array(
					'value' => $_block->getIdentifier(),
					'label' => $_block->getTitle(),
				);
			}

		}
		return $this->_options;
	}

}
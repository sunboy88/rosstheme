<?php
class Olegnax_Colorswatches_Block_Swatches extends Mage_Core_Block_Template
{
	private $_swatches;

	public function isSwatchesEnabled()
	{
		return $this->helper('olegnaxcolorswatches')->getCfg('main/status');
	}

	public function getSwatches()
	{
		return $this->_parseSwatches($this->helper('olegnaxcolorswatches')->getCfg('main/swatch_images'));
	}

	public function getSwatchKeys()
	{
		$swatches = $this->_parseSwatches($this->helper('olegnaxcolorswatches')->getCfg('main/swatch_images'));
		$keys = array();
		foreach ($swatches as $_swatch) {
			if ( !in_array($_swatch['key'], $keys) )
				$keys[] = $_swatch['key'];
		}
		return $keys;
	}

	protected function _parseSwatches($s)
	{
		if ( !empty($this->_swatches) ) {
			return $this->_swatches;
		}
		$swatches = array();
		if ($s) {
			if (preg_match_all("/^(.*)\:(.*)=(.*)$/m", $s, $m, PREG_SET_ORDER)) {
				foreach ($m as $_ln)
					$swatches[] = array(
						'key' => trim($_ln[1]),
						'value' => trim($_ln[2]),
						'img' => trim($_ln[3])
					);
			}
		}
		$this->_swatches = $swatches;
		return $swatches;
	}
}
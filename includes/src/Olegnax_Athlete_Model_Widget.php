<?php

class Olegnax_Athlete_Model_Widget extends Mage_Widget_Model_Widget
{
	public function getWidgetDeclaration($type, $params = array(), $asIs = true)
	{
		if (preg_match('~(^athlete/widget_banner_category)~', $type)) {
			$params['text'] = str_replace("\r\n", '\n', $params['text']);
		}
		return parent::getWidgetDeclaration($type, $params, $asIs);
	}
}
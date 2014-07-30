<?php
class Olegnax_Megamenu_Block_Megamenu extends Mage_Page_Block_Html_Topmenu
{

	/**
	 * Get sidebar menu html
	 *
	 * @param string $outermostClass
	 * @param string $childrenWrapClass
	 * @return string
	 */
	public function getSidebarHtml($outermostClass = '', $childrenWrapClass = '')
	{
		Mage::dispatchEvent('page_block_html_topmenu_gethtml_before', array(
			'menu' => $this->_menu,
			'block' => $this
		));

		$this->_menu->setOutermostClass($outermostClass);
		$this->_menu->setChildrenWrapClass($childrenWrapClass);

		$html = parent::_getHtml($this->_menu, $childrenWrapClass);

		Mage::dispatchEvent('page_block_html_topmenu_gethtml_after', array(
			'menu' => $this->_menu,
			'html' => $html
		));

		return $html;
	}

	/**
	 * Recursively generates top menu html from data that is specified in $menuTree
	 *
	 * @param Varien_Data_Tree_Node $menuTree
	 * @param string $childrenWrapClass
	 * @return string
	 */
	protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
	{
		$html = '';

		$children = $menuTree->getChildren();
		$parentLevel = $menuTree->getLevel();
		$childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

		$counter = 1;
		$childrenCount = $children->count();

		$parentPositionClass = $menuTree->getPositionClass();
		$itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

		foreach ($children as $child) {

			$childClass = '';

			$child->setLevel($childLevel);
			$child->setIsFirst($counter == 1);
			$child->setIsLast($counter == $childrenCount);
			$child->setPositionClass($itemPositionClassPrefix . $counter);

			$outermostClassCode = '';
			$outermostClass = $menuTree->getOutermostClass();

			$megamenuData = array(
				'type' => '',
				'layout' => 'menu',
				'menu' => 1,
				'top' => '',
				'bottom' => '',
				'right' => '',
				'percent' => 0,
			);
			if ($childLevel == 0 ) {
				if ($outermostClass) {
					$childClass .= ' '. $outermostClass;
				}
				$category = Mage::getModel('catalog/category')->load(str_replace('category-node-', '', $child->getId()));
				$childClass .= ' '. $category->getOlegnaxmegamenuType();
				$megamenuData['type'] = $category->getOlegnaxmegamenuType();
				$megamenuData['layout'] = $category->getOlegnaxmegamenuLayout();
				$megamenuData['menu'] = $category->getOlegnaxmegamenuMenu();
				$megamenuData['top'] = $category->getOlegnaxmegamenuTop();
				$megamenuData['bottom'] = $category->getOlegnaxmegamenuBottom();
				$megamenuData['right'] = $category->getOlegnaxmegamenuRight();
				$megamenuData['percent'] = $category->getOlegnaxmegamenuRightPercent();
				if ( $megamenuData['menu'] == '' ) $megamenuData['menu'] = 1;
				if ( $megamenuData['percent'] == '' ) $megamenuData['percent'] = 0;
				if ( empty($megamenuData['layout']) ) $megamenuData['layout'] = 'menu';
			}

			$showChildren = false;
			$leftClass = $rightClass = $top = $bottom = $right = $menu = '';
			if (
				$child->hasChildren()
				|| ( $childLevel == 0 && $megamenuData['type'] == 'wide'
					&& ( !empty($megamenuData['top']) || !empty($megamenuData['bottom'])
						|| ( !empty($megamenuData['right']) && $megamenuData['percent'] != 0 )
					)
				)
			) {
				$showChildren = true;

				if ( $megamenuData['type'] == 'wide' ) {
					$leftClass = 'megamenu-block-col-'.(6-$megamenuData['percent']);
					$rightClass = 'megamenu-block-col-'.$megamenuData['percent'];
					$top = $this->_drawMenuBlock('top', $megamenuData['top']);
					$bottom = $this->_drawMenuBlock('bottom', $megamenuData['bottom']);
					$right = $this->_drawMenuBlock('right', $megamenuData['right']);
				}
				if ( $megamenuData['menu'] == 1 || $megamenuData['type'] != 'wide' ) {
					$menu .= '<ul class="level' . $childLevel . '">';
					$menu .= $this->_getHtml($child, $childrenWrapClass);
					$menu .= '</ul>';
					$menu .= '<div class="clear"></div>';
				}

				if ( !$child->hasChildren() || $megamenuData['menu'] != 1 ) {
					$childClass .= ' parent parent-fake';
				}
			}

			$child->setClass($childClass);
			$html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
			$html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
				. $this->escapeHtml($child->getName()) . '</span></a>';

			if ( $showChildren ) {
				if (!empty($childrenWrapClass)) {
					$html .= '<div class="' . $childrenWrapClass . '">';
				}

				if ( $childLevel == 0 && $megamenuData['type'] == 'wide' ) {
					switch ( $megamenuData['layout'] ) {
						case 'top_menu' :
							$html .= '<div class="megamenu-block-col ' . $leftClass . '">';
							$html .= $top;
							$html .= $menu;
							$html .= '</div>';
							$html .= '<div class="megamenu-block-col ' . $rightClass . '">';
							$html .= $right;
							$html .= '</div>';
							$html .= '<div class="clear"></div>';
							$html .= $bottom;
							break;
						case 'top_menu_bottom' :
							$html .= '<div class="megamenu-block-col ' . $leftClass . '">';
							$html .= $top;
							$html .= $menu;
							$html .= $bottom;
							$html .= '</div>';
							$html .= '<div class="megamenu-block-col ' . $rightClass . '">';
							$html .= $right;
							$html .= '</div>';
							$html .= '<div class="clear"></div>';
							break;
						case 'menu' :
						default :
							$html .= $top;
							$html .= '<div class="megamenu-block-col ' . $leftClass . '">';
							$html .= $menu;
							$html .= '</div>';
							$html .= '<div class="megamenu-block-col ' . $rightClass . '">';
							$html .= $right;
							$html .= '</div>';
							$html .= '<div class="clear"></div>';
							$html .= $bottom;
					}
				} else {
					$html .= $menu;
				}
				if (!empty($childrenWrapClass)) {
					$html .= '</div>';
				}

			}
			$html .= '</li>';

			$counter++;
		}

		return $html;
	}

	/**
	 * @param string $type
	 * @param string $content
	 * @param string $class
	 * @return string
	 */
	protected function _drawMenuBlock($type, $content, $class = '')
	{
		$html = '';

		if ( !empty($content) ) {
			$html .= '<div class="std megamenu-block megamenu-block-'.$type.' '.$class.'">';
			$html .= $this->helper('olegnaxmegamenu')->processCmsBlock($content);
			$html .= '<div class="clear"></div>';
			$html .= '</div>';
		}

		return $html;
	}
}
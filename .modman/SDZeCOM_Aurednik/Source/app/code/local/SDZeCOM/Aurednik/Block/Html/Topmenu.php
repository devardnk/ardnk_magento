<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Top menu block
 *
 * @category    Mage
 * @package     Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SDZeCOM_Aurednik_Block_Html_Topmenu extends Mage_Page_Block_Html_Topmenu
{
	/**
	 * Get top menu html
	 *
	 * @param string $outermostClass
	 * @param string $childrenWrapClass
	 * @return string
	 */
	public function getHtml($outermostClass = '', $childrenWrapClass = '')
	{
		Mage::dispatchEvent('page_block_html_topmenu_gethtml_before', array(
			'menu' => $this->_menu
		));

		$this->_menu->setOutermostClass($outermostClass);
		$this->_menu->setChildrenWrapClass($childrenWrapClass);

		$html = $this->_getHtml($this->_menu, $childrenWrapClass);

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
		//$helper = new SDZeCOM_Aurednik_Catalog_Model_Category();

		$children = $menuTree->getChildren();
		$parentLevel = $menuTree->getLevel();
		$childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

		$counter = 1;
		$childrenCount = $children->count();

		$parentPositionClass = $menuTree->getPositionClass();
		$itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

		foreach ($children as $child)
		{
			$categoryModel = Mage::getModel('catalog/category');

			$child->setLevel($childLevel);
			$child->setIsFirst($counter == 1);
			$child->setIsLast($counter == $childrenCount);
			$child->setPositionClass($itemPositionClassPrefix . $counter);

			$childId = $child->getData('id');
			$childId = explode('-', $childId);
			$childId = end($childId);

			$outermostClassCode = '';
			$outermostClass = $menuTree->getOutermostClass();

			if ($childLevel == 0 && $outermostClass)
			{
				$outermostClassCode = ' class="' . $outermostClass . '" ';
				$child->setClass($outermostClass);
			}

			//echo get_class($helper);

			$html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';

			if ($childLevel == 1 && $categoryModel->load($childId)->getThumbnailUrl() != false)
			{
				$thumbnailUrl = $categoryModel->load($childId)->getThumbnailUrl();
				$html .= '<div class="thumbnail-image">';
				$html .= '<a href="' . $child->getUrl() . '"><img src="' . $thumbnailUrl . '" title="' . $this->escapeHtml($child->getName()) . '" /></a>';
				$html .= '</div>';
			}

			$html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
				. $this->escapeHtml($child->getName()) . '</span></a>';

			if ($child->hasChildren())
			{
				if (!empty($childrenWrapClass))
				{
					$html .= '<div class="' . $childrenWrapClass . '">';
				}
				if ($childLevel == 0)
				{
					$html .= '<div class="main-wrapper">';
				}

				$html .= '<ul class="level' . $childLevel . '">';
				$html .= $this->_getHtml($child, $childrenWrapClass);
				$html .= '</ul>';

				if (!empty($childrenWrapClass))
				{
					$html .= '</div>';
				}
				if ($childLevel == 0)
				{
					$html .= '</div>';
				}
			}
			$html .= '</li>';

			$counter++;
		}

		return $html;
	}
}

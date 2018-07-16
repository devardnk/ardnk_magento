<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Category_Source extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions()
	 */
	public function getAllOptions()
	{
		return array(
			array('value' => SDZeCOM_Aurednik_Helper_Category::VIEW_MODE_ID_GRID, 'label' => Mage::helper('aurednik')->__('grid')),
			array('value' => SDZeCOM_Aurednik_Helper_Category::VIEW_MODE_ID_LIST, 'label' => Mage::helper('aurednik')->__('list')));

	}

}
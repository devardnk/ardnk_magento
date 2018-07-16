<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Cms_Home_Highlight extends SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute
{


	//---------------------------------- public area ------------------------------------------------------
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert ein Colelction Array aufbereitet für ein "dropdown" Menü
	 *
	 * @return array Optionen
	 */
	public function toOptionArray()
	{

		$attributeCol = $this->getCollection();

		$attributeCol
			->getSelect()
			->where(self :: TABLE_COLUMN_TYPE_ID . "=?", SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type :: TYPE_HIGHLIGHT);

		return Mage:: helper('library/Option')->toOptionArray($attributeCol, self :: TABLE_COLUMN_NAME, self :: TABLE_COLUMN_ID);
	}

	//---------------------------------- protected area ---------------------------------------------------
	//---------------------------------- private area -----------------------------------------------------


}

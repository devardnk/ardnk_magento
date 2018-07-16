<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values extends Mage_Core_Model_Abstract
{

	/**
	 *
	 * @var TABLE_COLUMN_ID
	 */
	const TABLE_COLUMN_ID = "id";

	/**
	 *
	 * @var TABLE_COLUMN_ENTITY_ID
	 */
	const TABLE_COLUMN_ENTITY_ID = "entity_id";

	/**
	 *
	 * @var TABLE_COLUMN_ATTRIBUTE_ID
	 */
	const TABLE_COLUMN_ATTRIBUTE_ID = "attribute_id";

	/**
	 *
	 * @var TABLE_COLUMN_ATTRIBUTE_VALUE
	 */
	const TABLE_COLUMN_ATTRIBUTE_VALUE = "attribute_value";

	//---------------------------------- public area ------------------------------------------------------

	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Initialisiert ein Resource Model
	 */
	protected function _construct()
	{
		$this->_init('aurednik/cms_home_entity_attribute_values');
	}

	//---------------------------------- private area -----------------------------------------------------
}

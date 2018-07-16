<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Attribute_Values extends Mage_Core_Model_Resource_Db_Abstract
{

	/**
	 *
	 * @var TABLE_COLUMN_ATTRIBUTE_ID
	 */
	const TABLE_COLUMN_ATTRIBUTE_ID = "attribute_id";

	/**
	 *
	 * @var TABLE_COLUMN_ENTITY_ID
	 */
	const TABLE_COLUMN_TYPE = "entity_id";

	/**
	 *
	 * @var TABLE_COLUMN_ATTRIBUTE_VALUE
	 */
	const TABLE_COLUMN_ATTRIBUTE_VALUE = "attribute_value";

	//---------------------------------- public area ------------------------------------------------------

	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 */
	protected function _construct()
	{
		$this->_init('aurednik/cmshome_entity_attribute_values', 'id');
	}

	//---------------------------------- private area -----------------------------------------------------
}
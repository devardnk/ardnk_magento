<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute extends Mage_Core_Model_Abstract
{

	/**
	 *
	 * @var TABLE_COLUMN_ID
	 */
	const TABLE_COLUMN_ID = "id";

	/**
	 *
	 * @var TABLE_COLUMN_TYPE_ID
	 */
	const TABLE_COLUMN_TYPE_ID = "type_id";

	/**
	 *
	 * @var TABLE_COLUMN_NAME
	 */
	const TABLE_COLUMN_NAME = "name";

	/**
	 *
	 * @var TABLE_COLUMN_TITLE
	 */
	const TABLE_COLUMN_TITLE = "title";

	/**
	 *
	 * @var TABLE_COLUMN_INPUT_TYPE
	 */
	const TABLE_COLUMN_INPUT_TYPE = "input_type";

	/**
	 *
	 * @var TABLE_COLUMN_REQUIRED
	 */
	const TABLE_COLUMN_REQUIRED = "required";

	/**
	 *
	 * @var TABLE_COLUMN_BACKEND_MODEL
	 */
	const TABLE_COLUMN_BACKEND_MODEL = "backend_model";

	/**
	 *
	 * @var TABLE_COLUMN_SORT
	 */
	const TABLE_COLUMN_SORT = "sort";


	//---------------------------------- public area ------------------------------------------------------

	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Initialisiert das Model
	 */
	protected function _construct()
	{
		$this->_init('aurednik/cms_home_entity_attribute');
	}

	//---------------------------------- private area -----------------------------------------------------

}

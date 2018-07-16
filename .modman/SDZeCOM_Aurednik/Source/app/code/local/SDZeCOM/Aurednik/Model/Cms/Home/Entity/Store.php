<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store extends Mage_Core_Model_Abstract
{


	/**
	 *
	 * @var TABLE_COLUMN_CMS_HOME_ENTITY_ID
	 */
	const TABLE_COLUMN_CMS_HOME_ENTITY_ID = 'cms_home_entity_id';

	/**
	 *
	 * @var TABLE_COLUMN_STORE_ID
	 */
	const TABLE_COLUMN_STORE_ID = 'store_id';


	//---------------------------------- public area ------------------------------------------------------

	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Initialisiert eine Model Resource
	 *
	 */
	protected function _construct()
	{
		$this->_init('aurednik/cms_home_entity_store');
		$this->_isPkAutoIncrement = false;

	}
	//---------------------------------- private area -----------------------------------------------------
}

<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Type_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

	//---------------------------------- public area ------------------------------------------------------

	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_construct()
	 */
	protected function _construct()
	{
		$this->_init('aurednik/cms_home_entity_type');
	}

	//---------------------------------- private area -----------------------------------------------------
}
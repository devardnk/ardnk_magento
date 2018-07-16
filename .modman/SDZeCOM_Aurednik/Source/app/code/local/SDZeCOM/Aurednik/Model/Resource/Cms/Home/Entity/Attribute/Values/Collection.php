<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Attribute_Values_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{


	//---------------------------------- public area ------------------------------------------------------
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Grenzt die Collection anhand von $intEntityTypeId und $intAttributeId ein
	 *
	 * @param int $intEntityTypeId
	 *
	 * @param int $intAttributeId
	 *
	 * @return SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Attribute_Collection
	 */
	public function getByEntityIdAndAttributeId($intEntityId, $intAttributeId)
	{

		if (is_null($intEntityId) || $intEntityId <= 0)
		{
			return $this;
		}

		if (is_null($intAttributeId) || $intAttributeId <= 0)
		{
			return $this;
		}

		$this
			->getSelect()
			->where(
				SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ENTITY_ID .
				"=" .
				$intEntityId .
				" AND " .
				SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ATTRIBUTE_ID .
				"=" .
				$intAttributeId
			);

		return $this;

	}

	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_construct()
	 */
	protected function _construct()
	{
		$this->_init('aurednik/cms_home_entity_attribute_values');
	}

	//---------------------------------- private area -----------------------------------------------------
}
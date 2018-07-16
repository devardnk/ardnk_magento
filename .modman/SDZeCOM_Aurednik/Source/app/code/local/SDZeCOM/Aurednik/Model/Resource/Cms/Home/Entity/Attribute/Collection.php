<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Attribute_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{


	//---------------------------------- public area ------------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Grenzt die Collection anhand von $intEntityTypeId ein
	 *
	 * @param int $intEntityTypeId
	 *
	 * @return SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Attribute_Collection
	 */
	public function getByEntityTypeId($intEntityTypeId)
	{

		if (is_null($intEntityTypeId) || $intEntityTypeId <= 0)
		{
			return $this;
		}

		$this
			->getSelect()
			->where(SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_TYPE_ID . '=' . $intEntityTypeId);

		return $this;
	}

	//---------------------------------- protected area ---------------------------------------------------
	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_construct()
	 */
	protected function _construct()
	{
		$this->_init('aurednik/cms_home_entity_attribute');
	}

	//---------------------------------- private area -----------------------------------------------------
}
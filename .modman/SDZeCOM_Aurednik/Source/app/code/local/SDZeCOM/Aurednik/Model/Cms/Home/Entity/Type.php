<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type extends Mage_Core_Model_Abstract
{

	/**
	 *
	 * @var TYPE_BANNER
	 */
	const TYPE_BANNER = 1;

	/**
	 *
	 * @var TABLE_COLUMN_ID
	 */
	const TABLE_COLUMN_ID = 'id';

	/**
	 *
	 * @var TABLE_COLUMN_NAME
	 */
	const TABLE_COLUMN_NAME = 'name';

	/**
	 *
	 * @var TYPE_HIGHLIGHT
	 */
	const TYPE_HIGHLIGHT = 2;

	/**
	 *
	 * @var TYPE_ALL
	 */
	const TYPE_ALL = 3;


	//---------------------------------- public area ------------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert ein array mit Optionen  für das input type "dropdow" Menü
	 *
	 * @return array Optionen
	 */
	public function toOptionArray()
	{

		$objCollection = $this->getCollection();

		if ($objCollection->count() == 0)
		{
			return array();
		}

		$arrOptions = array();

		foreach ($objCollection as $objCurrent)
		{
			$arrOptions [$objCurrent->getId()] = $objCurrent->getName();
		}

		return $arrOptions;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert ein array mit Optionen  für das Filter Backend type "dropdow" Menü (Filter)
	 *
	 * @return array Optionen
	 */
	public function toBackendFilterOptionArray()
	{

		$objCollection = $this->getCollection();

		if ($objCollection->count() == 0)
		{
			return array();
		}

		$arrOptions = array();

		foreach ($objCollection as $objCurrent)
		{
			$name = $objCurrent->getName();
			$arrOptions [$name] = $name;
		}

		return $arrOptions;
	}

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
		$this->_init('aurednik/cms_home_entity_type');
	}
	//---------------------------------- private area -----------------------------------------------------
}

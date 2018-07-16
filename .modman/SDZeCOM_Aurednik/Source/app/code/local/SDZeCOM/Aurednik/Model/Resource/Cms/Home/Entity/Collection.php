<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

	/**
	 *
	 * @var STORE_FILTER
	 */
	const STORE_FILTER = 'store';

	/**
	 *
	 * @var STORE_TABLE
	 */
	const STORE_TABLE = 'store_table';

	//---------------------------------- public area ------------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Fügt Store Filter zu Collection
	 *
	 * @param int|Mage_Core_Model_Store $store
	 * @param bool $withAdmin
	 *
	 * @return SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Store_Collection
	 */
	public function addStoreFilter($store, $withAdmin = true)
	{

		if ($store instanceof Mage_Core_Model_Store)
		{
			$store = array($store->getId());
		}

		if (!is_array($store))
		{
			$store = array($store);
		}

		if ($withAdmin)
		{
			$store [] = Mage_Core_Model_App :: ADMIN_STORE_ID;
		}

		$this->addFilter(self :: STORE_FILTER, array('in' => $store), 'public');

		return $this;
	}

	//---------------------------------- protected area ---------------------------------------------------
	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_construct()
	 */
	protected function _construct()
	{
		$this->_init('aurednik/cms_home_entity');
		$this->_map ['fields'] ['store'] = self::STORE_TABLE . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID;
	}


	/**
	 * Join store relation table if there is store filter
	 */

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Fügt die Stores zur Collections hinzu
	 *
	 * @return Mage_Core_Model_Resource_Db_Collection_Abstract
	 */
	protected function _renderFiltersBefore()
	{

		if ($this->getFilter(self::STORE_FILTER))
		{

			$storeTable = $this->getTable('aurednik/cms_home_entity_store');
			$storeTable = 'aurednik_cmshome_entity_store';

			$this->getSelect()
				->join(
					array(self::STORE_TABLE => $storeTable),
					'main_table.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID . ' = ' . self::STORE_TABLE . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID,
					array())
				->group('main_table.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID);

			/*
			 * Allow analytic functions usage because of one field grouping
			*/
			$this->_useAnalyticFunction = true;
		}

		return parent:: _renderFiltersBefore();
	}

	//---------------------------------- private area -----------------------------------------------------
}
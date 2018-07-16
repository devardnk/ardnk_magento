<?php

/**
 *
 * @author akniss
 *
 * @copyright Copyright (c) 2014 SDZeCOM GmbH & Co. KG (http://www.sdzecom.de)
 *
 */
class SDZeCOM_Aurednik_Model_Cms_Home_Entity extends Mage_Core_Model_Abstract
{

	/**
	 *
	 * @var TABLE_COLUMN_ID
	 */
	const TABLE_COLUMN_ID = "id";

	/**
	 *
	 * @var TABLE_COLUMN_TYPE
	 */
	const TABLE_COLUMN_TYPE = "type_id";

	/**
	 *
	 * @var TABLE_COLUMN_NAME
	 */
	const TABLE_COLUMN_NAME = "entity_name";

	/**
	 *
	 * @var TABLE_COLUMN_ACTIVE
	 */
	const TABLE_COLUMN_ACTIVE = "active";

	/**
	 *
	 * @var TABLE_COLUMN_SORT
	 */
	const TABLE_COLUMN_SORT = "sort";


	//---------------------------------- public area ------------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert eine Collection mit allen aktiven Bannern
	 *
	 * @param int $intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID, StoreViewId des aktuellen Storeviews
	 *
	 * @return SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Collection
	 */
	public function getActiveBannerCollection($intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID)
	{
		return $this->getCollection(SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type ::TYPE_BANNER, 1, $intStoreViewId);

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert eine Collection mit allen aktiven Highlights
	 *
	 * @param int $intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID, StoreViewId des aktuellen Storeviews
	 *
	 * @return SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Collection
	 */
	public function getActiveHighlightCollection($intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID)
	{
		return $this->getCollection(SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type ::TYPE_HIGHLIGHT, 1, $intStoreViewId);

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param number collection Typ
	 *
	 * @param integer $isActive
	 *
	 * @param int $intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID,  StoreViewId des aktuellen Storeviews
	 *
	 * Liefert eine Collection
	 *
	 * @return array  SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Collection
	 */
	public function getCollection($intType = SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type::TYPE_ALL, $isActive = null, $intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID)
	{

		$entitiyCollect = parent:: getCollection();

		if ($intType != SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type::TYPE_ALL)
		{
			$entitiyCollect->getSelect()
				->where(SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_TYPE . '=?', $intType);
		}

		if (!is_null($isActive))
		{
			$entitiyCollect->getSelect()
				->where(SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ACTIVE . '=?', $isActive);
		}

		$storeTable = Mage::getModel('aurednik/cms_home_entity_store')->getResource()->getMainTable();

		$entitiyCollect->getSelect()
			->joinLeft(
				array($storeTable => $storeTable),
				'main_table.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID . '=' . $storeTable . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID,
				array(SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID => SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID))
			->order(SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_SORT, 'asc')
			->group('main_table.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID);

		if ($intStoreViewId != Mage_Core_Model_App :: ADMIN_STORE_ID)
		{

			$entitiyCollect->getSelect()
				->where(
					'(' . $storeTable . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID . '=' . $intStoreViewId . ' OR '
					. $storeTable . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID . '=' . Mage_Core_Model_App :: ADMIN_STORE_ID . ')')
				->group('main_table.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID);
		}
		else
		{

			$entitiyCollect->getSelect()
				->where($storeTable . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID . ' IN (?)', Mage::getModel('core/store')->getCollection()->getAllIds());
			//		->orWhere ( $storeTable . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID . ' is null' );
		}

		return $entitiyCollect;
	}

	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Initialisiert das Model
	 *
	 */
	protected function _construct()
	{
		$this->_init('aurednik/cms_home_entity');
	}

	//---------------------------------- private area -----------------------------------------------------
}

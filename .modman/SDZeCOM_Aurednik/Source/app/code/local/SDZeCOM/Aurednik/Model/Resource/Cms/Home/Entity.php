<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity extends Mage_Core_Model_Resource_Db_Abstract
{

	/**
	 *
	 * @var ENTITY_STORES
	 */
	const ENTITY_STORES = 'stores';

	//---------------------------------- public area ------------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * Liefert Entity StoreIds
	 *
	 * @param int $entityId
	 *
	 * @return array storeIds
	 */
	public function lookupStoreIds($entityId)
	{

		$adapter = $this->_getReadAdapter();

		$select = $adapter->select()
			->from($this->getTable('aurednik/cms_home_entity_store'), SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID)
			->where(SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID . ' = :' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID);

		$binds = array(
			':' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID => ( int )$entityId
		);

		return $adapter->fetchCol($select, $binds);
	}


	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 */
	protected function _construct()
	{
		$this->_init('aurednik/cms_home_entity', 'id');
	}


	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Model_Resource_Db_Abstract::_afterLoad()
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{

		if ($object->getId())
		{
			$stores = $this->lookupStoreIds($object->getId());

			$object->setData(SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID, $stores);
			$object->setData(self::ENTITY_STORES, $stores);
		}

		return parent:: _afterLoad($object);
	}


	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Model_Resource_Db_Abstract::_afterSave()
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		$oldStores = $this->lookupStoreIds($object->getId());

		$newStores = ( array )$object->getStores();

		$table = $this->getTable('aurednik/cms_home_entity_store');

		$insert = array_diff($newStores, $oldStores);
		$delete = array_diff($oldStores, $newStores);

		if ($delete)
		{
			$where = array(
				SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID . ' = ?' => ( int )$object->getId(),
				SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID . ' IN (?)' => $delete
			);

			$this->_getWriteAdapter()->delete($table, $where);
		}

		if ($insert)
		{
			$data = array();

			foreach ($insert as $storeId)
			{
				$data [] = array(
					SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID => ( int )$object->getId(),
					SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID => ( int )$storeId
				);
			}

			$this->_getWriteAdapter()->insertMultiple($table, $data);
		}

		return parent:: _afterSave($object);
	}

	//---------------------------------- private area -----------------------------------------------------
}
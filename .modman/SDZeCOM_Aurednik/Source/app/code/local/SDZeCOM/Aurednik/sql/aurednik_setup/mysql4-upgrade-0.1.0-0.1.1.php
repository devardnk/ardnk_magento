<?php
$installer = $this;

$installer->startSetup();

$entityMainTable = Mage:: getModel('aurednik/cms_home_entity')->getResource()->getMainTable();
$entityStoreTable = Mage:: getModel('aurednik/cms_home_entity_store')->getResource()->getMainTable();

$installer->run(
	"
DROP TABLE IF EXISTS " . $entityStoreTable . ";
		
CREATE TABLE `" . $entityStoreTable . "` (
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID . "` INT(11) NOT NULL,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID . "` SMALLINT(5) UNSIGNED NOT NULL,
	PRIMARY KEY (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID . "`, `" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID . "`),
	INDEX `FK_aurednik_cmshome_entity_store_core_store` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID . "`),
	CONSTRAINT `FK_aurednik_cmshome_entity_store_core_store` FOREIGN KEY (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID . "`) REFERENCES `core_store` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID . "`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `FK_aurednik_cmshome_entity_store_aurednik_cmshome_entity` FOREIGN KEY (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID . "`) REFERENCES `" . $entityMainTable . "` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID . "`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_bin'
ENGINE=InnoDB;

");

$read = Mage::getSingleton('core/resource')->getConnection('core_read');

$ids = $read->query('select ' . SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID . ' from ' . $entityMainTable)->fetchAll();

$checkSubString = false;

if (is_array($ids) && count($ids) > 0)
{

	$insert =
		'insert into ' .
		$entityStoreTable .
		' ( `' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_CMS_HOME_ENTITY_ID .
		'` , `' .
		SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID .
		'` ) VALUES ';

	foreach ($ids as $id)
	{

		if (isset ($id [SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID]))
		{
			$checkSubString = true;
			$insert .= ' ( ' . $id [SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID] . ', ' . 0 . ' ),';
		}
	}

	if ($checkSubString)
	{
		$insert = substr($insert, 0, -1);

		$installer->run($insert);
	}
}

$installer->endSetup();

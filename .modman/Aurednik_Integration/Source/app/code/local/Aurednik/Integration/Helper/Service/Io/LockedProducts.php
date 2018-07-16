<?php

/**
 * Hilfsklasse, die Methoden bereitstellt, um Importdateien einzulesen
 *
 * @author egutsche
 *
 */
class Aurednik_Integration_Helper_Service_Io_LockedProducts extends Aurednik_Integration_Helper_Data
{

	/**
	 *
	 * @var LOCKED_PRODUCTS_IMPORT_DIRECTORY_CONFIG
	 */
	const LOCKED_PRODUCTS_IMPORT_DIRECTORY_CONFIG = 'aurednik_integration/catalog_product_lockedproducts_import/local_import_directory';


	/**
	 *
	 * @var LOCKED_PRODUCTS_IMPORT_DIRECTORY_BACKUP_CONFIG
	 */
	const LOCKED_PRODUCTS_IMPORT_DIRECTORY_BACKUP_CONFIG = 'aurednik_integration/catalog_product_lockedproducts_import/local_import_backup_directory';


	/**
	 *
	 * @var LOCKED_PRODUCTS_IMPORT_DEFAULT_FILENAME_PATTERN
	 */
	const LOCKED_PRODUCTS_IMPORT_DEFAULT_FILENAME_PATTERN = 'aurednik_integration/catalog_product_lockedproducts_import/filename_pattern';


	/**
	 * Gibt den Dateipfad zum Import zurück
	 *
	 * @author egutsche
	 *
	 * @return string Dateipfad zum Import
	 */
	public static function getLockedProductsImportFilePath()
	{
		$configValue = Mage::getStoreConfig(self::LOCKED_PRODUCTS_IMPORT_DIRECTORY_CONFIG);
		if (is_null($configValue) || strlen(trim($configValue)) == 0)
		{
			return '';
		}

		return SDZeCOM_Library_Helper_Directory::joinPaths(Mage::getBaseDir('var'), $configValue);
	}


	/**
	 * Gibt den Dateipfad zum Sicherungsort des Imports zurück
	 *
	 * @author egutsche
	 *
	 * @return string Dateipfad zum Sicherungsort des Imports
	 */
	public static function getLockedProductsImportBackupFilePath()
	{
		$configValue = Mage::getStoreConfig(self::LOCKED_PRODUCTS_IMPORT_DIRECTORY_BACKUP_CONFIG);

		return SDZeCOM_Library_Helper_Directory::joinPaths(
			Mage::getBaseDir('var'),
			$configValue,
			date('y'),
			date('m'),
			date('d'));
	}


	/**
	 * Gibt den Standard-Pattern der Dateinamen des Imports zurück
	 *
	 * @author egutsche
	 *
	 * @return string Standard-Pattern der Dateinamen des Imports
	 */
	public static function getLockedProductsImportDefaultPattern()
	{
		$configValue = Mage::getStoreConfig(self::LOCKED_PRODUCTS_IMPORT_DEFAULT_FILENAME_PATTERN);

		return is_null($configValue) || strlen(trim($configValue)) == 0 ? '' : $configValue;
	}
}
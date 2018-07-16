<?php

/**
 * Hilfsklasse, die Mehtoden bereitstellt, um Importdateien einzulesen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Helper_Service_Io_Prices extends Aurednik_Integration_Helper_Data
{

	/**
	 *
	 * @var PRODUCT_PRICES_IMPORT_DIRECTORY_CONFIG
	 */
	const PRODUCT_PRICES_IMPORT_DIRECTORY_CONFIG = 'aurednik_integration/catalog_product_prices_import/local_import_directory';


	/**
	 *
	 * @var PRODUCT_PRICES_IMPORT_DIRECTORY_BACKUP_CONFIG
	 */
	const PRODUCT_PRICES_IMPORT_DIRECTORY_BACKUP_CONFIG = 'aurednik_integration/catalog_product_prices_import/local_import_backup_directory';


	/**
	 *
	 * @var PRODUCT_PRICES_IMPORT_DEFAULT_FILENAME_PATTERN
	 */
	const PRODUCT_PRICES_IMPORT_DEFAULT_FILENAME_PATTERN = 'aurednik_integration/catalog_product_prices_import/filename_pattern';


	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Gibt den Dateipfad zum Produktpreis Import zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @return string Dateipfad zum Produktpreis Import
	 */
	public static function getProductPricesImportFilePath()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_PRICES_IMPORT_DIRECTORY_CONFIG);

		if (is_null($configValue) || strlen(trim($configValue)) == 0)
		{
			return '';
		}

		return SDZeCOM_Library_Helper_Directory::joinPaths(Mage::getBaseDir('var'), $configValue);
	}


	/**
	 * Gibt den Dateipfad zum Sicherungsort des Produktpreis Import zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @return string Dateipfad zum Sicherungsort des Produktpreis Import
	 */
	public static function getProductPricesImportBackupFilePath()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_PRICES_IMPORT_DIRECTORY_BACKUP_CONFIG);

		return SDZeCOM_Library_Helper_Directory::joinPaths(
			Mage::getBaseDir('var'),
			$configValue,
			date('y'),
			date('m'),
			date('d'));
	}


	/**
	 * Gibt den Standard-Pattern der Dateinamen des Produktpreis Imports zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @return string Standard-Pattern der Dateinamen des Produktpreis Imports
	 */
	public static function getProductPricesImportDefaultPattern()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_PRICES_IMPORT_DEFAULT_FILENAME_PATTERN);

		return is_null($configValue) || strlen(trim($configValue)) == 0 ? '' : $configValue;
	}

	//------------------------------------protected-area--------------------------------------------------
	//------------------------------------private-area----------------------------------------------------

}
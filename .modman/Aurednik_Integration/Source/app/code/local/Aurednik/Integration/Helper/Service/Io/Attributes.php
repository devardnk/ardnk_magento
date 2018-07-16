<?php

/**
 * Hilfsklasse, die Mehtoden bereitstellt, um Importdateien einzulesen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Helper_Service_Io_Attributes extends Aurednik_Integration_Helper_Data
{

	/**
	 *
	 * @var PRODUCT_ATTRIBUTES_IMPORT_DIRECTORY_CONFIG
	 */
	const PRODUCT_ATTRIBUTES_IMPORT_DIRECTORY_CONFIG = 'aurednik_integration/catalog_product_attributes_import/local_import_directory';


	/**
	 *
	 * @var PRODUCT_ATTRIBUTES_IMPORT_DIRECTORY_BACKUP_CONFIG
	 */
	const PRODUCT_ATTRIBUTES_IMPORT_DIRECTORY_BACKUP_CONFIG = 'aurednik_integration/catalog_product_attributes_import/local_import_backup_directory';


	/**
	 *
	 * @var PRODUCT_ATTRIBUTES_IMPORT_DEFAULT_FILENAME_PATTERN
	 */
	const PRODUCT_ATTRIBUTES_IMPORT_DEFAULT_FILENAME_PATTERN = 'aurednik_integration/catalog_product_attributes_import/filename_pattern';


	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Gibt den Dateipfad zum Produktattribut Import zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @return string Dateipfad zum Produktattribut Import
	 */
	public static function getProductAttributesImportFilePath()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_ATTRIBUTES_IMPORT_DIRECTORY_CONFIG);

		if (is_null($configValue) || strlen(trim($configValue)) == 0)
		{
			return '';
		}

		return SDZeCOM_Library_Helper_Directory::joinPaths(Mage::getBaseDir('var'), $configValue);
	}


	/**
	 * Gibt den Dateipfad zum Sicherungsort des Produktattribut Import zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @return string Dateipfad zum Sicherungsort des Produktattribut Import
	 */
	public static function getProductAttributesImportBackupFilePath()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_ATTRIBUTES_IMPORT_DIRECTORY_BACKUP_CONFIG);

		return SDZeCOM_Library_Helper_Directory::joinPaths(
			Mage::getBaseDir('var'),
			$configValue,
			date('y'),
			date('m'),
			date('d'));
	}


	/**
	 * Gibt den Standard-Pattern der Dateinamen des Produktattribut Imports zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @return string Standard-Pattern der Dateinamen des Produktattribut Imports
	 */
	public static function getProductAttributesImportDefaultPattern()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_ATTRIBUTES_IMPORT_DEFAULT_FILENAME_PATTERN);

		return is_null($configValue) || strlen(trim($configValue)) == 0 ? '' : $configValue;
	}

	//------------------------------------protected-area--------------------------------------------------
	//------------------------------------private-area----------------------------------------------------

}
<?php

/**
 * Hilfsklasse, die Mehtoden bereitstellt, um die Importdateien
 * einzulesen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Helper_Service_Io_Local extends Aurednik_Integration_Helper_Data
{

	/**
	 *
	 * @var PRODUCT_IMPORT_DIRECTORY_CONFIG
	 */
	const PRODUCT_IMPORT_DIRECTORY_CONFIG = 'aurednik_integration/catalog_product_import/local_import_directory';

	/**
	 *
	 * @var PRODUCT_IMPORT_DIRECTORY_BACKUP_CONFIG
	 */
	const PRODUCT_IMPORT_DIRECTORY_BACKUP_CONFIG = 'aurednik_integration/catalog_product_import/local_import_backup_directory';


	/**
	 *
	 * @var PRODUCT_IMPORT_DEFAULT_FILENAME_PATTERN
	 */
	const PRODUCT_IMPORT_DEFAULT_FILENAME_PATTERN = 'aurednik_integration/catalog_product_import/filename_pattern';


	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Gibt den Dateipfad, in dem sich die Produktimport Dateien befnden zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Dateipfad zu den Produktimport Dateien
	 */
	public function getProductImportFilePath()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_IMPORT_DIRECTORY_CONFIG);

		if (is_null($configValue) || strlen(trim($configValue)) == 0)
		{
			return '';
		}

		return SDZeCOM_Library_Helper_Directory::joinPaths(Mage::getBaseDir('var'), $configValue);
	}


	/**
	 * Gibt den Dateipfad, in dem die Porduktimport Dateien gesichert werden zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Dateipfad zum Sicherungs Ordner der Produktimport Dateien
	 */
	public function getProductImportBackupFilePath()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_IMPORT_DIRECTORY_BACKUP_CONFIG);

		return SDZeCOM_Library_Helper_Directory::joinPaths(
			Mage::getBaseDir('var'),
			$configValue,
			date('y'),
			date('m'),
			date('d'));
	}


	/**
	 * Gibt den Standard-Pattern für den Produktimport zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Standard-Pattern für den Produktimport
	 */
	public function getProductImportDefaultPattern()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_IMPORT_DEFAULT_FILENAME_PATTERN);

		return is_null($configValue) || strlen(trim($configValue)) == 0 ? '' : $configValue;
	}

	//------------------------------------protected-area--------------------------------------------------
	//------------------------------------private-area----------------------------------------------------

}
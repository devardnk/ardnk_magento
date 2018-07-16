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
class Aurednik_Integration_Helper_Service_Io_Images extends Aurednik_Integration_Helper_Data
{

	/**
	 *
	 * @var PRODUCT_IMAGES_IMPORT_DIRECTORY_CONFIG
	 */
	const PRODUCT_IMAGES_IMPORT_DIRECTORY_CONFIG = 'aurednik_integration/catalog_product_images_import/local_import_directory';


	/**
	 *
	 * @var PRODUCT_IMAGES_IMPORT_DIRECTORY_BACKUP_CONFIG
	 */
	const PRODUCT_IMAGES_IMPORT_DIRECTORY_BACKUP_CONFIG = 'aurednik_integration/catalog_product_images_import/local_import_backup_directory';


	/**
	 *
	 * @var PRODUCT_IMAGES_IMPORT_DEFAULT_FILENAME_PATTERN
	 */
	const PRODUCT_IMAGES_IMPORT_DEFAULT_FILENAME_PATTERN = 'aurednik_integration/catalog_product_images_import/filename_pattern';


	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Gibt den Dateipfad zum Produktbilderimport zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @return string Dateipfad zum Produktbilderimport
	 */
	public static function getProductImagesImportFilePath()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_IMAGES_IMPORT_DIRECTORY_CONFIG);

		if (is_null($configValue) || strlen(trim($configValue)) == 0)
		{
			return '';
		}

		return SDZeCOM_Library_Helper_Directory::joinPaths(Mage::getBaseDir('var'), $configValue);
	}


	/**
	 * Gibt den Dateipfad zum Sicherungsort des Produktbilderimport zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @return string Dateipfad zum Sicherungsort des Produktbilderimports
	 */
	public static function getProductImagesImportBackupFilePath()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_IMAGES_IMPORT_DIRECTORY_BACKUP_CONFIG);

		return SDZeCOM_Library_Helper_Directory::joinPaths(
			Mage::getBaseDir('var'),
			$configValue,
			date('y'),
			date('m'),
			date('d'));
	}


	/**
	 * Gibt den Standard-Pattern der Dateinamen des Produktbiderimports zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @return string Standard-Pattern der Dateinamen des Produktbiderimports
	 */
	public static function getProductImagesImportDefaultPattern()
	{
		$configValue = Mage::getStoreConfig(self::PRODUCT_IMAGES_IMPORT_DEFAULT_FILENAME_PATTERN);

		return is_null($configValue) || strlen(trim($configValue)) == 0 ? '' : $configValue;
	}


	/**
	 * Gibt den Temp-Verzeichnis für den Produktbiderimport zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @return string Temp-Verzeichnis für den Produktbiderimport
	 */
	public static function getProductImagesTmpDirectory()
	{
		$tmpDir =
			SDZeCOM_Library_Helper_Directory::joinPaths(
				self::getProductImagesImportFilePath(),
				'temp');

		if (!file_exists($tmpDir))
		{
			@mkdir($tmpDir, 0777, true);
		}

		return $tmpDir;
	}


	/**
	 * Liefert den Dateipfad, in das die Bilder heruntergeladen werden.
	 * Wenn das Verzeichnis nicht existiert, wird es angelegt
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Dateipfad
	 */
	public static function getProductImagesDownloadDirectory()
	{

		$downloadPath = SDZeCOM_Library_Helper_Directory::joinPaths(Mage::getBaseDir('media'), 'import');

		if (!file_exists($downloadPath))
		{
			@mkdir($downloadPath, 0777, true);
		}

		return $downloadPath;
	}


	/**
	 * Erstellt eine Importdatei für den Produktbilder Import
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access static
	 *
	 * @param array $fields , CSV-Spaltenüberschriften
	 * @param array $data , CSV-Daten
	 *
	 * @return string Dateiname der erstelleten CSV-Datei
	 *
	 */
	public static function writeImportImageFile(array $fields, array $data)
	{

		if (!is_array($data) || count($data) == 0)
		{
			return '';
		}

		$writeCsv = SDZeCOM_Library_Helper_Directory::joinPaths(
			self::getProductImagesTmpDirectory(),
			preg_replace('/[^A-Za-z]+/', '', self::getProductImagesImportDefaultPattern()) . '_' . time() . '.csv'
		);

		$csvHandler = fopen($writeCsv, "w+");

		if ($csvHandler === false)
		{
			return '';
		}

		//Überschriften schreiben
		fputcsv($csvHandler, $fields, ';', '"');

		//Daten schreiben
		foreach ($data as $current)
		{
			fputcsv($csvHandler, $current, ';', '"');
		}

		fclose($csvHandler);

		rename($writeCsv, SDZeCOM_Library_Helper_Directory::joinPaths(self::getProductImagesImportFilePath(), basename($writeCsv)));

		return basename($writeCsv);
	}

	//------------------------------------protected-area--------------------------------------------------
	//------------------------------------private-area----------------------------------------------------

}
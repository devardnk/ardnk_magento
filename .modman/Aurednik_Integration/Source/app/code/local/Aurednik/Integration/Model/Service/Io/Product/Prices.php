<?php

/**
 * Klasse, die Mehtoden bereitstellt, um Produktattribut Importdateien einzulsesen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Model_Service_Io_Product_Prices extends SDZeCOM_Integration_Model_Service_Abstract implements SDZeCOM_Integration_Model_Service_Io_Interface
{

	//------------------------------------public-area-----------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_Io_Interface::fetchMultiple()
	 */
	public function fetchMultiple($pattern = null)
	{

		if (is_null($pattern))
		{
			$pattern = $this->getHelper()->getProductPricesImportDefaultPattern();
		}


		//Import Dateien
		$files = $this->getProductPricesImportFiles($pattern);

		$maxFilesPerImport = 1;

		if ($maxFilesPerImport !== '' && $maxFilesPerImport > 0)
		{
			$files = array_slice($files, 0, $maxFilesPerImport);
		}

		$files = $this->backupMulitple($files);

		return $files;
	}


	/**
	 * @ignore
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_Io_Interface::readMulitple()
	 */
	public function readMulitple(array $files)
	{
		return $files;
	}


	/**
	 * @ignore
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_Io_Interface::read()
	 */
	public function read($src)
	{
		return $src;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param array file, Dateien, die gesichert werden sollen
	 *
	 */
	public function backupMulitple(array $files)
	{

		$backupFiles = array();

		foreach ($files as $file)
		{

			$file = $this->backup($file);

			if (file_exists($file))
			{
				$backupFiles [] = $file;
			}
		}

		return $backupFiles;
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_Io_Interface::backup()
	 */
	public function backup($src, $prependDatetime = true)
	{

		$srcFileName = pathinfo($src, PATHINFO_FILENAME);
		$srcFileExtension = pathinfo($src, PATHINFO_EXTENSION);

		$backupFileName = '';

		if ($prependDatetime)
		{
			$backupFileName = date('y-m-d-H-s-') . $srcFileName;
		}
		else
		{
			$backupFileName = $srcFileName;
		}

		$backupFileName .= '.' . $srcFileExtension;

		$dest = SDZeCOM_Library_Helper_Directory::joinPaths($this->getProductPricesImportBackupFilePath(), $backupFileName);

		rename($src, $dest);

		return $dest;
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_Io_Interface::getErrors()
	 */
	public function getErrors()
	{
		return '';
	}

	//------------------------------------protected-area--------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Liefert die Hilfsklasse
	 *
	 * @return Aurednik_Integration_Helper_Service_Io_Prices
	 */
	protected function getHelper()
	{
		return Mage::helper('aurednik_integration/service_io_prices');
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param $pattern , nach dem, im Import Ordner
	 * nach Produktpreis-Importdateien gesucht werden soll
	 *
	 * Liefert den Dateipfad zu den Produktpreis-Importdateien für den
	 * Produktpreis-Import
	 *
	 * @return string Dateipfad zu den Importdateien
	 */
	protected function getProductPricesImportFilePath($pattern)
	{

		$filePath = $this->getHelper()->getProductPricesImportFilePath();

		if (!file_exists($filePath))
		{
			@mkdir($filePath, 0777, true);
		}

		$filePath = SDZeCOM_Library_Helper_Directory::joinPaths($filePath, $pattern);


		return $filePath;
	}


	/**
	 * Gibt den Dateipfad zu den Produktpreis-Importdateien zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param $pattern , nach dem, im Importordner
	 * nach Produktpreis-Importdateien gesucht werden soll
	 *
	 * @return string Dateipfad zu den Importdateien
	 */
	protected function getProductPricesImportFiles($pattern)
	{

		$filePath = $this->getProductPricesImportFilePath($pattern);

		return glob($filePath);
	}


	/**
	 * Gibt den Dateipfad zum Sicherungsordner der
	 * Produktpreis-Importdateien zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return string Dateipfad zum Sicherungsordner der Produktpreis-Importdateien
	 */
	protected function getProductPricesImportBackupFilePath()
	{

		$filePath = $this->getHelper()->getProductPricesImportBackupFilePath();

		if (!file_exists($filePath))
		{

			@mkdir($filePath, 0777, true);
		}

		return $filePath;
	}

	//------------------------------------private-area----------------------------------------------------
}
<?php

/**
 * Klasse, die Mehtoden bereitstellt, um Produktimport einzulsesen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Model_Service_Io_Product extends SDZeCOM_Integration_Model_Service_Abstract implements SDZeCOM_Integration_Model_Service_Io_Interface
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
			$pattern = $this->getHelper()->getProductImportDefaultPattern();
		}

		//Import Dateien
		$files = $this->getProductImportFiles($pattern);

		/**
		 * Wenn mehrere Dateien importiert werden sollen, wird die maxFilesPerImport Beschränkung nicht benötigt
		 */

		/*
		$maxFilesPerImport = 1;

		if ( $maxFilesPerImport !== '' && $maxFilesPerImport > 0 ) {
			$files = array_slice ( $files , 0 , $maxFilesPerImport );
		}
		*/

		//$files = $this->backupMulitple ( $files );

		return $files;
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_Io_Interface::readMulitple()
	 */
	public function readMulitple(array $files)
	{
		return $files;
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_Io_Interface::read()
	 */
	public function read($src)
	{
		return $src;
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_Io_Interface::backupMulitple()
	 */
	public function backupMulitple(array $files)
	{

		$backupFiles = array();

		foreach ($files as $file)
		{
			$srcFileExt = pathinfo($file, PATHINFO_EXTENSION);
			if ($srcFileExt == 'txt')
			{
				continue;
			}

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
		$checkFileDest =
			SDZeCOM_Library_Helper_Directory::joinPaths(
				$this->getProductImportFilePath(),
				'Produktexport_finished' . '.txt');

		if (file_exists($checkFileDest))
		{

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

			$dest =
				SDZeCOM_Library_Helper_Directory::joinPaths(
					$this->getProductImportBackupFilePath(),
					$srcFileExtension);

			if (!file_exists($dest))
			{
				@mkdir($dest, 0777, true);
			}

			$dest = SDZeCOM_Library_Helper_Directory::joinPaths($dest, $backupFileName);

			rename($src, $dest);
			unlink($checkFileDest);
			return $dest;
		}

		return '';
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
	 * Gibt die Hilfsklasse zruück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return Aurednik_Integration_Helper_Service_Io_Local
	 */
	protected function getHelper()
	{
		return Mage::helper('aurednik_integration/service_io_local');
	}


	/**
	 * Gibt den Dateipfad zu den Produktimport Dateien zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param $pattern , nach dem, im Importordner
	 * nach Produkt Importdateien gesucht werden soll
	 *
	 * @return string Dateipfad zu den Importdateien
	 */
	protected function getProductImportFilePath($pattern = null)
	{

		$filePath = $this->getHelper()->getProductImportFilePath();

		if (!file_exists($filePath))
		{
			@mkdir($filePath, 0777, true);
		}

		$filePath = SDZeCOM_Library_Helper_Directory::joinPaths($filePath, $pattern);


		return $filePath;
	}


	/**
	 * Gibt den Dateipfad zum Sicherungsordner der
	 * Produktimport Dateien zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return string Dateipfad zum Sicherungsordner der Produktimport Dateien
	 */
	protected function getProductImportBackupFilePath()
	{

		$filePath = $this->getHelper()->getProductImportBackupFilePath();

		if (!file_exists($filePath))
		{

			@mkdir($filePath, 0777, true);
		}

		return $filePath;
	}


	/**
	 * Gibt die Produkt Importdateien zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param $pattern , nach dem, im Importordner
	 * nach den Produkt Importdateien gesucht werden soll
	 *
	 * @return array Produkt Importdateien
	 */
	protected function getProductImportFiles($pattern)
	{

		$filePath = $this->getProductImportFilePath($pattern);

		$files = glob($filePath);

		if (!is_array($files) || count($files) == 0)
		{
			return array();
		}

		$files = $this->backupMulitple($files);

		if (!is_array($files) || count($files) == 0)
		{
			return array();
		}

		return $files;
	}


	//------------------------------------private-area----------------------------------------------------

}
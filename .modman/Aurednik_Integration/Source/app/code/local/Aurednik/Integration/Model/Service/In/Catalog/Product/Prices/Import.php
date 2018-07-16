<?php

/**
 * Klasse, die Mehtoden bereitstellt, um ein Produktpreise zu importieren
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Model_Service_In_Catalog_Product_Prices_Import extends Aurednik_Integration_Model_Service_In_Catalog_Product_Import
{


	//------------------------------------public-area-----------------------------------------------------

	/**
	 *
	 * Konstruktor Instantiert die Hilfsklasse
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 */
	public function __construct()
	{
		$this->helper = new Aurednik_Integration_Helper_Service_In_Catalog_Product_Prices_Import ();
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_In_Abstract::execute()
	 */
	public function execute($logPerformance = true)
	{

		ini_set('memory_limit', '1024M');

		$start = null;
		$end = null;

		// Hole alle Importdateien
		$files = $this->fetchFiles();

		//Keine Importdateien gefunden
		if (count($files) == 0)
		{
			return null;
		}

		if ($logPerformance)
		{
			$start = microtime(true);
		}

		// Map Files to Array
		$products = $this->getMapper()->mapFromFiles($files);

		if ($logPerformance)
		{
			$end = microtime(true);
			$this->getHelper()->logInfoMessage('Dauer des Mapping von ' . count($products) . ' Produktpreisen in Sekunden: ' . ($end - $start));
		}

		if (!is_array($products))
		{
			return 'ERROR OCCURED WHILE IMPORTING PRICES - FOR MORE DETAILS SEE: /var/log/ERRORS/price_import_' . $products . '.log';
		}

		//Importiere Produkte
		$errorMessages = $this->processData($products, $logPerformance);

		if (count($errorMessages) > 0)
		{
			return $errorMessages;
		}

		return 'no errors';
	}

	//------------------------------------protected-area--------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_In_Catalog_Product_Import::processData()
	 */
	protected function processData(array $products, $logImportDuration = true)
	{

		$startImport = null;
		$endImport = null;

		// Importer holen
		$importer = $this->getProductImporter();

		// Import Einstellungen definieren
		$importer->setContinueAfterErrors(true);
		$importer->setErrorLimit(99999);
		$importer->setBehavior(Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE);
		$importer->setImageAttributes(array());

		if ($logImportDuration)
		{
			$startImport = microtime(true);
		}

		//Produkte importieren
		$importer->processProductImport($products);

		if ($logImportDuration)
		{
			$endImport = microtime(true);
			$this->getHelper()->logInfoMessage('Dauer des Importvorgangs von ' . count($products) . ' Produktpreisen in Sekunden: ' . ($endImport - $startImport));
		}

		// Set Message to Schedule
		$errorMessages = $importer->getErrorMessages();

		$errorMessages = $this->logErrors($products, $importer->getErrorMessages());


		return $errorMessages;
	}
	//------------------------------------private-area----------------------------------------------------
}
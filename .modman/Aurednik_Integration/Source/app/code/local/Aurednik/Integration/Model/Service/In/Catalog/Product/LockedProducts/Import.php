<?php

/**
 * Klasse, die Methoden bereitstellt, um ein Produkte zu sperren
 *
 * @author egutsche
 *
 *
 */
class Aurednik_Integration_Model_Service_In_Catalog_Product_LockedProducts_Import extends Aurednik_Integration_Model_Service_In_Catalog_Product_Import
{

	/**
	 *
	 * Konstruktor Instantiert die Hilfsklasse
	 *
	 * @author egutsche
	 *
	 */
	public function __construct()
	{
		$this->helper = new Aurednik_Integration_Helper_Service_In_Catalog_Product_LockedProducts_Import ();
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

		$files = $this->fetchFiles();
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
			$this->getHelper()->logInfoMessage('Dauer des Mapping von ' . count($products) . ' gesperrten Produkten in Sekunden: ' . ($end - $start));
		}

		if (!is_array($products))
		{
			return 'ERROR OCCURED WHILE IMPORTING LOCKED PRODUCTS - FOR MORE DETAILS SEE: /var/log/ERRORS/locked_products_import_' . $products . '.log';
		}

		//Importiere Produkte
		$errorMessages = $this->processData($products, $logPerformance);

		if (count($errorMessages) > 0)
		{
			return $errorMessages;
		}

		return 'no errors';
	}


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
			$this->getHelper()->logInfoMessage('Dauer des Importvorgangs von ' . count($products) . ' gesperrten Produkten in Sekunden: ' . ($endImport - $startImport));
		}

		// Set Message to Schedule
		$errorMessages = $importer->getErrorMessages();

		$errorMessages = $this->logErrors($products, $importer->getErrorMessages());


		return $errorMessages;
	}
}
<?php

/**
 * Klasse, die Mehtoden bereitstellt, um ein Produktimport durchzuführen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Model_Service_In_Catalog_Product_Import extends SDZeCOM_Integration_Model_Service_In_Catalog_Product_Import
{

	/**
	 *
	 * @var $indexes
	 */
	protected $indexes = array(
		'catalog_product_attribute' => 0,
		'catalog_url' => 1,
		'catalog_product_flat' => 1,
		'catalogsearch_fulltext' => 1,
		'tag_summary' => 1,
	);

	/**
	 *
	 * @var Aurednik_Integration_Helper_Service_In_Catalog_Product_Import $helper
	 */
	protected $helper = null;

	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Instantiert die Hilfsklasse
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
		$this->helper = new Aurednik_Integration_Helper_Service_In_Catalog_Product_Import ();
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

		$mapper = $this->getMapper();

		// Map Files to Array
		$products = $mapper->mapFromFiles($files);

		if ($logPerformance)
		{
			$end = microtime(true);
			$this->getHelper()->logInfoMessage('Dauer des Mapping von ' . count($products) . ' Produkt(en) in Sekunden: ' . ($end - $start));
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

		$mapper = $this->getMapper();

		$errorMessages = array();

		// Import Einstellungen definieren
		$importer->setPartialIndexing(true);
		$importer->setContinueAfterErrors(true);
		$importer->setErrorLimit(99999);
		$importer->setBehavior(Mage_ImportExport_Model_Import::BEHAVIOR_APPEND);
		$importer->setUseNestedArrays(true);

		if ($logImportDuration)
		{
			$startImport = microtime(true);
		}

		if (is_array($products) && count($products) > 0)
		{
			//Produkte importieren
			$importer->processProductImport($products);

			if ($logImportDuration)
			{
				$endImport = microtime(true);
				$this->getHelper()->logInfoMessage('Dauer des Importvorgangs von ' . count($products) . ' Produkt(en) in Sekunden: ' . ($endImport - $startImport));

			}

			// Set Message to Schedule
			$errorMessages = $importer->getErrorMessages();

			$this->logErrors($products, $errorMessages);

			//Bilder-Csv rauschschreiben
			$imageCsvFileName = Aurednik_Integration_Helper_Service_Io_Images::writeImportImageFile($mapper->getImageDataFields(), $mapper->getImageData());

			if (strlen(trim($imageCsvFileName)) == 0)
			{
				$errorMessages ['Failed to create the images CSV file '] = array();
			}
			else
			{
				$this->getHelper()->logInfoMessage('Bilder CSV-Datei wurde erfolgreich erstellt! Dateiname: ' . $imageCsvFileName);
			}
		}
		else
		{
			$this->getHelper()->logInfoMessage('Keine Produkte zum Importieren gefunden');
		}

		//Produkte löschen
		$deleteProducts = $mapper->getProductDelete();

		if (!is_array($errorMessages))
		{
			$errorMessages = array();
		}

		if (is_array($deleteProducts) && count($deleteProducts) > 0)
		{
			$tmp = $this->_deleteProducts($deleteProducts, $logImportDuration);

			if (is_array($tmp) && count($tmp) > 0)
			{
				foreach ($tmp as $message)
				{
					$this->getHelper()->logErrorMessage($message);
				}

				$errorMessages = array_merge($errorMessages, $tmp);
			}
		}

		return $errorMessages;
	}


	/**
	 * Protokoliert Fehlermeldungen, die während dem Import entstanden sind
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param array $products , Produkte, die importiert wurden
	 *
	 * @param array $errorMessages , Fehlermeldungen, die während dem Import entstanden sind
	 *
	 */
	protected function logErrors(array $products, array $errorMessages)
	{

		$logErrors = array();

		if (!is_array($errorMessages) || count($errorMessages) == 0)
		{
			return;
		}

		foreach ($errorMessages as $message => $productIndices)
		{

			if (!is_array($productIndices) || count($productIndices) == 0)
			{
				continue;
			}

			foreach ($productIndices as $productPos)
			{

				$productPos = $productPos > 1 ? $productPos - 1 : $productPos;

				if (!isset($products[$productPos]))
				{
					continue;
				}

				$errorMessage = $message . ' => ' . print_r($products [$productPos], true);

				$this->getHelper()->logErrorMessage($errorMessage);

				$logErrors [] = $errorMessage;
			}
		}

		if (!is_array($logErrors) || count($logErrors) == 0)
		{
			return;
		}

		return;
	}


	/**
	 * Gibt die Hilfsklasse zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return Aurednik_Integration_Helper_Service_In_Catalog_Product_Import
	 *
	 */
	protected function getHelper()
	{
		return $this->helper;
	}

	//------------------------------------private-area----------------------------------------------------

	/**
	 * Löscht Produkte aus Magento
	 *
	 * @param array $products , Produkte, die gelöscht werden sollen
	 * @param boolean $logImportDuration , prüft, ob die Zeitdauer des Löschvorganges protokolliert werden sollte
	 *
	 * @return array, mit Meldungen, die während des Löschens entstanden sind
	 *
	 * @author akniss
	 * @version 1.00 01.09.2014
	 * @access private
	 */
	private function _deleteProducts(array $products, $logImportDuration = true)
	{

		$startImport = null;
		$endImport = null;

		$messages = array();

		if (count($products) == 0)
		{
			return array('Keine Produkte zum Löschen vorhanden');
		}

		//Benötigt man, um Produkte aus Magento löschen zu können
		Mage::register('isSecureArea', 1);

		if ($logImportDuration)
		{
			$startImport = microtime(true);
		}

		foreach ($products as $sku)
		{

			$sku = trim($sku);

			$productId = Mage::getModel('catalog/product')->getIdBySku($sku);

			if (!is_numeric($productId) || $productId <= 0)
			{
				$messages[] = "Error: Produkt mit der sku: {$sku} existiert nicht";
				continue;
			}

			$model = Mage::getModel('catalog/product')->load($productId);

			try
			{
				$model->delete();
				$messages[] = "Info: Produkt mit der sku: {$sku} Id {$productId} wurde erfolgreich gelöscht";

			}
			catch (Exception $e)
			{
				$messages[] = "Error: Das Produkt konnte nicht geloescht werden {$productId} sku: {$sku} Message: " . $e->getMessage();
			}


		}

		if ($logImportDuration)
		{
			$endImport = microtime(true);
			$this->getHelper()->logInfoMessage('Dauer des Loeschvorgangs von ' . count($products) . ' Produkt(en) in Sekunden: ' . ($endImport - $startImport));

		}

		Mage::unregister('isSecureArea');

		return $messages;
	}
}
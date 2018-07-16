<?php

/**
 * Klasse, die Mehtoden bereitstellt, um ein Produktattribut-Import durchzuführen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Model_Service_In_Catalog_Product_Attributes_Import extends SDZeCOM_Integration_Model_Service_In_Catalog_Product_Import
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
		$this->helper = new Aurednik_Integration_Helper_Service_In_Catalog_Product_Attributes_Import ();
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_In_Abstract::execute()
	 */
	public function execute()
	{

		ini_set('memory_limit', '1024M');

		// Hole alle Importdateien
		$files = $this->fetchFiles();

		//Keine Importdateien gefunden
		if (count($files) == 0)
		{
			return null;
		}

		// Map Files to Array
		$attributes = $this->getMapper()->mapFromFiles($files);

		//Importiere Produkte
		return $this->processData($attributes);

	}

	//------------------------------------protected-area--------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Service_In_Catalog_Product_Import::processData()
	 */
	protected function processData(array $attributes)
	{

		// Importer holen
		$importer = new SDZeCOM_Library_Model_Catalog_Product_Attribute_Import();

		//Produkte importieren
		$importer->importAttributes($attributes);


		$messages = 'successfully import: ' . print_r($importer->getSuccessMessages(), true) . '<br/>';

		$messages .= 'errors: ' . print_r($importer->getErrorMessages(), true);

		return $messages;
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
	 * @return Aurednik_Integration_Helper_Service_In_Catalog_Product_Attributes_Import
	 */
	protected function getHelper()
	{
		return new Aurednik_Integration_Helper_Service_In_Catalog_Product_Attributes_Import();
	}

	//------------------------------------private-area----------------------------------------------------
}
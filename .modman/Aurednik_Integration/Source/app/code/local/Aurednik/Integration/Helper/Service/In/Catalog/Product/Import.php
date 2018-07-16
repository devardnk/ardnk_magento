<?php

/**
 * Hilfsklasse, die Mehtoden bereitstellt, um den Produktimport
 * zu protokollieren
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Helper_Service_In_Catalog_Product_Import extends Mage_Core_Helper_Abstract
{

	/**
	 *
	 * @var $logFilename
	 */
	protected $logFilename = null;

	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Definiert den Dateinamen für die Logdatei
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
		$this->logFilename = 'aurednik-integration-product-import-' . date('y-m-d', time()) . '.log';
	}


	/**
	 * Gibt den Dateinamen der Log-Datei zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Dateiname der Log-Datei
	 *
	 */
	public function getlogFileName()
	{
		return $this->logFilename;
	}


	/**
	 * Protokolliert eine Fehlermeldung
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param string $message , die protokolliert werden soll
	 *
	 */
	public function logErrorMessage($message)
	{
		Mage::log($message, 3, $this->logFilename);
	}


	/**
	 * Protokolliert eine Information
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param string $message , die protokolliert werden soll
	 *
	 */
	public function logInfoMessage($message)
	{

		Mage::log($message, 6, $this->logFilename);
	}

	//------------------------------------protected-area--------------------------------------------------
	//------------------------------------private-area----------------------------------------------------
}
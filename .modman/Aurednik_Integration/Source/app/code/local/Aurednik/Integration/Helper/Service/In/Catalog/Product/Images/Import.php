<?php

/**
 * Hilfsklasse, die Mehtoden bereitstellt, um den Produktbilder-Import
 * zu protokollieren
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Helper_Service_In_Catalog_Product_Images_Import extends Aurednik_Integration_Helper_Service_In_Catalog_Product_Import
{

	/**
	 * Konstruktor Definiert den Dateinamen für die Logdatei
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 */
	public function __construct()
	{
		$this->logFilename = 'aurednik-integration-product-images-import-' . date('y-m-d', time()) . '.log';
	}

}
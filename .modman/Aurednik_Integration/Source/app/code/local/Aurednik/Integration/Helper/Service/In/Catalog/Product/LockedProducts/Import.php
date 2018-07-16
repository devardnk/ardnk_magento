<?php

/**
 * Hilfsklasse, die Mehtoden bereitstellt, um den Import
 * von gesperrten Artikeln zu protokollieren
 *
 * @author egutsche
 *
 */
class Aurednik_Integration_Helper_Service_In_Catalog_Product_LockedProducts_Import extends Aurednik_Integration_Helper_Service_In_Catalog_Product_Import
{

	/**
	 * Konstruktor Definiert den Dateinamen für die Logdatei
	 *
	 * @author egutsche
	 *
	 */
	public function __construct()
	{
		$this->logFilename = 'aurednik-integration-product-lockedproducts-import-' . date('y-m-d', time()) . '.log';
	}
}
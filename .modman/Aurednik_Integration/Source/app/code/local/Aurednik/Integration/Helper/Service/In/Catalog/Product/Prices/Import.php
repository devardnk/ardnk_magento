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
class Aurednik_Integration_Helper_Service_In_Catalog_Product_Prices_Import extends Aurednik_Integration_Helper_Service_In_Catalog_Product_Import
{
	/**
	 * Konstruktor Definiert den Dateinamen für die Logdatei
	 *
	 * @author egutsche
	 */
	public function __construct()
	{
		$logDir= Mage::getBaseDir('log') . '/priceimport/' . date('Y/Y-m/Y-m-d');
		$this->logFilename = '/priceimport/' . date('Y/Y-m/Y-m-d') . '/' . date('Y-m-d_H:i:s') . '_priceimport.log';

		if(!is_dir($logDir))
		{
			mkdir($logDir, 0777, true);
		}
	}
}
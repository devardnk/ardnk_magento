<?php

/**
 * Gesperrte Artikel Cronjob Handler
 *
 * @author egutsche
 *
 */
class Aurednik_Integration_Model_Cronjob_Catalog_Product_LockedProducts_Import extends Aurednik_Integration_Model_Cronjob_Abstract
{
	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Cronjob_Abstract::executeService()
	 */
	protected function executeService()
	{
		return $this->getServiceIn()->execute();
	}


	/**
	 * Gibt den Produktimport-Service zurück
	 *
	 * @author egutsche
	 *
	 * @return SDZeCOM_Integration_Model_Service_In_Catalog_Product_LockedProducts_Import
	 */
	protected function getServiceIn()
	{
		return $this->factory()->getServiceInCatalogLockedProductsImport();
	}
}
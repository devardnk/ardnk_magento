<?php

/**
 * Produktbilder Cronjob Handler
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Model_Cronjob_Catalog_Product_Images_Import extends Aurednik_Integration_Model_Cronjob_Abstract
{

	//------------------------------------public-area-----------------------------------------------------
	//------------------------------------protected-area--------------------------------------------------
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
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return SDZeCOM_Integration_Model_Service_In_Catalog_Product_Images_Import
	 */
	protected function getServiceIn()
	{
		return $this->factory()->getServiceInCatalogProductImagesImport();
	}

	//------------------------------------private-area----------------------------------------------------
}
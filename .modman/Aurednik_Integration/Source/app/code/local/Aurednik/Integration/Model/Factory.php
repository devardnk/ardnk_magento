<?php

/**
 * Factrory Klasse, die verschiendene Import-Services bereitstellt
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Model_Factory
{

	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Setzt den Service für den Produktimport
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return SDZeCOM_Integration_Model_Service_In_Catalog_Product_Import
	 */
	public function getServiceInCatalogProductImport()
	{

		$service = Mage::getModel('aurednik_integration/service_in_catalog_product_import');

		$serviceIoLocal = Mage::getModel('aurednik_integration/service_io_product');

		$mapper = Mage::getModel('aurednik_integration/mapper_io_product');

		$service->setServiceIo($serviceIoLocal);

		$service->setMapper($mapper);

		return $service;

	}


	/**
	 * Setzt den Service für den Produktbilderimport
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return SDZeCOM_Integration_Model_Service_In_Catalog_Product_Images_Import
	 */
	public function getServiceInCatalogProductImagesImport()
	{

		$service = Mage::getModel('aurednik_integration/service_in_catalog_product_images_import');

		$serviceImages = Mage::getModel('aurednik_integration/service_io_product_images');

		$mapper = Mage::getModel('aurednik_integration/mapper_io_product_images');

		$service->setServiceIo($serviceImages);

		$service->setMapper($mapper);

		return $service;
	}


	/**
	 * Setzt den Service für den Produktattributimport
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return SDZeCOM_Integration_Model_Service_In_Catalog_Product_Attributes_Import
	 */
	public function getServiceInCatalogProductAttributesImport()
	{

		$service = Mage::getModel('aurednik_integration/service_in_catalog_product_attributes_import');

		$serviceAttributes = Mage::getModel('aurednik_integration/service_io_product_attributes');

		$mapper = Mage::getModel('aurednik_integration/mapper_io_product_attributes');

		$service->setServiceIo($serviceAttributes);

		$service->setMapper($mapper);

		return $service;
	}


	/**
	 * Setzt den Service für den Produktpreisimport
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return SDZeCOM_Integration_Model_Service_In_Catalog_Product_Prices_Import
	 */
	public function getServiceInCatalogProductPricesImport()
	{

		$service = Mage::getModel('aurednik_integration/service_in_catalog_product_prices_import');

		$servicePrices = Mage::getModel('aurednik_integration/service_io_product_prices');

		$mapper = Mage::getModel('aurednik_integration/mapper_io_product_prices');

		$service->setServiceIo($servicePrices);

		$service->setMapper($mapper);

		return $service;
	}


	/**
	 * Setzt den Service für den Import von gesperrten Artikeln
	 *
	 * @author egutsche
	 *
	 * @return SDZeCOM_Integration_Model_Service_In_Catalog_Product_LockedProducts_Import
	 */
	public function getServiceInCatalogLockedProductsImport()
	{
		$service = Mage::getModel('aurednik_integration/service_in_catalog_product_lockedProducts_import');
		$serviceLocked = Mage::getModel('aurednik_integration/service_io_product_lockedProducts');
		$mapper = Mage::getModel('aurednik_integration/mapper_io_product_lockedProducts');

		$service->setServiceIo($serviceLocked);
		$service->setMapper($mapper);

		return $service;
	}


	/**
	 * Gibt die Hilfsklasse zurück
	 *
	 * @author akniss
	 *
	 * @return Aurednik_Integration_Helper_Data
	 */
	protected function helper()
	{
		return Mage::helper('aurednik_integration');
	}
} 
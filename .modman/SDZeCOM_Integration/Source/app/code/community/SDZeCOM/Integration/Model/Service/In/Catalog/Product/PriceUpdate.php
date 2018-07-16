<?php

/**
 *
 * @author mwalter
 */
class SDZeCOM_Integration_Model_Service_In_Catalog_Product_PriceUpdate
    extends SDZeCOM_Integration_Model_Service_In_Abstract
{
    protected $indexes = array(
        'catalog_product_price' => 1,
        'catalog_product_flat'  => 1,
    );

    /**
     * Process Data
     *
     * @param array $products contains an array of products that need to be process
     * @return array contains error messages
     */
    protected function processData(array $products)
    {
        // Import Using FastSimpleImport
        $import = $this->getProductImporter();
        $import->setPartialIndexing(false);
        $import->setContinueAfterErrors(true);
        $import->setErrorLimit(99999);
        $import->setBehavior(Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE);
        $import->processProductImport($products);

        // Indexes that need to be rebuild after a product import
        $this->processIndexReindex();

        // Set Message to Schedule
        $errorMessages = $import->getErrorMessages();
        return $errorMessages;
    }

    /**
     * @return AvS_FastSimpleImport_Model_Import
     */
    protected function getProductImporter()
    {
        return Mage::getSingleton('fastsimpleimport/import');
    }
}
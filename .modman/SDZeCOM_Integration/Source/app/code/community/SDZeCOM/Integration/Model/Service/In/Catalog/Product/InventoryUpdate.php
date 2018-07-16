<?php
/**
 *
 * @author mwalter
 */
class SDZeCOM_Integration_Model_Service_In_Catalog_Product_InventoryUpdate
    extends SDZeCOM_Integration_Model_Service_In_Abstract
{
    /**
     * Process Data
     *
     * @param array $products contains an array of products that need to be process
     * @return array contains error messages
     */
    protected function processData(array $products)
    {
        $errors = array();
        $productIds = array();

        // UPDATE Stock for Products
        foreach ($products as $stockData) {
            $sku = $stockData['sku'];

            // Check Database for Product
            /** @var Mage_Catalog_Model_Product $product */
            $product = Mage::getModel('catalog/product');
            $productId = $product->getIdBySku($sku);

            if (!$productId) {
                $errors[$sku] = $sku . ': sku not found';
                continue;
            }

            // Load StockItem
            /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
            $stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem = $stockItem->loadByProduct($productId);
            $stockItemId = $stockItem->getId();

            if (!$stockItemId) {
                $errors[$sku] = $sku . ': stockitem not found';
                continue;
            }

            // Update StockItem Data
            foreach ($stockData as $stockDataKey => $value) {
                if ($stockDataKey == 'sku') {
                    continue;
                }
                $stockItem->setData($stockDataKey, $value ? $value : 0);
            }

            // Save Stock
            $stockItem->setProcessIndexEvents(false); // Reindex will be done later
            $stockItem->save();

            // Mark ProductId as imported
            $productIds[] = $productId;

            // Clear Memory
            unset($stockItem);
            unset($product);
        }

        // Reindex Stock Index (an other open IndexEvents)
        $this->processIndexEvents();

        // $this->reindexStockPartial($productIds);

        return $errors;
    }

    /**
     * Process all index Events that where created during the update
     */
    protected function processIndexEvents()
    {
        $pCollection = Mage::getSingleton('index/indexer')->getProcessesCollection();
        foreach ($pCollection as $process) {
            /** @var $process Mage_Index_Model_Process */
            $process->indexEvents();

            // $process->reindexEverything();
        }
    }

    /**
     * Will Reindex Stock index (no other index)
     */
    protected function reindexStockIndex()
    {
        /** @var Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');
        $process = $indexer->getProcessByCode('cataloginventory_stock');
        $process->reindexEverything();
    }

    /**
     * Fastest method for reindexing: will only reindex Stock Items that where updated an nothing else
     * Somehow the index will not be marked as uptodate and will show an reindex required in the admin panel
     *
     * @param array $productIds
     */
    protected function reindexStockPartial(array &$productIds)
    {
        /*
         * Generate a fake mass update event that we pass to our indexers.
         */
        $event = Mage::getModel('index/event');
        $event->setNewData(
              array(
                  'reindex_stock_product_ids' => &$productIds, // for indexer_stock
              )
        );
        /** @var Mage_CatalogInventory_Model_Indexer_Stock $stockIndexer */
        $stockIndexer = Mage::getResourceSingleton('cataloginventory/indexer_stock');
        $stockIndexer->catalogProductMassAction($event);
        $stockIndexer->reindexAll();
    }
}
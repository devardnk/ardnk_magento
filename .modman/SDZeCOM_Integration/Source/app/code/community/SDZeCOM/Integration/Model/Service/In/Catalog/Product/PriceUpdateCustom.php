<?php
/**
 *
 * @author mwalter
 */
class SDZeCOM_Integration_Model_Service_In_Catalog_Product_PriceUpdateCustom
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
        $websites = Mage::app()->getWebsites();

        foreach ($products as $priceData) {
            $sku = $priceData['sku'];

            // Check Database for Product
            /** @var Mage_Catalog_Model_Product $product */
            $product = Mage::getModel('catalog/product');
            $productId = $product->getIdBySku($sku);

            if (!$productId) {
                $errors[$sku] = $sku . ': sku not found';
                continue;
            }

            // Iterate Websites and update Product Price(s)
            foreach ($websites as $website) {
                /** @var Mage_Core_Model_Website $website */
                $websiteCode = $website->getCode();

                if (!array_key_exists($websiteCode, $priceData)) {
                    continue;
                }

                // Website Price Rules
                $websitePrices = $priceData[$websiteCode];

                // Website Price
                $price = null;
                if (array_key_exists('price', $websitePrices)) {
                    $price = $websitePrices['price'];
                }

                // Website Tier Prices
                $tierPrice = null;
                if (array_key_exists('tier_price', $websitePrices)) {
                    $tierPrice = $websitePrices['tier_price'];
                }

                // Continue if no prices available
                if (is_null($price) and is_null($tierPrice)) {
                    continue;
                }

                // Get Store Ids
                $storeIds = $website->getStoreIds();

                if (!count($storeIds)) {
                    continue;
                }

                // Set Price per Store
                foreach ($storeIds as $storeId) {
                    $product->setStoreId($storeId);
                    $product->load($productId);

                    // Update Website Price
                    if ($product->getPrice() != $price and !is_null($price)) {
                        $product->setPrice($price);
                        $product->getResource()->saveAttribute($product, 'price');
                    }

                    // Update Website Tier Prices
                    if (!is_null($tierPrice) and is_array($tierPrice)) {
                        $this->processProductTierPrice($product, $tierPrice, $website);
                    }
                }

                // Mark ProductId as imported
                $productIds[] = $productId;
            }

            unset($product);
        }

        $this->reindexPriceIndex();

        // $this->reindexPriceIndexPartial($productIds);

        return $errors;
    }

    protected function processProductTierPrice(
        Mage_Catalog_Model_Product $product, array $tierPrices, Mage_Core_Model_Website $website
    )
    {
        $customerGroupId = Mage_Customer_Model_Group::CUST_GROUP_ALL; // All customer groups
        $websiteId = $website->getId();

        $productTierPricesData = array();
        foreach ($tierPrices as $key => $tierPrice) {
            $qty = $tierPrice['min_qty'];
            $price = $tierPrice['price'];

            $tierPriceData = array(
                'website_id' => $websiteId,
                'cust_group' => $customerGroupId,
                'price_qty' => $qty,
                'price' => $price
            );

            $productTierPricesData[] = $tierPriceData;
        }

        $product->setTierPrice($productTierPricesData);

        // Call Product save cause using saveAttribute tier_price is not saved
        $product->save();
    }

    /**
     * Will Reindex Stock index (no other index)
     */
    protected function reindexPriceIndex()
    {
        /** @var Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');
        $process = $indexer->getProcessByCode('catalog_product_price');
        $process->reindexEverything();
    }

    /**
     * Fastest method for reindexing: will only reindex Items that where updated an nothing else
     * Somehow the index will not be marked as uptodate and will show an reindex required in the admin panel
     *
     * @param array $productIds
     */
    protected function reindexPriceIndexPartial(array &$productIds)
    {
        /*
         * Generate a fake mass update event that we pass to our indexers.
         */
        $event = Mage::getModel('index/event');
        $event->setNewData(
              array(
                  'reindex_price_product_ids' => &$productIds, // for indexer_stock
              )
        );
        /** @var Mage_Catalog_Model_Product_Indexer_Price $indexer */
        $indexer = Mage::getResourceSingleton('catalog/product_indexer_price');
        $indexer->catalogProductMassAction($event);
        $indexer->reindexAll();
    }
}
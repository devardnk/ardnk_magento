<?php

/**
 * Hilfsklasse, die Mehtoden bereitstellt, um den Importvorgang
 * zu protokollieren
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Helper_Data extends Mage_Core_Helper_Abstract
{

	/**
	 *
	 * @var LOG_FILENAME, Dateiname
	 */
	const LOG_FILENAME = 'aurednik_integration';


	/**
	 * Protkolliert eine Nachricht
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @param $msg , Nachricht, die protokolliert werden soll
	 * @param string $level , Protokoll-Level (Fehlemdelung, Information usw. )
	 */
	public function log($msg, $level = null)
	{
		Mage::log($msg, $level, self::LOG_FILENAME);
	}

	public function getContinueShoppingUrl()
	{
		if (Mage::app()->getRequest()->getModuleName() != 'checkout' || Mage::app()->getRequest()->getControllerName() != 'cart') {
			return false;
		}

		$checkoutSessions = Mage::getSingleton('checkout/session');

		if ($checkoutSessions->getLastCategoryId()) {
			$categoryId = $checkoutSessions->getLastCategoryId();
		} else {
			$collection = $checkoutSessions->getQuote()->getItemsCollection();
			$collection->getSelect()->order('created_at DESC');

			$latestItem = $collection->getLastItem();
			$categoryIds = $latestItem->getProduct()->getCategoryIds();
			$categoryId = end($categoryIds);

		}


		$category = Mage::getModel('catalog/category')->getCollection()
			->addFieldToFilter('entity_id', $categoryId)
			->addUrlRewriteToResult()
			->setPageSize(1)
			->getFirstItem();

		return $category->getUrl($category);
	}
}

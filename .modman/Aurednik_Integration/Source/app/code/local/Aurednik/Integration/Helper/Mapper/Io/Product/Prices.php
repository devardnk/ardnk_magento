<?php

/**
 * Stellt Methoden bereit, um die Produktpreise aus der CSV-Datei
 * auszulesen und in das Magento Import-Format zu mappen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Helper_Mapper_Io_Product_Prices extends Aurednik_Integration_Helper_Mapper_Io_Product
{

	/**
	 *
	 * @var Mage_Catalog_Model_Product
	 */
	protected $product = null;


	/**
	 * (non-PHPdoc)
	 * @see Aurednik_Integration_Helper_Mapper_Io_Product::getType()
	 */
	public function getType()
	{
		$product = $this->getProduct();

		if ($product === false)
		{
			return '';
		}

		return $product->getTypeId();
	}


	/**
	 * (non-PHPdoc)
	 * @see Aurednik_Integration_Helper_Mapper_Io_Product::getAttributeSet()
	 *
	 */
	public function getAttributeSet()
	{
		$product = $this->getProduct();
		if (!$product instanceof SDZeCOM_Aurednik_Model_Product)
		{
			return '';
		}

		/**
		 * @var $attributeSetModel Mage_Eav_Model_Entity_Attribute_Set
		 * @var $product SDZeCOM_Aurednik_Model_Product
		 */
		$attributeSetModel = Mage::getModel("eav/entity_attribute_set");
		$attributeSetModel->load($product->getAttributeSetId());

		return $attributeSetModel->getAttributeSetName();
	}


	/**
	 * Gibt das Produkt zurück
	 *
	 * @author egutsche
	 *
	 * @return bool|Mage_Catalog_Model_Product
	 */
	protected function getProduct()
	{
		if (!is_null($this->product))
		{
			return $this->product;
		}

		$sku = $this->getSku();

		if (strlen(trim($sku)) == 0)
		{
			return false;
		}

		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

		if (is_null($product) || $product === false || $product->getID() <= 0)
		{
			return false;
		}
		$this->product = $product;

		return $this->product;
	}


	/**
	 * Gibt die Produkt Artikelnummer (sku) zurück
	 *
	 * @author egutsche
	 *
	 * @return string sku
	 */
	public function getSku()
	{
		$sku = parent::getSku();
		if (!empty($sku))
		{
			return $sku;
		}

		if (!isset($this->importData['Artikelnr.']))
		{
			return '';
		}

		return trim($this->importData ['Artikelnr.']);
	}


	/**
	 * Gibt den Einzelpreis zurück
	 *
	 * @author egutsche
	 *
	 * @return double Einzelpreis
	 */
	public function getSinglePrice()
	{
		if (!isset ($this->importData ['VK-Preis brutto']))
		{
			return '';
		}
		$tierPriceAmount = $this->getMinAmount();
		if ($tierPriceAmount != 0)
		{
			return '';
		}

		$singlePrice = trim($this->importData ['VK-Preis brutto']);
		$singlePrice = str_replace(array('.', ','), array('', '.'), $singlePrice);

		return $singlePrice;
	}


	/**
	 * Gibt die Mindestmenge zurück
	 *
	 * @author egutsche
	 *
	 * @return string
	 */
	public function getMinAmount()
	{
		if (!isset ($this->importData ['Mindestmenge']))
		{
			return '';
		}

		$minAmount = trim($this->importData ['Mindestmenge']);

		return $minAmount;
	}


	/**
	 * Gibt die Staffelpreise zurück
	 *
	 * @author egutsche
	 *
	 * @return array Staffelpreise
	 */
	public function getTierPrices()
	{
		$tierPriceAmount = $this->getMinAmount();
		$tierPrice = trim($this->importData ['VK-Preis brutto']);
		$tierPrice = str_replace(array('.', ','), array('', '.'), $tierPrice);

		if (!isset($tierPrice) || empty($tierPrice))
		{
			return array();
		}

		if ($tierPriceAmount == 0)
		{
			return 0;
		}

		$tierPrices = array
		(
			'sku' => false,
			'_attribute_set' => $this->getAttributeSet(),
			'_tier_price_qty' => $tierPriceAmount,
			'_tier_price_price' => str_replace(',', '.', $tierPrice),
			'_tier_price_website' => $this->getWebsite(),
			'_tier_price_customer_group' => 'all'
		);

		return $tierPrices;
	}


	/**
	 * (non-PHPdoc)
	 * @see Aurednik_Integration_Helper_Mapper_Io_Product::getStore()
	 */
	public function getStore()
	{
		if (!isset ($this->importData ['Preisgruppencode']) || strlen(trim($this->importData ['Preisgruppencode'])) == 0)
		{
			return '';
		}

		$store = $this->importData['Preisgruppencode'];
		if (strpos($store, 'AT') !== false)
		{
			// Wenn die variante einen AT Preis aufweist, wird der Konfigurierbare artikel auch auf AT gesetzt
			$this->setStoreForParentProduct();
			$store = 'oesterreich_shopview';// 'at_shop_view_at';
		}
		else
		{
			$store = 'de';//'de_shop_view_de';
		}

		if (strpos($store, 'admin') !== false)
		{
			$store = trim(str_replace('admin', '', $store), ',');
		}

		return $store;
	}


	/**
	 * Setzen der Websites für Konfigurierbare Produkte anhand der Variante
	 *
	 * @author egutsche
	 */
	protected function setStoreForParentProduct()
	{
		$childProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $this->getSku());
		$parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($childProduct->getID());

		if(count($parentIds) > 0)
		{
			foreach ($parentIds as $parentId)
			{
				$product = Mage::getModel('catalog/product')->load($parentId);

				// WebsiteIDs: 1 => DE, 2 => AT
				$product->setWebsiteIds(array(2,1));
				$product->save();
			}
		}
	}


	/**
	 * Gibt die Produkt-Webseite zurück
	 *
	 * @author egutsche
	 *
	 * @return string Produkt-Webseite
	 */
	public function getWebsite()
	{
		$website = parent::getWebsite();
		if (!empty($website))
		{
			return $website;
		}

		if (!isset ($this->importData ['Preisgruppencode']))
		{
			return '';
		}

		$website = $this->importData ['Preisgruppencode'];
		if (strpos($website, 'AT') !== false)
		{
			$website = 'oesterreich_shop'; //'at_website';
		}
		else
		{
			$website = 'base'; //'de_website';
		}

		return $website;
	}


	/**
	 * Gibt den Produktstatus zurück
	 *
	 * @author egutsche
	 *
	 * @return string Produktstatus
	 */
	public function getStatus()
	{
		$sku = $this->getSku();

		if (!empty($sku))
		{
			$collection = Mage::getModel('catalog/product')->getCollection();
			$collection->addAttributeToFilter('sku', $sku);
			$collection->addAttributeToSelect('status');
			$collection->addAttributeToSelect('price');
			$collection->addAttributeToSelect('an_379');
			$collection->addAttributeToSelect('an_380');
			$product = $collection->getFirstItem();

			/*
			 * @ticket https://projects.sdzecom.de/issues/5670 
			 * Neue Produkte haben keinen Preis und sollen deaktiviert im Shop ankommen
			 * Aktivierung der Produkte, wenn kein Preis vorliegt
			 *
			 * @author egutsche
			 */
			$product = Mage::getModel('catalog/product')->load($product->getID());
			if(empty($product))
			{
				Mage::log('[No Product found]Preisimport: ' . $sku. ' | ProductID: ' . '', null, date('Y-m-d') . '_priceimport.log');
				return '';
			}

			$productLocked = $this->isProductLocked($product);
			Mage::log('Ist Produkt gesperrt: '. $productLocked .' | ' . $sku . ' | ProductID: ' . $product->getId(), null, date('Y-m-d') . '_priceimport.log');
			$productPrice = $product->getPrice();
			Mage::log('Preisimport: ' . $sku . ' | ProductID: ' . $product->getId(), null, date('Y-m-d') . '_priceimport.log');
			Mage::log('Preis: ' .  $productPrice . ' | ProductID: ' . $product->getId(), null, date('Y-m-d') . '_priceimport.log');

			if(($productPrice == '0.0000' || $productPrice == '0.00') && $productLocked == 'false')
			{
				Mage::log('[Price is 0 and product not locked] Aktiviere Produkt: ' . $sku. ' | ProductID: ' . $product->getId(), null, date('Y-m-d') . '_priceimport.log');
				return 1;
			}

			$status = $product->getAttributeText('status');
			Mage::log('Status: ' . $status . ' | ProductID: ' . $product->getId(), null, date('Y-m-d') . '_priceimport.log');	

			if (empty($status) || $status == 'Disabled' || $status == 'disabled' || $status == '2')
			{
				Mage::log('[Status was disabled before Priceimport] Preisimport: ' . $sku . ' | ProductID: ' . $product->getId(), null, date('Y-m-d') .'_priceimport.log');
				return 2;
			}

			return $product->getStatus();
		}

		return 1;
	}


	/**
	 * Prüft ob der Artikel im Shop nicht verkauft werden darf
	 * @ticket https://projects.sdzecom.de/issues/5670
	 *
	 * @author egutsche
	 *
	 * @param $product
	 * @return bool
	 */
	protected function isProductLocked($product)
	{
		$saleProductInShop = $product->getAttributeText('an_379');
		$showProductInShop = $product->getAttributeText('an_380');

		if ($saleProductInShop == 'false' || $showProductInShop == 'false')
		{
			return 'true';
		}

		return 'false';
	}


	/**
	 * Gibt die Mehrwertsteuer-Id zurück
	 *
	 * @author egutsche
	 *
	 * @return int Mehrwertsteuer-Id
	 */
	public function getTaxClassId ()
	{
		if (!isset($this->importData['MWSt Produktbuchungsgruppe']))
		{
			return 0;
		}

		$tax_class_id = (int)$this->importData['MWSt Produktbuchungsgruppe'];
		$tax_class_id = $this->getTaxClassMapping($tax_class_id);

		return $tax_class_id;
	}


	/**
	 * Gibt die gemappte Magento Mwst-ID zurück
	 *
	 * @author egutsche
	 *
	 * @param $tax_class_id
	 * @return string
	 */
	public function getTaxClassMapping($tax_class_id)
	{

		switch ($tax_class_id) {
			case '19':
				return '7';
			case '7':
				return '5';
			case '20':
				return '7';
			case '10':
				return '5';
			default:
				return '0';
		}
	}
}
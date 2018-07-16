<?php

/**
 * Stellt Methoden bereit, um Produkte aus der CSV-Datei
 * auszulesen und in das Magento Import-Format zu mappen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Helper_Mapper_Io_Product extends Aurednik_Integration_Helper_Data
{

	/**
	 *
	 * @var $importData , Produkt Importdaten
	 */
	protected $importData = array();


	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Setzt die Importdaten
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param $importData , Importdaten
	 */
	public function setImportData(array $importData)
	{
		$this->importData = $importData;
	}


	/**
	 * Gibt den Produkttyp zurück
	 *
	 * @author akniss
	 *
	 * @return string
	 */
	public function getType()
	{

		if (!isset ($this->importData ['type']))
		{
			return '';
		}

		return trim($this->importData ['type']);
	}


	/**
	 * Gibt die Produkt Artikelnummer (sku) zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string sku
	 */
	public function getSku()
	{

		if (!isset ($this->importData ['sku']))
		{
			return '';
		}

		return trim($this->importData ['sku']);
	}


	/**
	 * Prüft, ob das Produkt aus Magento gelöscht werden kann
	 *
	 * @return boolean
	 *
	 * @author akniss
	 * @version 1.00 29.08.2014
	 * @access public
	 */
	public function canProductDelete()
	{
		if (!isset ($this->importData ['cs_1297']) || strlen(trim($this->importData ['cs_1297'])) == 0)
		{
			return false;
		}

		$delete = (int)$this->importData ['cs_1297'];

		return $delete == 0 ? true : false;
	}


	/**
	 * Gibt die Produkt-Webseite zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Produkt-Webseite
	 */
	public function getWebsite()
	{

		if (!isset ($this->importData ['websites']))
		{
			return '';
		}

		return $this->importData ['websites'];
	}


	/**
	 * Gibt den Produkt-Store zurück
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Produkt-Store
	 */
	public function getStore()
	{

		if (!isset ($this->importData ['store']))
		{
			return '';
		}

		return $this->importData ['store'];
	}


	/**
	 * Gibt das Produkt-Attributset zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Produkt-Attributeset
	 */
	public function getAttributeSet()
	{

		if (!isset ($this->importData ['attribute_set']))
		{
			return '';
		}

		return $this->importData ['attribute_set'];
	}


	/**
	 * liefert Produkt-CategorieIds zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Produkt-CategorieIds
	 */
	public function getCategoryIds()
	{

		if (!isset ($this->importData ['category_ids']))
		{
			return array();
		}

		return explode(',', $this->importData ['category_ids']);
	}


	/**
	 * Gibt den Produktnamen zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Produktname
	 */
	public function getName()
	{

		if (!isset ($this->importData ['name']))
		{
			return '';
		}

		return $this->importData ['name'];
	}


	/**
	 * Gibt den Produktstatus zurück
	 *
	 * @author akniss
	 
	 * @return string Produktstatus
	 */
	public function getStatus()
	{

		/*
		 * @ticket https://projects.sdzecom.de/issues/5670 - Neue Produkte haben keinen Preis
		 * und sollen deaktiviert im Shop ankommen
		 * @author egutsche
		 */
		$productId = Mage::getModel('catalog/product')->getIdBySku($this->getSku());
		Mage::log('Verarbeite SKU: ' . $this->getSku() . ' | ProductID: ' . $productId, null, '02_sdz.log');
		if($productId == false)
		{
			Mage::log('Deaktiviere : ' . $this->getSku() . ' | ProductID: ' . $productId, null, '02_sdz.log');
			return 2;
		}

		if (!isset ($this->importData ['status']))
		{
			return '';
		}

		if ($this->isProductLocked())
		{
			return 2;
		}

		return $this->importData ['status'];
	}


	/**
	 * Prüft ob der Artikel im Shop nicht verkauft werden darf
	 *
	 * @author egutsche
	 *
	 * @return bool
	 */
	protected function isProductLocked()
	{
		$saleProductInShop = $this->importData ['an_379'];
		$showProductInShop = $this->importData ['an_380'];

		if ($saleProductInShop == '379_false' && $showProductInShop == '380_false')
		{
			return true;
		}

		return false;
	}


	/**
	 * Gibt die Produkt Sichtbarkeit zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Produkt Sichtbarkeit
	 */
	public function getVisibility()
	{

		if (!isset ($this->importData ['visibility']))
		{
			return '';
		}

		return $this->importData ['visibility'];
	}


	/**
	 * Gibt die Produkt Position zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Produkt Sichtbarkeit
	 */
	public function getPosition()
	{
		if (!isset ($this->importData ['sortorder']))
		{
			return 1;
		}

		return (int)$this->importData ['sortorder'];
	}


	/**
	 * Gibt die Produktkurzbeschreibung
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 *
	 * @return string Produktkurzbeschreibung
	 */
	public function getShortDescription()
	{

		if (!isset ($this->importData ['short_description']) || strlen(trim($this->importData ['short_description'])) == 0)
		{
			return '-';
		}

		return $this->importData ['short_description'];
	}


	/**
	 * Gibt die Produktbeschreibung
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Produktbeschreibung
	 */
	public function getDescription()
	{

		if (!isset ($this->importData ['description']) || strlen(trim($this->importData ['description'])) == 0)
		{
			return '-';
		}

		return $this->importData ['description'];
	}


	/**
	 * Gibt das Produktgewicht
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Produktgewicht
	 */
	public function getWeight()
	{

		if (!isset ($this->importData ['weight']) || strlen(trim($this->importData ['weight'])) == 0)
		{
			return 0;
		}

		return str_replace(',', '.', $this->importData ['weight']);
	}


	/**
	 * Gibt die Artikelnummern der Produktvarianten zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return array  Artikelnummern der Produktvarianten
	 */
	public function getSimpleSkus()
	{

		if (!isset ($this->importData ['simple_skus']) || strlen(trim($this->importData ['simple_skus'])) == 0)
		{
			return array();
		}

		return array_unique(explode(',', $this->importData ['simple_skus']));
	}


	/**
	 * Gibt die Attribute zurück,
	 * über die ein Konfigurierbares Produkt sich konfigurieren lässt
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return array Attribute
	 */
	public function getConfigurableAttributes()
	{

		if (!isset ($this->importData ['configurable_attributes']) || strlen(trim($this->importData ['configurable_attributes'])) == 0)
		{
			return array();
		}

		return explode(',', $this->importData ['configurable_attributes']);
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
		$sku = $this->getSku();
		if (!empty($sku))
		{
			$collection = Mage::getModel('catalog/product')->getCollection();
			$collection->addAttributeToFilter('sku', $sku);
			$collection->addAttributeToSelect('price');
			$product = $collection->getFirstItem();
			$prodSku = $product->getSku();

			if (!empty($prodSku))
			{
				$productPrice = $product->getPrice();

				return $productPrice;
			}

			return 0.0;
		}
	}


	/**
	 * Gibt die Staffelpreise zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return array Staffelpreise
	 */
	public function getTierPrices()
	{

		if (!isset ($this->importData ['tier_price:_all_']) || strlen(trim($this->importData ['tier_price:_all_'])) == 0)
		{
			return array();
		}

		$importTierPrices = explode(';', $this->importData ['tier_price:_all_']);

		if (!is_array($importTierPrices) || count($importTierPrices) == 0)
		{
			return array();
		}

		$tierPrices = array();

		foreach ($importTierPrices as $currTierPrice)
		{

			$tmp = explode(':', $currTierPrice);

			if (!is_array($tmp) || count($tmp) != 2)
			{
				continue;
			}

			$tierPrices [] = array(
				'sku' => false,
				'_attribute_set' => $this->getAttributeSet(),
				'_tier_price_qty' => $tmp [0],
				'_tier_price_price' => str_replace(',', '.', $tmp [1]),
				'_tier_price_website' => $this->getWebsite(),
				'_tier_price_customer_group' => 'all');
		}

		return $tierPrices;
	}


	/**
	 * Gibt die Liefermenge zurück
	 *
	 * @return int Liefermenge
	 *
	 * @author akniss
	 * @version 1.0
	 * @version 2.0 09.09.2014
	 * @access public
	 *

	 */
	public function getQty()
	{

		if (!isset ($this->importData ['qty']) || strlen(trim($this->importData ['qty'])) == 0)
		{
			return '';
		}

		return $this->importData ['qty'];
	}


	/**
	 * Gibt das Thumbmail-Bild zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Thumbnail-Bild
	 */
	public function getThumbnailImage()
	{

		if (!isset ($this->importData ['thumbnail']) || strlen(trim($this->importData ['thumbnail'])) == 0)
		{
			return '';
		}

		return $this->importData ['thumbnail'];
	}


	/**
	 * Gibt das Small-Image zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Image-Bild
	 */
	public function getSmallImage()
	{

		if (!isset ($this->importData ['small_image']) || strlen(trim($this->importData ['small_image'])) == 0)
		{
			return '';
		}

		return $this->importData ['small_image'];
	}


	/**
	 * Gibt das Base-Image (Standard Bild) zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Base-Image
	 */
	public function getBaseImage()
	{

		if (!isset ($this->importData ['image']) || strlen(trim($this->importData ['image'])) == 0)
		{
			return '';
		}

		return $this->importData ['image'];
	}


	/**
	 * Gibt die Galeriebilder zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Galeriebilder
	 */
	public function getGalleryImages()
	{

		if (!isset ($this->importData ['media_gallery']) || strlen(trim($this->importData ['media_gallery'])) == 0)
		{
			return '';
		}

		return $this->importData ['media_gallery'];
	}


	/**
	 * Gibt zurück, ob ein Artikel auf Lager ist
	 *
	 * '1' -> Produkt auf Lager
	 * '0' -> Produkt nicht auf Lager
	 *
	 * @author egutsche
	 *
	 */
	public function inStock()
	{
		$locked = $this->isProductLocked();

		if ($locked)
		{
			return 0;
		}

		return 1;
	}


	/**
	 * Gibt benutzeridentifizierte Produktattribute zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return array benutzeridentifizierte Produtktattribute
	 */
	public function getCustomAttributes()
	{

		if (!is_array($this->importData) || count($this->importData) == 0)
		{
			return array();
		}

		$customerAttributesData = array();

		$prefix = $this->getCustomAttributesPrefix();


		foreach ($this->importData as $attrCode => $attrValue)
		{

			$attrValue = trim($attrValue);
			$attrValue = $this->removeCsIdPrefix($attrValue);
			if (!$this->isCustomAttribute($prefix, $attrCode) || strlen($attrValue) == 0)
			{
				continue;
			}

			$customerAttributesData [$attrCode] = $attrValue;
		}

		return $customerAttributesData;
	}

	//------------------------------------protected-area--------------------------------------------------


	/**
	 * Id Prefix aus Contentserv Export entfernen,
	 * da die Attributswerte bei Aurednik nicht übersetzt werden und der Prefix im Frontend ungewünscht ist
	 *
	 * @param string , Attributwert mit ID Prefix
	 * @return string Attributwert ohne führende CS Id
	 *
	 * @author egutsche
	 * @version 1.0 2015-07-13
	 *
	 * @access protected
	 */
	protected function removeCsIdPrefix($attrValue)
	{
		$attrValue = preg_replace("/[0-9]*_/", "", $attrValue);
		return $attrValue;
	}


	/**
	 * Gibt den Prefix der benutzeridentifizierte Produktattribute zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return string Prefix der Produktattribute
	 */
	protected function getCustomAttributesPrefix()
	{
		$configValue = Mage::getStoreConfig('aurednik_integration/catalog_product_import/custom_attributes_prefix');

		return is_null($configValue) ? '' : trim($configValue);
	}


	/**
	 * Prüft, ob es um ein benutzeridentifiziertes Produktattribut handelt
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 *
	 * @param string $prefix , Prefix der benutzeridentifizierter Produktattribute
	 * @param string $attrCode , Attributcode des Produktattributes
	 *
	 * @return boolean true | false
	 */
	protected function isCustomAttribute($prefix, $attrCode)
	{
		return strpos($attrCode, $prefix) !== false ? true : false;

	}

	//------------------------------------private-area----------------------------------------------------

}
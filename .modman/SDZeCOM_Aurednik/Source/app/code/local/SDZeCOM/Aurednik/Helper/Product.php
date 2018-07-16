<?php

/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Aurednik_Helper_Product extends SDZeCOM_Aurednik_Helper_Data
{

	/**
	 *
	 * @var $strProductType
	 */
	private $strProductType = "";

	/**
	 *
	 * @var $arrConfigOptions
	 */
	private $arrConfigOptions = array();

	/**
	 *
	 * @var $dblShippingCostsStockItems
	 */
	private $dblShippingCostsStockItems = null;

	/**
	 *
	 * @var $intStoreId
	 */
	private $intStoreId = 0;

	/**
	 *
	 * @var SDZeCOM_Aurednik_Model_Product $objProduct
	 */
	private $objProduct = null;

	/**
	 *
	 * @var $arrProductWarningNotices
	 */
	private $arrProductWarningNotices = array();

	/**
	 * @var array $additionalOrderAttributes
	 */
	protected $additionalOrderAttributes = array();

//----------------------- public area --------------------------------------------------------------------------------------------


	/**
	 * Konstruktor
	 */
	public function __construct($intProductId, $intStoreId = 0)
	{
		//StoreId setzen
		$this->intStoreId = $intStoreId;

		$arrConfigOptions = array(
			self :: SHIPPING_CONFIG_OPTIONS,
			self :: SHIPPING_CONFIG_OPTIONS . "/" . self::PRODUCT_FREIGHTCOST_ADDITIONAL_INFO_DE_ATTR,
			self :: SHIPPING_CONFIG_OPTIONS . "/" . self::PRODUCT_FREIGHTCOST_ADDITIONAL_INFO_AT_ATTR,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_TYPES_CONFIG_OPTIONS,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_CONFIG_OPTIONS,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_CONFIG_OPTIONS,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_CONFIG_OPTIONS,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_CONFIG_OPTIONS,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_FURNITURE_CONFIG_OPTIONS,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_DIRECT_ARTICLE_CONFIG_OPTIONS,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_OUTSIDE_PLAY_CONFIG_OPTIONS,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_CONFIG_OPTIONS,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_WARNING_NOTICES_CONFIG_OPTIONS,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: CATALOG_PRODUCT_PDF_SETTINGS,

			self :: AUREDNIK_CONFIG_OPTIONS . "/" . 'product_additional_order_data'

		);


		foreach ($arrConfigOptions as $strCurrentOption)
		{

			$arrTmp = Mage:: getStoreConfig($strCurrentOption, $this->intStoreId);

			if (!is_array($arrTmp) || count($arrTmp) == 0)
			{
				continue;
			}

			$this->arrConfigOptions = array_merge($this->arrConfigOptions, $arrTmp);

		}

		if (is_null($this->arrConfigOptions) || $this->arrConfigOptions === false)
		{
			$this->arrConfigOptions = array();

			return;
		}

		//Produkttyp Möbel
		if (isset ($this->arrConfigOptions [self :: PRODUCT_TYPE_FURNITURE]))
		{
			$this->arrConfigOptions [self :: PRODUCT_TYPE_FURNITURE] =
				strtolower(str_replace(array(" ", "\r", "\n", "\t"), "", $this->arrConfigOptions [self :: PRODUCT_TYPE_FURNITURE]));
		}

		//Produkttyp Lagerware
		if (isset ($this->arrConfigOptions [self :: PRODUCT_TYPE_STOCK_ITEM]))
		{
			$this->arrConfigOptions [self :: PRODUCT_TYPE_STOCK_ITEM] =
				strtolower(str_replace(array(" ", "\r", "\n", "\t"), "", $this->arrConfigOptions [self :: PRODUCT_TYPE_STOCK_ITEM]));
		}

		//Produkttyp Direktartikel
		if (isset ($this->arrConfigOptions [self :: PRODUCT_TYPE_DIRECT_ARTICLE]))
		{
			$this->arrConfigOptions [self :: PRODUCT_TYPE_DIRECT_ARTICLE] =
				strtolower(str_replace(array(" ", "\r", "\n", "\t"), "", $this->arrConfigOptions [self :: PRODUCT_TYPE_DIRECT_ARTICLE]));
		}

		//Produkttyp Außenspiel
		if (isset ($this->arrConfigOptions [self :: PRODUCT_TYPE_OUTSIDE_PLAY]))
		{
			$this->arrConfigOptions [self :: PRODUCT_TYPE_OUTSIDE_PLAY] =
				strtolower(str_replace(array(" ", "\r", "\n", "\t"), "", $this->arrConfigOptions [self :: PRODUCT_TYPE_OUTSIDE_PLAY]));
		}

		//Produkttyp Speditionsartikel
		if (isset ($this->arrConfigOptions [self :: PRODUCT_TYPE_FORWARDING_GOOD]))
		{
			$this->arrConfigOptions [self :: PRODUCT_TYPE_FORWARDING_GOOD] =
				strtolower(str_replace(array(" ", "\r", "\n", "\t"), "", $this->arrConfigOptions [self :: PRODUCT_TYPE_FORWARDING_GOOD]));
		}

		//Produkt setzen

		$this->setProduct($intProductId);
	}


	/**
	 * @author akniss, egutsche
	 *
	 * @version 1.1
	 *
	 * @access public
	 *
	 * @param Mage_Catalog_Model_Product $objProduct
	 *
	 * setzt das Produkt
	 *
	 */
	public function setProduct($objProduct)
	{

		if ($objProduct == false)
		{
			return;
		}

		if (is_numeric($objProduct))
		{
			$objProduct = parent:: getProduct($objProduct, $this->intStoreId);
		}


		if (!($objProduct instanceof Mage_Catalog_Model_Product))
		{
			return;
		}

		$this->objProduct = $objProduct;
		$strProductType = null;
		//Produkttyp setzen	
		if (isset ($this->arrConfigOptions [self :: SHIPPING_VARIANTS_ATTR]))
		{
			$strProductType = $this->objProduct->getAttributeText(
				$this->arrConfigOptions [self :: SHIPPING_VARIANTS_ATTR]);
		}

		if (!is_null($strProductType) || $strProductType !== false || strlen($strProductType) > 0)
		{
			$this->strProductType =
				strtolower(str_replace(array(" ", "\r", "\n", "\t"), "", $strProductType));
		}

		//Versandpreis für die Lagerware setzen
		if (isset ($this->arrConfigOptions [self :: SHIPPING_COSTS_STOCKITEM]))
		{
			$this->dblShippingCostsStockItems =
				$this->arrConfigOptions [self :: SHIPPING_COSTS_STOCKITEM];
		}

		// Mindestbestellwert für freien Versand
		if (isset ($this->arrConfigOptions [self :: MIN_ORDER_VALUE_FREE_SHIPPING_STOCKITEM]))
		{
			$this->intMinOrderValueForFreeShippingStockItems =
				$this->arrConfigOptions [self :: MIN_ORDER_VALUE_FREE_SHIPPING_STOCKITEM];
		}

		$this->setProductWarningNotices();
		$this->setAdditionalProductOrderData();
	}


	/**
	 * Gibt den Sperrstatus eines Produktes zurück
	 * Falls ein Artikel nicht mehr lieferbar ist und der Bestelbutton deaktiviert werden soll
	 *
	 * @author egutsche
	 *
	 * @return boolean True falls das Produkt gesperrt ist, ansonsten False
	 */
	public function getProductLockStatus()
	{
		$productLockStatus = $this->objProduct->getAttributeText('an_381');
		$prodShopSaleStatus = $this->objProduct->getAttributeText('an_379');
		$prodVisibilityStatus = $this->objProduct->getAttributeText('an_380');

		if ($this->isProductLocked($productLockStatus, $prodShopSaleStatus, $prodVisibilityStatus))
		{
			return true;
		}

		return false;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt ein Möbelstück ist
	 *
	 * @return boolean TRUE falls das Produkt ein Möbelstück ist | FALSE
	 */
	public function isFurniture()
	{
		if (!isset ($this->arrConfigOptions [self :: PRODUCT_TYPE_FURNITURE]))
		{
			return false;
		}

		return SDZeCOM_Library_Helper_String:: isEqual(
			$this->strProductType, $this->arrConfigOptions [self :: PRODUCT_TYPE_FURNITURE]);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt ein Direktartikel ist
	 *
	 * @return boolean TRUE falls das Produkt ein Direktartikel ist | FALSE
	 */
	public function isDirectArticle()
	{
		if (!isset($this->arrConfigOptions[self::PRODUCT_TYPE_DIRECT_ARTICLE]))
		{
			return false;
		}

		if (strcmp($this->strProductType, $this->arrConfigOptions[self::PRODUCT_TYPE_DIRECT_ARTICLE]) == 0)
		{
			return true;
		}
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt ein Außenspiel ist
	 *
	 * @return boolean TRUE falls das Produkt ein Außenspiel ist | FALSE
	 */
	public function isOutsidePlay()
	{

		if (!isset ($this->arrConfigOptions [self :: PRODUCT_TYPE_OUTSIDE_PLAY]))
		{
			return false;
		}

		return SDZeCOM_Library_Helper_String:: isEqual(
			$this->strProductType, $this->arrConfigOptions [self :: PRODUCT_TYPE_OUTSIDE_PLAY]);

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt eine Lagerware ist
	 *
	 * @return boolean TRUE falls das Produkt eine Lagerware ist | FALSE
	 */
	public function isStockItem()
	{

		if (!isset ($this->arrConfigOptions [self :: PRODUCT_TYPE_STOCK_ITEM]))
		{
			return false;
		}

		return SDZeCOM_Library_Helper_String:: isEqual(
			$this->strProductType, $this->arrConfigOptions [self :: PRODUCT_TYPE_STOCK_ITEM]);

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt eine  ist
	 *
	 * @param int $intProductId
	 * @param int $intStoreId
	 *
	 * @return boolean TRUE falls das Produkt eine Speditionsware ist | FALSE
	 */
	public function isFreightForwardingGood()
	{

		if (!isset ($this->arrConfigOptions [self :: PRODUCT_TYPE_FORWARDING_GOOD]))
		{
			return false;
		}

		return SDZeCOM_Library_Helper_String:: isEqual(
			$this->strProductType, $this->arrConfigOptions [self :: PRODUCT_TYPE_FORWARDING_GOOD]);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt eine  ist
	 *
	 * @return boolean TRUE falls das Produkt eine Speditionsware ist | FALSE
	 */
	public function getProductType()
	{
		return $this->strProductType;
	}


	/**
	 * Gibt die Versandkosten für Versand eines Artikels der Gruppe Lagerware
	 *
	 * @ticket https://projects.sdzecom.de/issues/5978
	 * @author akniss , egutsche (#5978)
	 *
	 * @return double $dblShippingCosts Versandkosten für Versand eines Artikels der Gruppe Lagerware | FALSE, falls keiner existiert
	 */
	public function getFixedFreightShippingCosts($website = 0)
	{

		if (!(isset ($this->arrConfigOptions [self :: SHIPPING_FREIGHT_YESNO_ATTR]) &&
			isset ($this->arrConfigOptions [self :: SHIPPING_FREIGHT_ATTR_DE])))
		{

			return 0;
		}

		if (!$this->hasFreightCosts())
		{
			return 0;
		}

		// AT STORE
		if ($website == '2')
		{
			$atFreightCosts = $this->objProduct->getData($this->arrConfigOptions[self::SHIPPING_FREIGHT_ATTR_AT]);
			$atFreightCosts = str_replace(',', '.', $atFreightCosts);

			return $atFreightCosts;
		}

		$deFreightCosts = $this->objProduct->getData($this->arrConfigOptions[self::SHIPPING_FREIGHT_ATTR_DE]);

		//#5978
		$deFreightCosts = str_replace(',', '.', $deFreightCosts);

		return $deFreightCosts;
	}


	/**
	 * Prüft, ob ein Produkt Frachtkosten besitzt
	 *
	 * @author akniss, egutsche
	 *
	 * @return true falls ein Produkt Frachtkosten enthält | wenn keine
	 *         Frachtkosten vorhanden sind FALSE
	 */
	public function hasFreightCosts()
	{
		if (!isset($this->arrConfigOptions[self::SHIPPING_FREIGHT_YESNO_ATTR]))
		{
			return false;
		}

		$intHasFreightCosts = trim($this->objProduct->getAttributeText($this->arrConfigOptions[self::SHIPPING_FREIGHT_YESNO_ATTR]));

		if ($intHasFreightCosts == 'ja' || $intHasFreightCosts == 'Ja')
		{
			return true;
		}

		return false;
	}


	/**
	 * Gibt die Versandkosten für den Deutschen Versand eines Artikels
	 *
	 * @author egutsche
	 *
	 * @return double $dblShippingCosts Versandkosten für Deutschland
	 */
	public function getFreightShippingCostsDe()
	{

		if (!$this->hasFreightCosts())
		{
			return 0;
		}

		return ( double )$this->objProduct->getData($this->arrConfigOptions [self :: SHIPPING_FREIGHT_ATTR_DE]);
	}


	/**
	 * Gibt die Versandkosten für den AT Versand eines Artikels
	 *
	 * @author egutsche
	 *
	 * @return double $dblShippingCosts Versandkosten für AT
	 */
	public function getFreightShippingCostsAt()
	{

		if (!$this->hasFreightCosts())
		{
			return 0;
		}

		return ( double )$this->objProduct->getData($this->arrConfigOptions [self :: SHIPPING_FREIGHT_ATTR_AT]);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt das zu Produkt zurück
	 *
	 * @return Mage_Catalog_Model_Product Produkt
	 */
	public function getProduct($intProductId = 0, $intStoreId = 0)
	{
		return $this->objProduct;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt den mindest Bestellwert für den freien Versand zurück (Lagerware)
	 *
	 * @return double Produkt
	 */
	public function getMinOrderValueForFreeShippingStockItems()
	{
		return ( double )$this->dblMinOrderValueForFreeShippingStockItems;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück
	 *
	 * @return string Text für Produktverfügbarkeit
	 */
	public function getProductAvailabilityText()
	{
		if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_ATTR]))
		{
			return "";
		}
		$strProductAvailability = trim($this->objProduct->getAttributeText($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_ATTR]));
		$strProductAvailability = substr($strProductAvailability, 0, 1);

		if (strlen($strProductAvailability) == 0)
		{
			return "";
		}
		if ($this->isStockItem())
		{
			return $this->getProductAvailabilityTextStockItems($strProductAvailability);
		}
		else
		{
			if ($this->isFurniture())
			{
				return $this->getProductAvailabilityTextFurniture($strProductAvailability);
			}
			else
			{
				if ($this->isDirectArticle())
				{
					return ( string )$this->getProductAvailabilityTextDirectArticle($strProductAvailability);
				}
				else
				{
					if ($this->isOutsidePlay())
					{
						return ( string )$this->getProductAvailabilityTextOutsidePlay($strProductAvailability);
					}
					else
					{
						if ($this->isFreightForwardingGood())
						{
							return ( string )$this->getProductAvailabilityTextFreightForwardingGoods($strProductAvailability);
						}
					}
				}
			}
		}
		return "";
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt den Text für die Lieferzeit zurück
	 *
	 * @return string Text für Lieferzeit
	 */
	public function getProductShippingText()
	{


		if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_ATTR]))
		{
			return "";
		}
		$strProductShipping = trim($this->objProduct->getAttributeText($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_ATTR]));
		$strProductShipping = substr($strProductShipping, 0, 1);
		if (strlen($strProductShipping) == 0)
		{
			return "";
		}
		if ($this->isStockItem())
		{
			return $this->getProductShippingTextStockItems($strProductShipping);
		}
		return "";
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt das Text für die Produktverfügbarkeit zurück
	 *
	 * @return string Text für Produktverfügbarkeit
	 */
	public function getProductAvailabilityPicture()
	{

		if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_ATTR]))
		{
			return "";
		}

		$strProductAvailability = trim($this->objProduct->getAttributeText($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_ATTR]));
		$strProductAvailability = substr($strProductAvailability, 0, 1);
		if (strlen($strProductAvailability) == 0)
		{
			return "";
		}

		if ($this->isStockItem())
		{
			return ( string )$this->getProductAvailabilityPictureStockItems($strProductAvailability);
		}

		return "";
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt die Farbe für den Produktverfügbarkeitstext zurück
	 *
	 * @return string Text-color für Produktverfügbarkeit
	 */
	public function getProductAvailabilityTextColor($fallback_color = '#000000')
	{
		if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_ATTR]))
		{
			return $fallback_color;
		}

		$strProductAvailability = trim($this->objProduct->getAttributeText($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_ATTR]));
		$strProductAvailability = substr($strProductAvailability, 0, 1);
		if (strlen($strProductAvailability) == 0)
		{
			return $fallback_color;
		}


		if ($this->isStockItem())
		{

			return $this->getProductAvailabilityTextColorStockItems($strProductAvailability, $fallback_color);
		}

		return $fallback_color;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück
	 *
	 * @return string Text für Produktverfügbarkeit
	 */
	public function getProductAvailabilityPictureUrl()
	{
		return SDZeCOM_Library_Helper_Directory:: joinPaths(
			Mage:: getBaseUrl(Mage_Core_Model_Store :: URL_TYPE_MEDIA), $this->getProductAvailabilityPicture()
		);
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt die relative Url für das PDF Icon zurück
	 *
	 * @return string|$arrConfigOptions
	 */
	public function getProductPDFPicture()
	{

		if (!isset ($this->arrConfigOptions [self :: PRODUCT_PDF_FILE_PICTURE]))
		{
			return "";
		}
		$strProductPDFPicture = $this->arrConfigOptions [self ::PRODUCT_PDF_FILE_PICTURE];
		if (strlen($strProductPDFPicture) == 0)
		{
			return "";
		}
		else
		{

			return $strProductPDFPicture;
		}
		return "";
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt die komplette URL für das PDF Icon zurück
	 *
	 * @return Ambigous <string, mixed>
	 */
	public function getProductPDFPictureUrl($url)
	{
		return SDZeCOM_Library_Helper_Directory::joinPaths(
			Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA), $this->getProductPDFPicture()
		);
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param boolean $url , falls true wird url zurückgegeben
	 *
	 * gibt die Url der Katalog- PDF  zurück
	 *
	 * @return Ambigous <string, mixed>
	 */
	public function getProductPDFilePath($url)
	{

		if (!isset ($this->arrConfigOptions [self :: PRODUCT_PDF_FILE_PATH]))
		{
			return '';
		}
		$filePath = $this->arrConfigOptions [self :: PRODUCT_PDF_FILE_PATH];
		$filePath = SDZeCOM_Library_Helper_Directory::joinPaths(Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA), $filePath);

		if (!file_exists($filePath))
		{
			@mkdir($filePath);
		}
		if ($url)
		{
			return SDZeCOM_Library_Helper_Directory:: joinPaths(
				Mage:: getBaseUrl(Mage_Core_Model_Store :: URL_TYPE_MEDIA), $this->arrConfigOptions [self :: PRODUCT_PDF_FILE_PATH], $this->getProductPDFileName()
			);
		}

		$filePath = SDZeCOM_Library_Helper_Directory::joinPaths($filePath, $this->getProductPDFileName());
		return $filePath;
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt den Dateinamen der PDF zurück
	 *
	 * @return string
	 */
	public function getProductPDFileName()
	{

		if (!isset($this->arrConfigOptions[self::PRODUCT_PDF_FILE_ATTRIBUTE]))
		{
			return '';
		}

		$strPdFileName = trim($this->objProduct->getData($this->arrConfigOptions[self::PRODUCT_PDF_FILE_ATTRIBUTE]));
		return $strPdFileName;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob das Produkt Warnhinweise besitzt
	 *
	 * @return boolean TRUE | FALSE
	 */
	public function hasProductWarningNotices()
	{
		return is_array($this->arrProductWarningNotices) && count($this->arrProductWarningNotices) > 0 ? true : false;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt die Produkt Warnhinweise zurück
	 *
	 * @return array mit den Warnhinweisen
	 */
	public function getProductWarningNotices()
	{

		if (!$this->hasProductWarningNotices())
		{
			return array();
		}

		return $this->arrProductWarningNotices;
	}


	/**
	 * Gibt die Anmerkungen für Frachtkosten (DE, AT) zurück
	 *
	 * @author egutsche
	 *
	 * @param $freightInfoAttribute , Attribut für Frachtkostenanmerkung (DE, AT)
	 * @return string
	 */
	public function getAdditionalFreightCostInformation($freightInfoAttribute)
	{
		$freightDEConfig = $this->arrConfigOptions[self::PRODUCT_FREIGHTCOST_ADDITIONAL_INFO_DE_ATTR];
		$freightATConfig = $this->arrConfigOptions[self::PRODUCT_FREIGHTCOST_ADDITIONAL_INFO_AT_ATTR];
		$freightcostAdditionalInfo = '';

		if (!isset($freightDEConfig) || !isset($freightATConfig))
		{
			return "";
		}

		if (strcmp($freightInfoAttribute, $freightDEConfig) == 0)
		{
			$freightcostAdditionalInfo = trim($this->objProduct->getData($freightDEConfig));
		}
		elseif (strcmp($freightInfoAttribute, $freightATConfig) == 0)
		{
			$freightcostAdditionalInfo = trim($this->objProduct->getData($freightATConfig));
		}

		return $freightcostAdditionalInfo;
	}


	/**
	 * @TODO Kommentar
	 *
	 * @ticket
	 * @author Eugen Gutsche
	 */
	public function setAdditionalProductOrderData()
	{
		if (!isset($this->arrConfigOptions['productadditionalorderdataattribute']))
		{
			return $this;
		}

		$additionalProductDataAttrCodes = trim($this->arrConfigOptions['productadditionalorderdataattribute']);
		if (strlen($additionalProductDataAttrCodes) == 0)
		{
			return $this;
		}

		$additionalProductDataAttrCodes = explode(',', $additionalProductDataAttrCodes);
		if (!is_array($additionalProductDataAttrCodes) || count($additionalProductDataAttrCodes) == 0)
		{
			return $this;
		}

		foreach ($additionalProductDataAttrCodes as $additionalProductDataAttrCode)
		{
			$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $additionalProductDataAttrCode);
			$attributeType = $attribute->getFrontendInput();
			switch ($attributeType)
			{
				case 'select':
					$attributeValue = $this->objProduct->getAttributeText($additionalProductDataAttrCode);
					break;
				default:
					$attributeValue = $this->objProduct->getData($additionalProductDataAttrCode);
			}

			$this->additionalOrderAttributes[$additionalProductDataAttrCode] = $attributeValue;
		}

		return $this;
	}


	public function getAdditionalProductOrderData()
	{

		if (!$this->hasAdditionalProductOrderData())
		{
			return array();
		}

		return $this->additionalOrderAttributes;
	}


	public function hasAdditionalProductOrderData()
	{
		return is_array($this->additionalOrderAttributes) && count($this->additionalOrderAttributes) > 0 ? true : false;
	}


	/**
	 * Prüft ob der Artikel gesperrt ist und im Shop nicht verkauft werden darf
	 *
	 * @author egutsche
	 *
	 * @return bool
	 */
	protected function isProductLocked($productLockStatus, $prodShopSaleStatus, $prodVisibilityStatus)
	{
		if ($productLockStatus == 'true')
		{
			return true;
		}

		if ($prodShopSaleStatus == 'false' && $prodVisibilityStatus == 'true')
		{
			return true;
		}

		return false;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * setzt Produkt Warnhinweise
	 *
	 */
	protected function setProductWarningNotices()
	{

		if (!isset ($this->arrConfigOptions [self :: PRODUCT_WARNING_NOTICES_ATTR]))
		{
			return $this;
		}

		$strProductWarningNoticesAttrCodes = trim($this->arrConfigOptions [self :: PRODUCT_WARNING_NOTICES_ATTR]);

		if (strlen($strProductWarningNoticesAttrCodes) == 0)
		{
			return $this;
		}

		$strProductWarningNoticesAttrCodes = explode(',', $strProductWarningNoticesAttrCodes);

		if (!is_array($strProductWarningNoticesAttrCodes) || count($strProductWarningNoticesAttrCodes) == 0)
		{
			return $this;
		}

		foreach ($strProductWarningNoticesAttrCodes as $strProductWarningNoticeAttrCode)
		{

			$intWarningNoticeId = trim($this->objProduct->getAttributeText($strProductWarningNoticeAttrCode));

			if (strlen($intWarningNoticeId) == 0 || $intWarningNoticeId <= 0)
			{
				continue;
			}

			$strProductWarningNoticeKey = self :: PRODUCT_WARNING_NOTICE_X . $intWarningNoticeId;

			if (!isset ($this->arrConfigOptions [$strProductWarningNoticeKey]) ||
				strlen(trim($this->arrConfigOptions [$strProductWarningNoticeKey])) == 0)
			{
				continue;
			}

			$this->arrProductWarningNotices [$strProductWarningNoticeKey] = $this->arrConfigOptions [$strProductWarningNoticeKey];
		}

		return $this;
	}

//----------------------- private area -------------------------------------------------------------------------------------------


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück (Lagerware)
	 *
	 * @param $strProductAvailability , Id der Produktverfügbarkeit
	 *
	 * @return string Text für Produktverfügbarkeit (Lagerware)
	 */
	private function getProductAvailabilityTextStockItems($strProductAvailability)
	{

		$strProductAvailability = trim($strProductAvailability);

		if (strlen($strProductAvailability) == 0)
		{
			return "";
		}

		switch ($strProductAvailability)
		{
			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT] :

				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_TEXT]))
				{
					return "";
				}

				return $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_TEXT];

			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT] :

				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_TEXT]))
				{
					return "";
				}

				return $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_TEXT];

			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT] :

				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_TEXT]))
				{
					return "";
				}
				return $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_TEXT];
		}

		return "";
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt den Text für die Lieferzeit zurück (Lagerware)
	 *
	 * @param $strProductShipping , Id der Produktverfügbarkeit
	 *
	 * @return string Text für Lieferzeit (Lagerware)
	 */
	private function getProductShippingTextStockItems($strProductShipping)
	{

		$strProductShipping = trim($strProductShipping);
		if (strlen($strProductShipping) == 0)
		{
			return "";
		}

		switch ($strProductShipping)
		{
			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT] :

				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_SHIPPING_STOCK_ITEMS_GREEN_LIGHT_TEXT]))
				{
					return "";
				}

				return $this->arrConfigOptions [self :: PRODUCT_SHIPPING_STOCK_ITEMS_GREEN_LIGHT_TEXT];

			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT] :

				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_SHIPPING_STOCK_ITEMS_YELLOW_LIGHT_TEXT]))
				{
					return "";
				}

				return $this->arrConfigOptions [self :: PRODUCT_SHIPPING_STOCK_ITEMS_YELLOW_LIGHT_TEXT];

			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT] :

				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_SHIPPING_STOCK_ITEMS_RED_LIGHT_TEXT]))
				{
					return "";
				}
				return $this->arrConfigOptions [self :: PRODUCT_SHIPPING_STOCK_ITEMS_RED_LIGHT_TEXT];
		}

		return "";
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt das Bildnamen für die Produktverfügbarkeit zurück (Lagerware)
	 *
	 * @param $strProductAvailability , Id der Produktverfügbarkeit
	 *
	 * @return string Bildname für Produktverfügbarkeit (Lagerware)
	 */
	private function getProductAvailabilityPictureStockItems($strProductAvailability)
	{
		$strProductAvailablity = trim($strProductAvailability);

		if (strlen($strProductAvailability) == 0)
		{
			return "";
		}

		switch ($strProductAvailability)
		{
			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT] :

				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_PICTURE]))
				{
					return "";
				}

				return $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_PICTURE];

			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT] :

				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_PICTURE]))
				{
					return "";
				}


				return $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_PICTURE];

			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT] :

				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_PICTURE]))
				{
					return "";
				}

				return $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_PICTURE];
		}

		return "";
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt die Farbe für den Produktverfügbarkeitstext zurück
	 *
	 * @param $strProductAvailability , Id der Produktverfügbarkeit
	 * @param $fallback_color , Standartfarbe Schwarz zurückgeben falls keine Farbe vergeben wurde
	 *
	 * @return string Textfarbe für den Produktverfügbarkeitstext
	 */
	private function getProductAvailabilityTextColorStockItems($strProductAvailability, $fallback_color)
	{
		$strProductAvailablity = trim($strProductAvailability);

		if (strlen($strProductAvailability) == 0)
		{
			return $fallback_color;
		}

		$color = null;

		switch ($strProductAvailability)
		{
			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT] : // 1
				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_TEXT_COLOR]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_TEXT_COLOR]))
				{
					return "";
				}
				$color = $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_TEXT_COLOR];
				break;

			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT] : //2
				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_TEXT_COLOR]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_TEXT_COLOR]))
				{
					return "";
				}
				$color = $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_TEXT_COLOR];
				break;

			case  $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT] : //2
				if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_TEXT_COLOR]) ||
					!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_TEXT_COLOR]))
				{
					return "";
				}
				$color = $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_TEXT_COLOR];
				break;

			default:
				$color = $fallback_color;
		}

		if (is_null($color))
		{
			return $fallback_color;
		}
		return $color;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück (Möbel)
	 *
	 * @param $strProductAvailability , Id der Produktverfügbarkeit
	 *
	 * @return string Text für Produktverfügbarkeit (Möbel)
	 */
	private function getProductAvailabilityTextFurniture($strProductAvailability)
	{

		$strProductAvailability = trim($strProductAvailability);

		if (strlen($strProductAvailability) == 0)
		{
			return "";
		}

		if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_FURNITURE_INDENT]) ||
			!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_FURNITURE_TEXT]))
		{
			return "";
		}

		if (trim($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_FURNITURE_INDENT]) != $strProductAvailability)
		{
			return "";
		}

		return $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_FURNITURE_TEXT];
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück (Direktartikel)
	 *
	 * @param $strProductAvailability , Id der Produktverfügbarkeit
	 *
	 * @return string Text für Produktverfügbarkeit (Direktartikel)
	 */
	private function getProductAvailabilityTextDirectArticle($strProductAvailability)
	{

		$strProductAvailablity = trim($strProductAvailability);

		if (strlen($strProductAvailablity) == 0)
		{
			return "";
		}


		if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_DIRECT_ARTICLE_INDENT]) ||
			!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_DIRECT_ARTICLE_TEXT]))
		{
			return "";
		}

		if (trim($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_DIRECT_ARTICLE_INDENT]) != $strProductAvailability)
		{
			return "";
		}

		return $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_DIRECT_ARTICLE_TEXT];
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück (Außenspiel)
	 *
	 * @param $strProductAvailability , Id der Produktverfügbarkeit
	 *
	 * @return string Text für Produktverfügbarkeit (Außenspiel)
	 */
	private function getProductAvailabilityTextOutsidePlay($strProductAvailability)
	{

		$strProductAvailablity = trim($strProductAvailability);

		if (strlen($strProductAvailability) == 0)
		{
			return "";
		}

		if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_OUTSIDE_PLAY_INDENT]) ||
			!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_OUTSIDE_PLAY_TEXT]))
		{
			return "";
		}

		if (trim($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_OUTSIDE_PLAY_INDENT]) != $strProductAvailability)
		{
			return "";
		}

		return $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_OUTSIDE_PLAY_TEXT];
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück (Speditionsware)
	 *
	 * @param $strProductAvailability , Id der Produktverfügbarkeit
	 *
	 * @return string Text für Produktverfügbarkeit (Speditionsware)
	 */
	private function getProductAvailabilityTextFreightForwardingGoods($strProductAvailability)
	{

		$strProductAvailablity = trim($strProductAvailability);

		if (strlen($strProductAvailability) == 0)
		{
			return "";
		}

		if (!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_INDENT]) ||
			!isset ($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_TEXT]))
		{
			return "";
		}

		if (trim($this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_INDENT]) != $strProductAvailablity)
		{
			return "";
		}

		return $this->arrConfigOptions [self :: PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_TEXT];
	}
}
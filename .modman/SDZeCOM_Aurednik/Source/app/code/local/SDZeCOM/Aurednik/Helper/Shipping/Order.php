<?php


/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Helper_Shipping_Order extends SDZeCOM_Aurednik_Helper_Data
{

	/**
	 *
	 * @var $shippingStockItems
	 */
	private $shippingStockItems = array();

	/**
	 *
	 * @var $shippingDirectArticles
	 */
	private $shippingDirectArticles = array();

	/**
	 *
	 * @var $shippingFurnitureArticles
	 */
	private $shippingFurnitureArticles = array();

	/**
	 *
	 * @var $arrisOutsidePlayArticles
	 */
	private $shippingOutsidePlayArticles = array();

	/**
	 *
	 * @var $shippingFreightForwardingGoodArticles
	 */
	private $shippingFreightForwardingGoodArticles = array();

	/**
	 *
	 * @var $intStoreId
	 */
	private $intStoreId = 0;

	/**
	 *
	 * @var $dblMinOrderValueForFreeShippingStockItems
	 */
	private $dblMinOrderValueForFreeShippingStockItems = null;

	/**
	 *
	 * @var $dblShippingPriceStockItems
	 */
	private $dblShippingPriceStockItems = null;

	/**
	 *
	 * @var $arrAvailableOrderCountries
	 */
	private $arrAvailableOrderCountries = array();

	/**
	 *
	 * @var $arrAllOrderArticles
	 */
	private $arrAllOrderArticles = array();

	/**
	 *
	 * @var $arrShippingCountries
	 */
	private $arrShippingCountries = array();

	/**
	 *
	 * @var $objOrder
	 */
	private $objOrder = null;

	/**
	 *
	 * @var $intOrderWarningNoticesAgreementId
	 */
	private $intOrderWarningNoticesAgreementId = 0;

	/**
	 * @var string $shippingText , Versandkosten zusatztext
	 */
	protected $shippingText = '';

	/**
	 * @var int
	 */
	protected $websiteID = 0;

	/**
	 * @var bool
	 */
	protected $addStockCosts = false;

	/**
	 * @var bool
	 */
	protected $paidShipping = false;

//----------------------- public area ----------------------------------------------------------------------------------------------


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param Mage_Sales_Model_Order | Mage_Sales_Model_Quote $objOrder
	 *
	 * @param int|number $intStoreId
	 */
	public function __construct($objOrder, $intStoreId = 0)
	{
		if (is_null($objOrder) || $objOrder->getId() == 0)
		{
			return;
		}

		$this->objOrder = $objOrder;

		$this->setOrderShippingCountries();

		$this->categorizeOrderItems();

		$this->intStoreId = $intStoreId;
		$this->intStoreId = Mage::app()->getStore()->getWebsiteId();

		$this->dblMinOrderValueForFreeShippingStockItems = Mage:: getStoreConfig(self :: MIN_ORDER_VALUE_FREE_SHIPPING_STOCKITEM, $this->intStoreId);

		$this->dblShippingPriceStockItems = Mage:: getStoreConfig(self :: SHIPPING_COSTS_STOCKITEM, $this->intStoreId);

		if (Mage:: getStoreConfig(self :: ALLOW_ALL_ORDER_COUNTRIES, $this->intStoreId) == 1)
		{
			$strTmp = trim(Mage:: getStoreConfig(self :: ALLOW_SPECIFIC_ORDER_COUNTRIES, $this->intStoreId));

			if (strlen($strTmp) > 0)
			{
				$this->arrAvailableOrderCountries = explode(',', $strTmp);
			}
		}

		$intOrderWarningNoticesAgreementId = $this->intOrderWarningNoticesAgreementId = Mage:: getStoreConfig(self :: ORDER_PRODUCT_WARNINGS_NOTICES_AGREEMENT_ID, $this->intStoreId);

		if ($intOrderWarningNoticesAgreementId > 0)
		{
			$this->intOrderWarningNoticesAgreementId = $intOrderWarningNoticesAgreementId;
		}
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Id der Bestellbedinung,
	 * die für die Produkthinweise zuständig ist.
	 *
	 * @return number Id der Bestellbedinung
	 */
	public function getOrderProductWarningsAgreementId()
	{
		return $this->intOrderWarningNoticesAgreementId;
	}


	/**
	 * Liefert die gesamten Versandkosten der Bestellung
	 *
	 * @author Eugen Gutsche
	 *
	 * @return float, gesamte Versandkosten der Bestellung
	 */
	public function getTotalShippingCosts()
	{
		$totalShipping = 0.0;
		$shippingContainer = array(
			'stock' => $this->getShippingCostsStockItems(),
			'furniture' => $this->getShippingCostsFurniture(),
			'outsidePlay' => $this->getShippingCostsOutsidePlay(),
			'directArticles' => $this->getShippingCostsDirectArticles(),
			'forwardingGoods' => $this->getShippingCostsForwardingGoods()
		);

		foreach ($shippingContainer as $shippingType => $shippingCosts)
		{
			if($shippingCosts != $this->dblShippingPriceStockItems)
			{
				$totalShipping += $shippingCosts;
			}
			else
			{
				$totalShipping = $shippingCosts;
			}
		}

		return $totalShipping;
	}


	/**
	 * Liefert die gesamten Frachtkosten der Bestellung
	 *
	 * @author Eugen Gutsche
	 *
	 * @return float, gesamte Frachtkosten der Bestellung
	 */
	public function getTotalShippingFreightCosts()
	{
		$totalShipping = 0.0;

		$shippingContainer = array(
			'furniture' => $this->getShippingCostsFurniture(),
			'outsidePlay' => $this->getShippingCostsOutsidePlay(),
			'directArticles' => $this->getShippingCostsDirectArticles(),
			'forwardingGoods' => $this->getShippingCostsForwardingGoods()
		);

		foreach ($shippingContainer as $shippingType => $shippingCosts)
		{
			if($shippingCosts != $this->dblShippingPriceStockItems)
			{
				$totalShipping += $shippingCosts;
			}
		}

		return $totalShipping;
	}


	/**
	 * Liefert die Versandkosten für Lagerware
	 *
	 * @author Eugen Gutsche
	 *
	 * @return float, Versandkosten Lagerware
	 */
	public function getShippingCostsStockItems()
	{
		$this->addStockCosts = false;
		if (!is_array($this->shippingStockItems) || count($this->shippingStockItems) == 0)
		{
			return 0.0;
		}

		$totalCosts = $this->calculateShippingCosts('shippingStockItems');
		if (empty($totalCosts))
		{
			$totalCosts = $this->getDefaultShippingCosts($totalCosts);
		}

		return $totalCosts;
	}


	/**
	 * Liefert die Versandkosten für Direktartikel
	 *
	 * @author Eugen Gutsche
	 *
	 * @return float, Versandkosten Direktartikel
	 */
	public function  getShippingCostsDirectArticles()
	{
		$this->addStockCosts = false;
		if (!is_array($this->shippingDirectArticles) || count($this->shippingDirectArticles) == 0)
		{
			return 0.0;
		}
		
		$totalCosts = $this->calculateShippingCosts('shippingDirectArticles');
		$totalCosts = $this->getDefaultShippingCosts($totalCosts);

		return $totalCosts;
	}


	/**
	 * Liefert die Versandkosten für Möbel
	 *
	 * @author egutsche
	 *
	 * @return number Versandkosten Möbel
	 */
	public function  getShippingCostsFurniture()
	{
		$this->addStockCosts = false;
		if (!is_array($this->shippingFurnitureArticles) || count($this->shippingFurnitureArticles) == 0)
		{
			return 0.0;
		}

		$totalCosts = $this->calculateShippingCosts('shippingFurnitureArticles');
		if (empty($totalCosts))
		{
			$totalCosts = $this->getDefaultShippingCosts($totalCosts);
		}

		return $totalCosts;
	}


	/**
	 * Liefert die Versandkosten für Außenspiel
	 *
	 * @author egutsche

	 * @return float , Versandkosten Außenspiel
	 */
	public function  getShippingCostsOutsidePlay()
	{
		$this->addStockCosts = false;
		if (!is_array($this->shippingOutsidePlayArticles) || count($this->shippingOutsidePlayArticles) == 0)
		{
			return 0.0;
		}

		$totalCosts = $this->calculateShippingCosts('shippingOutsidePlayArticles');
		if (empty($totalCosts))
		{
			$totalCosts = $this->getDefaultShippingCosts($totalCosts);
		}

		return $totalCosts;
	}


	/**
	 * Liefert die Versandkosten für Speditionsware
	 *
	 * @author egutsche
	 *
	 * @return float , Versandkosten Speditionsware
	 */
	public function  getShippingCostsForwardingGoods()
	{
		$this->addStockCosts = false;
		if (!is_array($this->shippingFreightForwardingGoodArticles) || count($this->shippingFreightForwardingGoodArticles) == 0)
		{
			return 0.0;
		}

		$totalCosts = $this->calculateShippingCosts('shippingFreightForwardingGoodArticles');
		if (empty($totalCosts))
		{
			$totalCosts = $this->getDefaultShippingCosts($totalCosts);
		}

		return $totalCosts;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob die Frachttabelle für Möbel angezeigt werden soll
	 *
	 * @return boolean TRUE | FALSE
	 */
	public function showFreightTableFurniture()
	{

		if (count($this->shippingFurnitureArticles) == 0)
		{
			return false;
		}

		return true;

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob die Frachttabelle für Außenspiel angezeigt werden soll
	 *
	 * @return boolean TRUE | FALSE
	 */
	public function showFreightTableOutsidePlay()
	{

		if (count($this->shippingOutsidePlayArticles) == 0)
		{
			return false;
		}

		return true;
	}








	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt den Mindestbestellwert für freie Zustellung zurück (gilt nur für Lagerware )
	 *
	 * @return double $dblMinOrderValue Mindestbestellwert für freie Zustellung | FALSE, falls keiner existiert
	 */
	public function getMinOrderValueForFeeShippingStockItems()
	{
		return $this->dblMinOrderValueForFreeShippingStockItems;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * Gibt den Bestelltyp zurück
	 *
	 * @return string Bestelltyp
	 */
	public function getOrderType()
	{

		//Keine gültigen Artikel vorhanden
		if ($this->getTotalOrderItemsCount() == 0)
		{
			return self :: GETORDERTYPE_RETURN_VALUE_REQUEST;
		}

		//Versendet auch in ein erlaubtes Land
		if (!$this->isShippingInAllowCountry())
		{
			return self :: GETORDERTYPE_RETURN_VALUE_REQUEST;
		}

		if (count($this->shippingFurnitureArticles) > 0 ||
			count($this->shippingDirectArticles) > 0 ||
			count($this->shippingFreightForwardingGoodArticles) > 0
		)
		{

			$arrArticles = array_merge(
				$this->shippingDirectArticles,
				$this->shippingFreightForwardingGoodArticles
			);

			foreach ($arrArticles as $objShippingArticle)
			{

				if ($objShippingArticle->hasFreightCosts() && $objShippingArticle->getFixedFreightShippingCosts($this->intStoreId) <= 0)
				{
					return self :: GETORDERTYPE_RETURN_VALUE_REQUEST;
				}
			}
		}

		if (count($this->shippingDirectArticles) > 0 ||
			count($this->shippingFurnitureArticles) > 0 ||
			count($this->shippingFreightForwardingGoodArticles) > 0 ||
			count($this->shippingOutsidePlayArticles) > 0
		)
		{

			return self :: GETORDERTYPE_RETURN_VALUE_REQUEST_AND_ORDER;
		}

		return self :: GETORDERTYPE_RETURN_VALUE_ORDER;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * Liefert die Anzahl der Artikel
	 *
	 * @return number
	 */
	public function getTotalOrderItemsCount()
	{
		return
			count($this->shippingDirectArticles) +
			count($this->shippingFreightForwardingGoodArticles) +
			count($this->shippingFurnitureArticles) +
			count($this->shippingOutsidePlayArticles) +
			count($this->shippingStockItems);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * Liefert den Hinweistext, falls die Versandkosten nicht berechnet werden können
	 *
	 * @return string Hinweistext für die Versandkosten
	 */
	public function getShippingNoteText()
	{
		return Mage:: getStoreConfig(self :: SHIPPING_NOTE_TEXT, $this->intStoreId);
	}


	protected function setShippingText($shippingMethod)
	{
		if ($shippingMethod == 'free')
		{
			$this->shippingText = Mage::getStoreConfig('carriers/aurednikshipping/textfreeshipping', $this->intStoreId);
		}
		elseif ($shippingMethod == 'paid')
		{
			$this->shippingText = Mage::getStoreConfig('carriers/aurednikshipping/textnonfreeshipping', $this->intStoreId);
		}

		return '';
	}


	/**
	 * Gibt den Text für kostenlosen Versand zurück
	 *
	 * @author egutsche
	 *
	 * @return string
	 */
	public function getShippingText()
	{
		return $this->shippingText;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob die Frachttabelle für Möbel angezeigt werden darf
	 *
	 * @return boolean ob die Frachttabelle für Möbel angezeigt werden darf
	 */
	public function canDisplayFurnitureFreightTable()
	{
		return $this->hasOrderFurnitureArticles();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob die Frachttabelle für Außenspiel angezeigt werden darf
	 *
	 * @return boolean ob die Frachttabelle für Außenspiel angezeigt werden darf
	 */
	public function canDisplayOutSidePlayFreightTable()
	{
		return $this->hasOrderOutsidePlayArticles();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Gibt den Inhalt der Außenspiel Frachttabelle, als HTML zurück
	 *
	 * @return string Inhalt der Außenspiel Frachttabelle
	 */
	public function getOutSidePlayFreightTableHtml()
	{
		return $this->getCmsBlockContent(
			( int )Mage:: getStoreConfig(self :: FREIGHT_TABLE_OUTSIDE_PLAY_CMS_BLOCK_ID, $this->intStoreId));

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Gibt den Inhalt der Möbel Frachttabelle, als HTML zurück
	 *
	 * @return string Inhalt der Möbel Frachttabelle
	 */
	public function getFurnitureFreightTableHtml()
	{
		return $this->getCmsBlockContent(
			( int )Mage:: getStoreConfig(self :: FREIGHT_TABLE_FURNITURE_CMS_BLOCK_ID, $this->intStoreId));

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob eine Bestellung Produkt Warnhinweise enthält
	 *
	 * @return boolean TRUE | boolean FALSE
	 */
	public function hasOrderWarningNotices()
	{

		if (!is_array($this->arrAllOrderArticles) && count($this->arrAllOrderArticles) == 0)
		{
			return false;
		}

		foreach ($this->arrAllOrderArticles as $objCurrentArticleHlpr)
		{
			if ($objCurrentArticleHlpr->hasProductWarningNotices())
			{
				return true;
			}
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
	 * Prüft, ob eine Bestellung Lagerwareartikel enthält
	 *
	 * @return boolean TRUE | boolean FALSE
	 */
	public function hasOrderStockItems()
	{
		return (count($this->shippingStockItems) > 0) ? true : false;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob eine Bestellung Direktware enthält
	 *
	 * @return boolean TRUE | boolean FALSE
	 */
	public function hasOrderDirectArticles()
	{
		return (count($this->shippingDirectArticles) > 0) ? true : false;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob eine Bestellung Möbel enthält
	 *
	 * @return boolean TRUE | boolean FALSE
	 */
	public function hasOrderFurnitureArticles()
	{
		return (count($this->shippingFurnitureArticles) > 0) ? true : false;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob eine Bestellung Außenspiel Artikel enthält
	 *
	 * @return boolean TRUE | boolean FALSE
	 */
	public function hasOrderOutsidePlayArticles()
	{
		return (count($this->shippingOutsidePlayArticles) > 0) ? true : false;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob eine Bestellung Speditionsware enthält
	 *
	 * @return boolean TRUE | boolean FALSE
	 */
	public function hasOrderFreightForwardingGoodArticles()
	{
		return (count($this->shippingFreightForwardingGoodArticles) > 0) ? true : false;
	}




	public function getAdditionalOrderInformation()
	{
		$html = '';

		$additionalOrderAttributes = array(
			'aurednik_customer_number',
			'aurednik_shipping_type' ,
			'aurednik_shipping_de',
			'aurednik_shipping_at',
			'aurednik_shipping_information',
			'aurednik_info_outside_furniture',
			'aurednik_product_warnings'
		);

		foreach ($additionalOrderAttributes as $additionalOrderAttribute)
		{
			$html .= '<input';
			$html .= ' type="text"';
			$html .= ' id="' . $additionalOrderAttribute . '"';
			$html .= ' name="' . $additionalOrderAttribute . '"';
			$html .= ' value="value...'. $this->getSpecificOrderData($additionalOrderAttribute) . '"';
			$html .= '> ';
		}

		return $html;
	}


	/**
	 * @TODO Kommentar
	 *
	 * @ticket https://projects.sdzecom.de/issues/6268
	 *
	 * @author Eugen Gutsche
	 * @return mixed
	 */
	public function getFreightCostInfoAttribute()
	{
		switch ($this->intStoreId)
		{
			case 2: // DE Store
				$freightCostAddInfoAttr = Mage::getStoreConfig('carriers/aurednikshipping/freightcostadditionalinfoattributede', $this->intStoreId);
				break;
			case 3: // AT Store
				$freightCostAddInfoAttr = Mage::getStoreConfig('carriers/aurednikshipping/freightcostadditionalinfoattributeat', $this->intStoreId);
				break;
			default: // DE Store
				$freightCostAddInfoAttr = Mage::getStoreConfig('carriers/aurednikshipping/freightcostadditionalinfoattributede', $this->intStoreId);
		}

		return $freightCostAddInfoAttr;
	}


	/**
	 * Ausgabe der Frachtkostenanmerkungen nach dem Gesamtbetrag
	 *
	 * Falls Artikel in den Frachtkostenanmerkungen den Eintrag "Frachtfrei" oder einen leerern Wert aufweisen,
	 * wird ein Hinweistext aus der Aurednik-Shipping-Konfiguration ausgegeben.
	 *
	 * @author egutsche
	 *
	 * @param $freightInformation
	 * @return string
	 * @internal param $storeID
	 */
	public function getFreightCostHintText($freightInformation)
	{
		$freightCostHintText = trim(Mage::getStoreConfig('carriers/aurednikshipping/freightcostonepagecheckouthinttext', $this->intStoreId));
		$freightCostFreeShippingText = trim(Mage::getStoreConfig('carriers/aurednikshipping/freightcostfreetext', $this->intStoreId));
		$checkoutFreightHintText = '';

		if (!empty($freightInformation) && strcmp($freightInformation, $freightCostFreeShippingText) != 0)
		{
			$checkoutFreightHintText = $freightCostHintText;
		}

		return $checkoutFreightHintText;
	}


	/**
	 * Frachtkosteninformationen für Möbel und Speditionsware liefern
	 *
	 * @ticket https://projects.sdzecom.de/issues/6268
	 *
	 * @author Eugen Gutsche
	 * @param $product
	 * @return string
	 */
	public function getIndividualFreightCosts($product)
	{
		/** @var SDZeCOM_Aurednik_Model_Product $product */
		$shippingTypeValue = $product->getAttributeText('an_49'); //an_liefervariante
		switch ($shippingTypeValue)
		{
			case 'Möbel':
				return 'Bei diesem Artikel entstehen indiv. Frachtkosten';
			case 'Außenspiel':
				return 'Bei diesem Artikel entstehen indiv. Frachtkosten';
			default:
				return '';
		}
	}


	/**
	 * Gibt zusätzliche, Storespezifische Frachtkosteninformationen eines
	 * aus dem Attribut "an_anmerkung_frachtkosten_[store]" Zurück
	 *
	 * @ticket https://projects.sdzecom.de/issues/6268
	 *
	 * @author Eugen Gutsche
	 * @return mixed
	 */
	public function getStoreSpecificFreightCostAttribute()
	{
		switch ($this->intStoreId)
		{
			case 2: // DE Store
				return trim(Mage::getStoreConfig('carriers/aurednikshipping/freightcostadditionalinfoattributede'));
			case 3: // AT Store
				return trim(Mage::getStoreConfig('carriers/aurednikshipping/freightcostadditionalinfoattributeat'));
			default: // DE Store
				return trim(Mage::getStoreConfig('carriers/aurednikshipping/freightcostadditionalinfoattributede'));
		}
	}


	/**
	 * Gibt die Frachttabellen zurück
	 *
	 * @ticket https://projects.sdzecom.de/issues/6268
	 *
	 * @author Eugen Gutsche
	 * @return array
	 */
	public function getShippingTables()
	{
		$shippingTableHtml = array();
		if ($this->canDisplayFurnitureFreightTable())
		{
			$shippingTableHtml[] = $this->getFurnitureFreightTableHtml();
		}

		if ($this->canDisplayOutSidePlayFreightTable())
		{
			$shippingTableHtml[] = $this->getOutSidePlayFreightTableHtml();
		}

		return $shippingTableHtml;
	}

//----------------------- protected area -------------------------------------------------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Prüft, ob die Bestellung in ein erlaubtes Land, verschickt werden soll
	 *
	 * @return boolean true falls die Bestellung in ein erlaubtes Land verschickt wird , andernfalls boolean false
	 */
	protected function isShippingInAllowCountry()
	{

		if (Mage:: getStoreConfig(self :: ALLOW_ALL_ORDER_COUNTRIES, $this->intStoreId) == 0)
		{
			return true;
		}

		$arrIntersect = array_intersect($this->arrAvailableOrderCountries, $this->arrShippingCountries);

		if (count($arrIntersect) == 0)
		{
			return false;
		}

		return true;


	}


	/**
	 * Versand/Frachtkosten berechnen
	 *
	 * @author Eugen Gutsche
	 *
	 * @param $shippingType
	 * @return float
	 */
	protected function calculateShippingCosts($shippingType)
	{
		$totalCosts = 0.0;
		$this->addStockCosts = false;
		$orderItems = $this->objOrder->getAllItems();

		foreach ($orderItems as $orderItem)
		{
			$productID = $orderItem->getProduct()->getID();
			$qty = $orderItem->getTotalQty();

			if (isset($this->{$shippingType}[$productID]))
			{
				$helperProduct = $this->{$shippingType}[$productID];
				if ($helperProduct instanceof SDZeCOM_Aurednik_Helper_Product)
				{
					$productFreightCosts = $helperProduct->getFixedFreightShippingCosts($this->intStoreId);
					if (empty($productFreightCosts))
					{
						$this->addStockCosts = true;
					}
					$totalCosts += $productFreightCosts * $qty;
				}
			}
		}

		return $totalCosts;
	}


	/**
	 * Zusätzliche Logik für die Frachtkostenberechnung
	 * Alle Versandtypen müssen sich an die Freigrenze-Logik halten
	 * Aktuell: Bis Gesamtwarenkorb < 90.00€ = 3.95€ Versandkosten
	 *
	 * @ticket https://projects.sdzecom.de/issues/6289
	 *
	 * @author Eugen Gutsche
	 * @param $totalCosts
	 * @return float|mixed|null
	 * @internal param $subTotal
	 */
	protected function getDefaultShippingCosts($totalCosts)
	{
		$totals = Mage::getSingleton('checkout/cart')->getQuote()->getTotals();
		$subTotal = $totals["subtotal"]->getValue();
		$websiteID = $this->getWebsiteID();

		if ($subTotal >= $this->dblMinOrderValueForFreeShippingStockItems  && $websiteID != 2 && $totalCosts == 0)
		{
			$this->shippingText = $this->setShippingText('free');
			$this->paidShipping = false;

			return 0.0;
		}
		$this->shippingText = $this->setShippingText('paid');

		if ($websiteID != 2 && $this->addStockCosts)
		{
			$totalCosts += $this->dblShippingPriceStockItems;
			$this->paidShipping = true;
		}
		elseif ($subTotal < $this->dblMinOrderValueForFreeShippingStockItems && $totalCosts != 0 && $this->addStockCosts && $websiteID == 2)
		{
			$totalCosts += $this->dblShippingPriceStockItems;
			$this->paidShipping = true;
		}
		if (!empty($totalCosts))
		{
			return $totalCosts;
		}

		return $this->dblShippingPriceStockItems;
	}


	/**
	 * Website ID beziehen
	 *
	 * @author Eugen Gutsche
	 * @return int
	 */
	protected function getWebsiteID()
	{
		if (!empty($this->websiteID))
		{
			return $this->websiteID;
		}

		return $this->websiteID = (int)Mage::app()->getStore()->getWebsiteId();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * Setzt die Versandländer, der Besellung
	 *
	 * @return SDZeCOM_Aurednik_Helper_Shipping_Order
	 */
	private function setOrderShippingCountries()
	{

		$this->arrShippingCountries = array();

		$this->arrShippingCountries [] = $this->objOrder->getShippingAddress()->getCountry();

		return $this;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * Unterteilt die einzelenen Bestellungen in Kategorien ( Lagerware | Möbel usw. )
	 *
	 * @return SDZeCOM_Aurednik_Helper_Shipping_Order
	 */
	private function categorizeOrderItems()
	{

		$arrAllOrderItems = $this->objOrder->getAllItems();

		if (!is_array($arrAllOrderItems) || count($arrAllOrderItems) == 0)
		{
			return $this;
		}

		foreach ($arrAllOrderItems as $objOrderItem)
		{
			$productID = $objOrderItem->getProduct()->getId();
			$objShippingHlpr = new SDZeCOM_Aurednik_Helper_Product ($productID, $this->intStoreId);

			$this->arrAllOrderArticles [] = $objShippingHlpr;

			if ($objShippingHlpr->isStockItem())
			{
				$this->shippingStockItems[$productID] = $objShippingHlpr;
			}
			else
			{
				if ($objShippingHlpr->isDirectArticle())
				{
					$this->shippingDirectArticles[$productID] = $objShippingHlpr;
				}
				else
				{
					if ($objShippingHlpr->isFurniture())
					{
						$this->shippingFurnitureArticles[$productID] = $objShippingHlpr;
					}
					else
					{
						if ($objShippingHlpr->isOutsidePlay())
						{
							$this->shippingOutsidePlayArticles[$productID] = $objShippingHlpr;
						}
						else
						{
							if ($objShippingHlpr->isFreightForwardingGood())
							{
								$this->shippingFreightForwardingGoodArticles[$productID] = $objShippingHlpr;
							}
						}
					}
				}
			}
		}

		return $this;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * @param $intCmsBlockId , Id des Cms Blocks
	 *
	 * gibt den Inhalt eines Cms Blocks zurücks
	 *
	 * @return string Inhalt eines Cms Blocks zurücks
	 */
	private function getCmsBlockContent($intCmsBlockId)
	{

		if ($intCmsBlockId <= 0)
		{
			return "";
		}

		$objCmsBlock = Mage:: getModel('cms/block')->load($intCmsBlockId)->setStoreId($this->intStoreId);

		if (is_null($objCmsBlock) || $objCmsBlock->getId() <= 0)
		{
			return "";
		}

		return $objCmsBlock->getContent();
	}
}
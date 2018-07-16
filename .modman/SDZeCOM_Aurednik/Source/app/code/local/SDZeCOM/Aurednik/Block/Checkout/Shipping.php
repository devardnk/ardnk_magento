<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Block_Checkout_Shipping extends Mage_Tax_Block_Checkout_Shipping
{

	/**
	 *
	 * @var $objOrderHlpr
	 */
	private $objOrderHlpr = null;


	/**
	 * Konstruktor
	 */
	public function __construct()
	{

		parent:: __construct();

		$this->objOrderHlpr = new SDZeCOM_Aurednik_Helper_Shipping_Order ($this->getQuote());
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die gesamten Frachtkosten der Bestellung
	 *
	 * @return number gesamte Frachtkosten der Bestellung
	 */
	public function getTotalShippingFreightCosts()
	{
		return $this->objOrderHlpr->getTotalShippingFreightCosts();

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 *         Liefert die Versandkosten für Direktartikel
	 *
	 * @return number Versandkosten Direktartikel
	 */
	public function  getShippingCostsDirectArticles()
	{
		return $this->objOrderHlpr->getShippingCostsDirectArticles();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Versandkosten für Möbel
	 *
	 * @return number Versandkosten Möbel
	 */
	public function  getShippingCostsFurniture()
	{
		return $this->objOrderHlpr->getShippingCostsFurniture();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Versandkosten für Außenspiel
	 *
	 * @return number Versandkosten Außenspiel
	 */
	public function  getShippingCostsOutsidePlay()
	{
		return $this->objOrderHlpr->getShippingCostsOutsidePlay();
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
		return $this->objOrderHlpr->showFreightTableFurniture();

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
		return $this->objOrderHlpr->showFreightTableOutsidePlay();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Versandkosten für Außenspiel
	 *
	 * @return number Versandkosten Außenspiel
	 */
	public function  getShippingCostsForwardingGoods()
	{
		return $this->objOrderHlpr->getShippingCostsForwardingGoods();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die gesamten Versandkosten der Bestellung
	 *
	 * @return number gesamte Versandkosten der Bestellung
	 */
	public function getTotalShippingCosts()
	{
		return $this->objOrderHlpr->getTotalShippingCosts();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Versandkosten für Lagerware
	 *
	 * @return number Versandkosten Lagerware
	 */
	public function  getShippingCostsStockItems()
	{
		return $this->objOrderHlpr->getShippingCostsStockItems();
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
		return $this->objOrderHlpr->hasOrderStockItems();
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
		return $this->objOrderHlpr->hasOrderDirectArticles();
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
		return $this->objOrderHlpr->hasOrderFurnitureArticles();
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
		return $this->objOrderHlpr->hasOrderOutsidePlayArticles();
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
		return $this->objOrderHlpr->hasOrderFreightForwardingGoodArticles();
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
		return $this->objOrderHlpr->canDisplayFurnitureFreightTable();
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
		return $this->objOrderHlpr->canDisplayOutSidePlayFreightTable();
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
		return $this->objOrderHlpr->getOutSidePlayFreightTableHtml();
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
		return $this->objOrderHlpr->getFurnitureFreightTableHtml();
	}
}

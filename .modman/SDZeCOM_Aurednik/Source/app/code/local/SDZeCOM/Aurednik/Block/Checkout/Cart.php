<?php

class SDZeCOM_Aurednik_Block_Checkout_Cart extends Mage_Checkout_Block_Cart_Abstract
{

	/**
	 *
	 * @var $objOrderHelper
	 */
	protected $objOrderHelper = null;

//----------------------- public area ----------------------------------------------------------------------------------------------

	/**
	 * Konstruktor
	 */
	public function __construct()
	{
		parent:: __construct();
		$this->objOrderHelper = new SDZeCOM_Aurednik_Helper_Shipping_Order ($this->getQuote());

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
		return $this->objOrderHelper->canDisplayFurnitureFreightTable();
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
		return $this->objOrderHelper->canDisplayOutSidePlayFreightTable();
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
		return $this->objOrderHelper->getOutSidePlayFreightTableHtml();
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
		return $this->objOrderHelper->getFurnitureFreightTableHtml();
	}


}
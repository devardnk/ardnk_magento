<?php

class SDZeCOM_Aurednik_Block_Onepage_Shipping_Method extends Mage_Checkout_Block_Onepage_Shipping_Method
{

	private $shippingHlpr = null;


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 */
	protected function _construct()
	{
		$storeId = Mage::app()->getStore()->getId();
		$this->shippingHlpr = new SDZeCOM_Aurednik_Helper_Shipping_Order ($this->getQuote(), $storeId);
		parent::_construct();
	}


	/**
	 * Retrieve is allow and show block
	 *
	 * @return bool
	 */
	public function isShow()
	{
		return !$this->getQuote()->isVirtual();
	}


	/**
	 *
	 * @author egutsche
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
		if (is_null($this->shippingHlpr))
		{
			return false;
		}
		return $this->shippingHlpr->canDisplayFurnitureFreightTable();
	}


	/**
	 *
	 * @author egutsche
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
		if (is_null($this->shippingHlpr))
		{
			return false;
		}
		return $this->shippingHlpr->canDisplayOutSidePlayFreightTable();
	}


	/**
	 *
	 * @author egutsche
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
		if (is_null($this->shippingHlpr))
		{
			return '';
		}
		return $this->shippingHlpr->getOutSidePlayFreightTableHtml();
	}


	/**
	 *
	 * @author egutsche
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
		if (is_null($this->shippingHlpr))
		{
			return '';
		}
		return $this->shippingHlpr->getFurnitureFreightTableHtml();
	}
}

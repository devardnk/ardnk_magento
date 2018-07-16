<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{

	/**
	 *
	 * @var $objAurednikProductHlpr
	 */
	private $objAurednikProductHlpr = null;


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param Mage_Sales_Model_Quote_Item_Abstract $item
	 *
	 * @return SDZeCOM_Aurednik_Block_Checkout_Cart_Item_Renderer
	 */
	public function setItem(Mage_Sales_Model_Quote_Item_Abstract $item)
	{
		$this->_item = $item;

		$this->objAurednikProductHlpr = new SDZeCOM_Aurednik_Helper_Product ($item->getProduct()->getId());

		return $this;
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
		return $this->objAurednikProductHlpr->isFurniture();
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
		return $this->objAurednikProductHlpr->isDirectArticle();
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
		return $this->objAurednikProductHlpr->isOutsidePlay();
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
		return $this->objAurednikProductHlpr->isStockItem();
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
		return $this->objAurednikProductHlpr->isFreightForwardingGood();
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
		return $this->objAurednikProductHlpr->getProductAvailabilityText();
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
		return $this->objAurednikProductHlpr->getProductAvailabilityPicture();
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
	public function getProductAvailabilityPictureUrl()
	{
		return $this->objAurednikProductHlpr->getProductAvailabilityPictureUrl();
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
		return $this->objAurednikProductHlpr->hasProductWarningNotices();
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
		return $this->objAurednikProductHlpr->getProductWarningNotices();
	}


	/**
	 * Gibt die Farbe für den Produktverfügbarkeitstext zurück
	 *
	 * @author egutsche
	 *
	 * @return string Text-color für Produktverfügbarkeit
	 */
	public function getProductAvailabilityTextColor()
	{
		return $this->objAurednikProductHlpr->getProductAvailabilityTextColor();
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
		if (is_null($this->objAurednikProductHlpr))
		{
			return '';
		}
		return $this->objAurednikProductHlpr->getProductShippingText();
	}


	/**
	 * Gibt die Zusatzinformationen für Frachtkosten zurück
	 *
	 * @author egutsche
	 *
	 * @param $freightInfoAttribute , Attribut für Frachtkostenanmerkung (DE, AT)
	 * @return string
	 */
	public function getAdditionalFreightCostInformation($freightInfoAttribute)
	{
		if (is_null($this->objAurednikProductHlpr))
		{
			return '';
		}

		return $this->objAurednikProductHlpr->getAdditionalFreightCostInformation($freightInfoAttribute);
	}
}

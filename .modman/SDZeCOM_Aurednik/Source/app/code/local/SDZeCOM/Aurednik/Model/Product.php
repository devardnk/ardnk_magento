<?php

class SDZeCOM_Aurednik_Model_Product extends Mage_Catalog_Model_Product
{
	/**
	 * @var SDZeCOM_Aurednik_Helper_Product $productHlpr
	 */
	private $productHlpr = null;


	/**
	 * Load product options if they exists
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	protected function _afterLoad()
	{

		parent::_afterLoad();

		if ($this->getId() > 0)
		{
			$this->productHlpr = new SDZeCOM_Aurednik_Helper_Product($this);
		}
		return $this;
	}


	/**
	 * Prüft ob das übergebene Produkt gesperrt ist
	 *
	 * @author egutsche
	 *
	 * @return boolean True falls das Produkt gesperrt ist, ansonsten False
	 */
	public function isProductLocked()
	{
		if (is_null($this->productHlpr))
		{
			return '';
		}
		return $this->productHlpr->getProductLockStatus();
	}


	/**
	 * @author egutsche
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
		if (is_null($this->productHlpr))
		{
			return '';
		}
		return $this->productHlpr->getProductAvailabilityText();
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
		if (is_null($this->productHlpr))
		{
			return '';
		}
		return $this->productHlpr->getProductShippingText();
	}


	/**
	 * Gibt die Frachtkosten für DE zurück
	 *
	 * @author egutsche
	 */
	public function getFreightShippingCostsDe()
	{
		if (is_null($this->productHlpr))
		{
			return '';
		}

		return $this->productHlpr->getFreightShippingCostsDe();
	}


	/**
	 * Gibt die Frachtkosten für AT zurück
	 *
	 * @author egutsche
	 */
	public function getFreightShippingCostsAt()
	{
		if (is_null($this->productHlpr))
		{
			return '';
		}

		return $this->productHlpr->getFreightShippingCostsAt();
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
		if (is_null($this->productHlpr))
		{
			return '';
		}

		return $this->productHlpr->getAdditionalFreightCostInformation($freightInfoAttribute);
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt Lagerware ist
	 *
	 * @return boolean TRUE falls das Produkt eine Lagerware ist | FALSE
	 */
	public function isStockItem()
	{
		if (is_null($this->productHlpr))
		{
			return '';
		}

		return $this->productHlpr->isStockItem();
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft den Typ des übergebenen Produkts: Lagerware, Möbel, Speditionsware, ...
	 *
	 * @return boolean TRUE falls das Produkt eine Speditionsware ist | FALSE
	 */
	public function getProductType()
	{
		if (is_null($this->productHlpr))
		{
			return '';
		}
		return $this->productHlpr->getProductType();
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt die Farbe für den Text der Produktverfügbarkeit zurück
	 *
	 * @return string Text für Produktverfügbarkeit
	 */
	public function getProductAvailabilityTextColor($fallback_color = '#000000')
	{
		if (is_null($this->productHlpr))
		{
			return '';
		}
		return $this->productHlpr->getProductAvailabilityTextColor($fallback_color);
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt die Url des Bildes für die Produktverfügbarkeit zurück
	 *
	 * @return string Url für Produktverfügbarkeit
	 */
	public function getProductAvailabilityPictureUrl()
	{
		if (is_null($this->productHlpr))
		{
			return '';
		}
		return $this->productHlpr->getProductAvailabilityPictureUrl();
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
	 * @return String /Pfad zum PDF Icon
	 */
	public function getProductPDFPictureUrl($url = true)
	{
		if (is_null($this->productHlpr))
		{
			return '';
		}
		return $this->productHlpr->getProductPDFPictureUrl($url);
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param $url
	 *
	 * gibt die Url der Katalog- PDF  zurück
	 *
	 * @return String /Pfad Katalogseite
	 */
	public function getProductPDFilePath($url = false)
	{
		if (is_null($this->productHlpr))
		{
			return '';
		}
		return $this->productHlpr->getProductPDFilePath($url);
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
	 * @return string Dateiname der Katalogseite
	 */
	public function getProductPDFileName()
	{
		if (is_null($this->productHlpr))
		{
			return '';
		}
		return $this->productHlpr->getProductPDFileName();
	}


	/**
	 * @author egutsche
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
		if (is_null($this->productHlpr))
		{
			return '';
		}
		return $this->productHlpr->getProductWarningNotices();
	}


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Gibt das Gewicht formatiert und auf 2 Nachkommastellen zurück.
	 * Als Rundungsmethode wird die PHP Methode 'PHP_ROUND_HALF_UP' verwendet
	 *
	 * Alternativ können die Einheiten ausgegeben werden
	 *
	 * @param boolean $withUnits = false, sollen die Einheitn ausgegeben werden
	 *
	 * @return string Dateiname der Katalogseite
	 */
	public function getFormatedWeight($withUnits = false)
	{

		$weight = Mage::app()->getLocale()->getNumber(parent::getWeight());

		if ($weight < 1)
		{
			$weight = round($weight * 1000, 0);

			if ($withUnits)
			{
				$weight .= ' ' . Mage::helper('aurednik')->__('gramm');
			}
		}
		else
		{
			$weight = round($weight, 2);

			if ($withUnits)
			{
				$weight .= ' ' . Mage::helper('aurednik')->__('kg');
			}
		}

		return $weight;
	}


	/**
	 * Gibt eine definierte Menge an Zusatzdaten/Attribute eines Produkts
	 * für den Bestellvorgang zurück
	 *
	 * @author egutsche
	 *
	 * @return array
	 */
	public function getAdditionalProductOrderData()
	{
		if (is_null($this->productHlpr))
		{
			return [];
		}

		return $this->productHlpr->getAdditionalProductOrderData();
	}
}

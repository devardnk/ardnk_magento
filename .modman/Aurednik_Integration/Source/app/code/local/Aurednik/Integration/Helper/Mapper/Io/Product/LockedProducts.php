<?php

/**
 * Stellt Methoden bereit, um gesperrte Artikel aus der CSV-Datei
 * auszulesen und in das Magento Import-Format zu mappen
 *
 * @author egutsche
 *
 */
class Aurednik_Integration_Helper_Mapper_Io_Product_LockedProducts extends Aurednik_Integration_Helper_Mapper_Io_Product
{
	/**
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
	 * @return $product SDZeCOM_Aurednik_Model_Product
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

		if (!isset($this->importData['Nr.']))
		{
			return '';
		}

		return trim($this->importData ['Nr.']);
	}
}
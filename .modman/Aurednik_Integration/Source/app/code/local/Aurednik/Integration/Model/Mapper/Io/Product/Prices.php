<?php

/**
 * Klasse, die Mehtoden bereitstellt,
 * um eine CSV-Datei einzulesen und die eingelesenen Daten in das Magento Importformat zu mappen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Model_Mapper_Io_Product_Prices extends Aurednik_Integration_Model_Mapper_Io_Product
{

	/**
	 * @var $csvDelimiter Csv-Trennzeichen
	 */
	protected $csvDelimiter = '|';


	/**
	 * Setzt die Hilfsklasse
	 *
	 * @author akniss
	 *
	 */
	public function __construct()
	{
		$this->helper = new Aurednik_Integration_Helper_Mapper_Io_Product_Prices();
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Mapper_Io_Csv_Abstract::mapFromCsvLine()
	 *
	 * @param array $lineData
	 */
	public function mapFromCsvLine(array $lineData)
	{
		$this->setHelper($lineData);

		if (isset ($this->data [$this->getHelper()->getType()] [$this->getHelper()->getSku()]) == '')
		{
			$defaultProductData = $this->getDefaultProductData();
			$this->addData($defaultProductData);
			$globalProductData = $this->getProductData();
			$this->addData($globalProductData);
		}
		else
		{
			$globalProductData = $this->getProductData();
			$this->addData($globalProductData);
		}

		$tierPrice = $this->getHelper()->getTierPrices();

		//Staffelpreise
		if (!empty($tierPrice))
		{
			$this->addData($tierPrice);
		}
	}


	/**
	 * Gibt die gemappten Produktdaten zurück
	 *
	 * @author akniss
	 *
	 * @return array Produktdaten
	 */
	protected function getProductData()
	{
		return $this->getRequiredProductData();
	}


	/**
	 * Gibt die für den Import erforderlichen Produktdaten zurück
	 *
	 * @author egutsche
	 *
	 * @return array Produktdaten
	 */
	protected function getDefaultProductData()
	{
		// Default Product Data
		$defaultProductData = array
		(
			'sku' => $this->helper->getSku(),
			'_type' => $this->helper->getType(),
			'_attribute_set' => $this->helper->getAttributeSet(),
			'_product_websites' => $this->helper->getWebsite(),
			'_store' => $this->helper->getStore(),
			'price' => $this->helper->getSinglePrice(),
			'tax_class_id' => $this->helper->getTaxClassId(),
			'status' => $this->helper->getStatus()
		);

		return $defaultProductData;
	}


	/**
	 * Gibt die für den Import erforderlichen Produktdaten zurück
	 *
	 * @author egutsche
	 *
	 * @return array Produktdaten
	 */
	protected function getRequiredProductData()
	{
		// Global Product Data
		$productData = array
		(
			'sku' => false,
			'_type' => $this->helper->getType(),
			'_attribute_set' => $this->helper->getAttributeSet(),
			'_product_websites' => $this->helper->getWebsite(),
			'_store' => $this->helper->getStore(),
			'price' => $this->helper->getSinglePrice(),
			'tax_class_id' => $this->helper->getTaxClassId(),
			'status' => $this->helper->getStatus()
		);

		return $productData;
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Mapper_Abstract::getData()
	 */
	public function getData($key = null)
	{

		$data = array();

		if (isset ($this->data [Mage_Catalog_Model_Product_Type::TYPE_SIMPLE]) && count($this->data [Mage_Catalog_Model_Product_Type::TYPE_SIMPLE]) > 0)
		{

			foreach ($this->data [Mage_Catalog_Model_Product_Type::TYPE_SIMPLE] as $sku => $product)
			{
				$data = array_merge($data, $product);
			}
		}

		if (isset ($this->data [Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE]) && count($this->data [Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE]) > 0)
		{

			foreach ($this->data [Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE] as $sku => $product)
			{
				$data = array_merge($data, $product);
			}
		}

		if (empty($data))
		{

			$date = date("Y-m-d_H-i-s");

			foreach ($this->data as $key => $value)
			{
				foreach ($value as $k => $data)
				{
					Mage::log('Produkt mit der Sku: ' . $data[0]['sku'] . ' existiert nicht im Shop!', 3, '/ERRORS/price_import_' . $date . '.log');
				}
			}

			return $date; //$this->helper->getSku();
		}

		return $data;
	}
}
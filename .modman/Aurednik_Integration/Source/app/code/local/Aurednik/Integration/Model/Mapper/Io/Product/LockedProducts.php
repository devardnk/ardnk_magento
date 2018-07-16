<?php

/**
 * Klasse, die Mehtoden bereitstellt,
 * um eine CSV-Datei einzulesen und die eingelesenen Daten in das Magento Importformat zu mappen
 *
 * @author egutsche
 *
 */
class Aurednik_Integration_Model_Mapper_Io_Product_LockedProducts extends Aurednik_Integration_Model_Mapper_Io_Product
{

	/**
	 * @var $csvDelimiter Csv-Trennzeichen
	 */
	protected $csvDelimiter = '|';


	/**
	 * Setzt die Hilfsklasse
	 *
	 * @author egutsche
	 */
	public function __construct()
	{
		$this->helper = new Aurednik_Integration_Helper_Mapper_Io_Product_LockedProducts();
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Mapper_Io_Csv_Abstract::mapFromCsvLine()
	 */
	public function mapFromCsvLine(array $lineData)
	{
		$this->setHelper($lineData);

		$defaultProductData = $this->getDefaultProductData();
		$atProductData = $this->getAtStoreSpecificProductData();
		$deProductData = $this->getDeStoreSpecificProductData();

		$this->addData($defaultProductData);
		$this->addData($atProductData);
		$this->addData($deProductData);
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
		$defaultProductData = array
		(
			'sku' => $this->helper->getSku(),
			'_type' => $this->helper->getType(),
			'_product_websites' => 'base',
			'_store' => 'de',
			'status' => 2
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
	protected function getAtStoreSpecificProductData()
	{
		$atProductData = array
		(
			'sku' => false,
			'_type' => $this->helper->getType(),
			'_product_websites' => 'oesterreich_shop',
			'_store' => 'oesterreich_shopview',
			'status' => 2
		);

		return $atProductData;
	}


	/**
	 * Gibt die für den Import erforderlichen Produktdaten zurück
	 *
	 * @author egutsche
	 *
	 * @return array Produktdaten
	 */
	protected function getDeStoreSpecificProductData()
	{
		$deProductData = array
		(
			'sku' => false,
			'_type' => $this->helper->getType(),
			'_product_websites' => 'base',
			'_store' => 'de',
			'status' => 2
		);

		return $deProductData;
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
					Mage::log('Produkt mit der Sku: ' . $data[0]['sku'] . ' existiert nicht im Shop!', 3,Mage::getBaseDir('log'). '/ERRORS/locked_products_import_' . $date . '.log');
				}
			}

			return $date;
		}

		return $data;
	}
}
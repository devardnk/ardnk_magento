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
class Aurednik_Integration_Model_Mapper_Io_Product_Attributes extends Aurednik_Integration_Model_Mapper_Io_Product
{

	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Setzt das Bilderattribut $imageAttributeId und die Hilfsklasse
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 */
	public function __construct()
	{
		$this->helper = new Aurednik_Integration_Helper_Mapper_Io_Product_Attributes ();
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Mapper_Abstract::addData()
	 */
	public function addData($data, $key = null)
	{
		if (isset($this->data [$this->getHelper()->getCode()] [$key]) && is_array($data))
		{
			array_merge($this->data [$this->getHelper()->getCode()] [$key], $data);
		}
		else
		{
			$this->data [$this->getHelper()->getCode()] [$key] = $data;
		}
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Mapper_Abstract::getData()
	 */
	public function getData($key = null)
	{
		return $this->data;
	}


	/**
	 * (non-PHPdoc)
	 * @see Aurednik_Integration_Model_Mapper_Io_Article::mapFromCsvLine()
	 */
	public function mapFromCsvLine(array $lineData)
	{

		if (!is_array($lineData) || count($lineData) == 0)
		{
			return;
		}

		$this->setHelper($lineData);

		$this->addOptions();
		$this->setIsFilterable();

	}


	//------------------------------------protected-area--------------------------------------------------

	/**
	 * Setzt die Attribut-Optionen
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 */
	protected function addOptions()
	{

		$options = $this->getHelper()->getOptions();

		if (is_array($options) && count($options) > 0)
		{
			$this->addData($options, SDZeCOM_Library_Model_Catalog_Product_Attribute_Import::ADD_OPTIONS);
		}
	}


	/**
	 * Setzt den Attributfilter
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 */
	protected function setIsFilterable()
	{

		$filter = $this->getHelper()->getIsFilterable();

		if ($filter !== false)
		{
			$this->addData($filter, SDZeCOM_Library_Model_Catalog_Product_Attribute_Import::IS_FILTERABLE);
		}
	}


	//------------------------------------private-area----------------------------------------------------
}
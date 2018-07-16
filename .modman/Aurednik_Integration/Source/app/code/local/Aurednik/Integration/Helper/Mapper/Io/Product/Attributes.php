<?php

/**
 * Stellt Methoden bereit, um Produktattribute aus der CSV-Datei
 * auszulesen und in das Magento Import-Format zu mappen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Helper_Mapper_Io_Product_Attributes extends Aurednik_Integration_Helper_Mapper_Io_Product
{


	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Konstruktor
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 */
	public function __construct()
	{

	}


	/**
	 * Gibt den Attribut-Code zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function getCode()
	{

		if (!isset ($this->importData ['attribute_code']) || strlen(trim($this->importData ['attribute_code'])) == 0)
		{
			return '';
		}

		return trim($this->importData ['attribute_code']);
	}


	/**
	 * Gibt Attribut-Filterung zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function getIsFilterable()
	{

		if (!isset ($this->importData ['is_filterable']) || strlen(trim($this->importData ['is_filterable'])) == 0)
		{
			return false;
		}

		return ( int )trim($this->importData ['is_filterable']);

	}


	/**
	 * Gibt Attribut-Optionen zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function getOptions()
	{

		if (!isset ($this->importData ['attribute_options']) || strlen(trim($this->importData ['attribute_options'])) == 0)
		{
			return array();
		}

		$options = str_getcsv(
			$this->importData['attribute_options'],
			',',
			$enclosure = "'",
			$escape = "\\"
		);


		if (!is_array($options) || count($options) == 0)
		{
			return array();
		}

		$result = array();

		foreach ($options as $option)
		{

			//str_getcsv php Bug escape Zeichen werden nicht entfernt
			$option = str_replace('\\', '', $option);

			$option = str_getcsv(
				$option,
				';',
				$enclosure = "'"
			);

			if (!is_array($option) || count($option) == 0)
			{
				continue;
			}

			$option = $this->getOption($option);

			if (!is_array($option) || count($option) == 0)
			{
				continue;
			}

			$result[] = array('label' => $option, 'order' => 0, 'is_default' => 0);
		}

		return $result;
	}


	/**
	 * Gibt Attribut-Gültigkeit zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function getScope()
	{

		if (!isset ($this->importData ['scope']) || strlen(trim($this->importData ['scope'])) == 0)
		{
			return '';
		}

		return $this->importData ['scope'];
	}



	//------------------------------------protected-area--------------------------------------------------


	//------------------------------------private-area----------------------------------------------------

	/**
	 * Gibt Attribut-Option mit Übersetzungen zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function getOption($options)
	{

		if (!is_array($options) || count($options) == 0)
		{
			return array();
		}

		$result = array();

		foreach ($options as $option)
		{

			$option = str_getcsv(
				$option,
				':',
				$enclosure = '"',
				$escape = ''
			);


			if (!is_array($option) || !isset ($option[0]) || !isset ($option[1]))
			{
				continue;
			}

			$tmp = array('store_id' => ( int )$option[0], 'value' => $option[1]);

			if (isset ($option[3]))
			{
				$tmp['order'] = $option[2];
			}

			if (isset ($option[4]))
			{
				$tmp['is_default'] = $option[3];
			}

			$result[] = $tmp;
		}

		return $result;
	}

}
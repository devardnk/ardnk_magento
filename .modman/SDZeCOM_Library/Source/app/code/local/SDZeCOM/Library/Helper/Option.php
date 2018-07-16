<?php

/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 * @copyright SDZeCOM GmbH
 *
 */
class SDZeCOM_Library_Helper_Option extends SDZeCOM_Library_Helper_Data
{


	/**
	 *
	 * @version 1.1
	 * @author akniss
	 *
	 * Liefert eine Option Array, dass für Magento Select-Felder verwendet werden kann
	 *
	 * @param $objCollection
	 *
	 * @param string $strOptionLabel
	 *
	 * @param string $strOptionValue
	 *
	 * @throws InvalidArgumentException
	 *
	 * @return array $arrOptions mit den Optionen für das Select Feld
	 */
	public function toOptionArray($objCollection, $strOptionLabel, $strOptionValue)
	{

		if (!is_object($objCollection))
		{
			throw new InvalidArgumentException("Collection is not an object");
		}

		if (!is_string($strOptionLabel) || strlen(trim($strOptionLabel)) == 0)
		{
			throw new InvalidArgumentException("strOptionKey must be string and length must be > 0");
		}
		else
		{
			$strOptionLabel = trim($strOptionLabel);
		}

		if (!is_string($strOptionValue) || strlen(trim($strOptionValue)) == 0)
		{
			throw new InvalidArgumentException("strOptionValue must be string and length must be > 0");
		}
		else
		{
			$strOptionValue = trim($strOptionValue);
		}

		$arrOptions = array();

		foreach ($objCollection->getData() as $arrCurrentCollectionElement)
		{

			if (!isset($arrCurrentCollectionElement[$strOptionLabel]))
			{
				throw new InvalidArgumentException("strOptionLabel not exists in collection");
			}

			if (!isset($arrCurrentCollectionElement[$strOptionValue]))
			{
				throw new InvalidArgumentException("strOptionValue not exists in collection");
			}

			$arrOptions[] = array(
				'label' => $arrCurrentCollectionElement[$strOptionLabel],
				'value' => $arrCurrentCollectionElement [$strOptionValue],
			);
		}
		// Liste nach Namen sortieren
		uasort($arrOptions, function ($a, $b)
		{
			return strnatcasecmp($a['label'], $b['label']);
		});
		return $arrOptions;
	}
}
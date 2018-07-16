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
class Aurednik_Integration_Model_Mapper_Io_Product_Images extends Aurednik_Integration_Model_Mapper_Io_Product
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
		$this->helper = new Aurednik_Integration_Helper_Mapper_Io_Product_Images ();
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Mapper_Abstract::addData()
	 */
	public function addData($data, $key = null)
	{

		$this->data [$this->getHelper()->getSku()] [$key] [] = $data;

	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Mapper_Abstract::getData()
	 */
	public function getData($key = null)
	{

		if (!is_array($this->data) || count($this->data) == 0)
		{

			return array();
		}

		$data = array();

		foreach ($this->data as $sku => $imageData)
		{

			if (!is_array($imageData) || count($imageData) == 0)
			{
				continue;
			}

			foreach ($imageData as $currImage => $currImageData)
			{
				$data = array_merge($data, $currImageData);
			}
		}

		return $data;
	}


	/**
	 * (non-PHPdoc)
	 * @see Aurednik_Integration_Model_Mapper_Io_Article::mapFromCsvLine()
	 */
	public function mapFromCsvLine(array $lineData)
	{

		$this->setHelper($lineData);

		$this->addImageData($this->getHelper()->getAllImages());
		$this->addImageData($this->getHelper()->getMediaGalleryImages());
	}

	//------------------------------------protected-area--------------------------------------------------

	/**
	 * Gibt die erforderlichen Produktbilder Importdaten zurück,
	 * die bei jedem Bilderimport gesetzt werden müssen
	 * beispielsweise die sku, _product_websites, _store usw.
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return array benötigte Daten für den Bildimport
	 *
	 */
	protected function addImageData($images = null)
	{

		if (!is_array($images) || count($images) == 0)
		{
			return array();
		}

		$globalImageData = array(
			'_product_websites' => $this->getHelper()->getWebsite(),
			'type' => $this->getHelper()->getType(),
			'_store' => $this->getHelper()->getStore(),

		);

		foreach ($images as $currImage)
		{
			$currImageData = $globalImageData;

			$currImageData ['sku'] = $this->isOrphanProduct() ? false : $this->getHelper()->getSku();

			if (!$this->isOrphanProduct($currImage))
			{

				$currImageData ['_media_attribute_id'] = $this->getHelper()->getImageAttributeId();
				$currImageData ['_media_is_disabled'] = 0;
				$currImageData ['_media_position'] = 0;

				if ($this->getHelper()->isThumbnailImage($currImage))
				{
					$currImageData ['thumbnail'] = $currImage;
				}

				if ($this->getHelper()->isSmallImage($currImage))
				{
					$currImageData ['small_image'] = $currImage;
				}


				if ($this->getHelper()->isBaseImage($currImage))
				{
					$currImageData ['image'] = $currImage;
				}

				$currImageData ['_media_image'] = $currImage;
				$currImageData ['_media_lable'] = pathinfo($currImage, PATHINFO_FILENAME);
			}

			$this->addData($currImageData, $currImage);
		}
	}


	/**
	 * (non-PHPdoc)
	 * @see Aurednik_Integration_Model_Mapper_Io_Article::isOrphanProduct()
	 */
	protected function isOrphanProduct($image = null)
	{

		if (!is_null($image))
		{
			return isset ($this->data [$this->getHelper()->getSku()] [$image]) ? true : false;
		}

		return isset ($this->data [$this->getHelper()->getSku()]) ? true : false;
	}

	//------------------------------------private-area----------------------------------------------------
}
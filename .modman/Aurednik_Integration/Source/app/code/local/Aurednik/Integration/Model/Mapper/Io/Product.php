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
class Aurednik_Integration_Model_Mapper_Io_Product extends SDZeCOM_Integration_Model_Mapper_Io_Csv_Abstract
{

	/**
	 * @var $csvDelimiter Csv-Trennzeichen
	 */
	protected $csvDelimiter = ';';

	/**
	 *
	 * @var Aurednik_Integration_Helper_Mapper_Io_Product $helper Hilfsklasse
	 */
	protected $helper = null;

	/**
	 *
	 * @var $imageData
	 */
	protected $imageData = array();

	/**
	 *
	 * @var $productsDelete
	 */
	protected $productsDelete = array();


	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Setzt die Hilfsklasse
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 */
	public function __construct()
	{
		$this->helper = new Aurednik_Integration_Helper_Mapper_Io_Product ();
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Mapper_Abstract::addData()
	 */
	public function addData($data, $key = null)
	{
		$this->data [$this->getHelper()->getType()] [$this->getHelper()->getSku()] [] = $data;
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
		return $data;
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Mapper_Io_Csv_Abstract::mapFromCsvLine()
	 */
	public function mapFromCsvLine(array $lineData)
	{

		$this->setHelper($lineData);

		if ($this->canProductDelete())
		{
			$this->addProductToDelete();
		}
		else
		{

			$globalProductData = $this->getProductData();
			$configurableProductVariants = $this->getConfigurableProductVariants();
			$configurableProductSuperAttributes = $this->getHelper()->getConfigurableAttributes();
			//$tierPrices 						= $this->getHelper ()->getTierPrices ();
			$categoryIds = $this->getHelper()->getCategoryIds();

			$this->addData($globalProductData);

			$attributeSet = $this->getHelper()->getAttributeSet();

			if (is_array($configurableProductVariants) && count($configurableProductVariants) > 0)
			{

				foreach ($configurableProductVariants as $currVariant)
				{
					$this->addData($currVariant);
				}
			}

			//Konfigurierbare Produkte
			if (is_array($configurableProductSuperAttributes) && count($configurableProductSuperAttributes) > 0)
			{
				foreach ($configurableProductSuperAttributes as $currSuperAttribute)
				{
					$this->addData(array('sku' => false, '_attribute_set' => $attributeSet, '_super_attribute_code' => $currSuperAttribute));
				}
			}

			//Staffelpreise
			/*if ( is_array ( $tierPrices ) && count ( $tierPrices ) > 0 ) {

				 foreach ( $tierPrices as $currTierPrice ) {
					 $this->addData ( $currTierPrice );
				 }
			 }*/

			$position = $this->getHelper()->getPosition();

			//Kategorie Ids
			if (is_array($categoryIds) && count($categoryIds) > 0)
			{

				foreach ($categoryIds as $currCategoryId)
				{
					$this->addData(array('_category' => $currCategoryId, 'position' => $position, 'sku' => false, '_attribute_set' => $attributeSet));
				}
			}

			//Produkt BildInformationen
			$this->addImageData();
		}
	}


	/**
	 * Gibt Produkt Bildinformationen zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 *
	 * @return array Produkt Bildinformationen
	 */
	public function getImageData()
	{
		return $this->imageData;
	}


	/**
	 * Gibt die CSV-Spalten für den Produktbilder Import zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return array Produkt Bildinformationen
	 */
	public function getImageDataFields()
	{
		return array(
			'sku',
			'type',
			'attribute_set',
			'websites',
			'store',
			'thumbnail',
			'small_image',
			'image',
			'media_gallery'
		);
	}


	/**
	 * Gibt die CSV-Daten für das Löschen der Produkte zurück
	 *
	 * @return array
	 *
	 * @author akniss
	 * @version 1.00 29.08.2014
	 * @access public
	 *
	 */
	public function getProductDelete()
	{
		return array_unique($this->productsDelete);
	}

	//------------------------------------protected-area--------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Mapper_Io_Csv_Abstract::prepareLine()
	 */
	protected function prepareLine($line)
	{
		return $line;
	}


	/**
	 * Gibt die gemappten Produktdaten zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return array Produktdaten
	 */
	protected function getProductData()
	{

		$requiredProductData = $this->getRequiredProductData();

		$customProductData = $this->getHelper()->getCustomAttributes();

		return array_merge($requiredProductData, $customProductData);

	}


	/**
	 * Gibt die für den Import erforderlichen Produktdaten zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return array Produktdaten
	 */
	protected function getRequiredProductData()
	{

		// Global Product Data
		$productData = array(
			'_type' => $this->getHelper()->getType(),
			'_attribute_set' => $this->getHelper()->getAttributeSet(),
			'_product_websites' => $this->getHelper()->getWebsite(),
			'is_in_stock' => $this->getHelper()->inStock(),
			'name' => $this->getHelper()->getName(),
			'description' => $this->getHelper()->getDescription(),
			'short_description' => $this->getHelper()->getShortDescription(),
			'price' => $this->getHelper()->getSinglePrice(),
			//'tax_class_id' => $this->getHelper()->getTaxClassId(),
			'status' => $this->getHelper()->getStatus(),
			'visibility' => $this->getHelper()->getVisibility(),
			'_store' => $this->getHelper()->getStore()

		);

		if ($this->isOrphanProduct())
		{
			$productData ['sku'] = false;
			$productData ['_attribute_set'] = $this->getHelper()->getAttributeSet();
		}
		else
		{
			$productData ['sku'] = $this->getHelper()->getSku();
		}

		if ($this->isSimpleProduct())
		{
			$productData ['weight'] = $this->getHelper()->getWeight();
			$productData ['qty'] = $this->getHelper()->getQty();
		}

		return $productData;
	}


	/**
	 * Gibt die Varianten eines konfigurierbaren Produkts zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return array Varianten eines konfigurierbaren Produkts
	 */
	protected function getConfigurableProductVariants()
	{

		if (!$this->isConfigurableProduct())
		{
			return array();
		}

		$simpleSkus = $this->getHelper()->getSimpleSkus();

		$attributeSet = $this->getHelper()->getAttributeSet();

		$configurableProductVariants = array();

		if (is_array($simpleSkus) && count($simpleSkus) > 0)
		{
			foreach ($simpleSkus as $simpleSku)
			{

				$configurableProductVariants [] = array('_super_products_sku' => trim($simpleSku), 'sku' => false, '_attribute_set' => $attributeSet);
			}
		}

		return $configurableProductVariants;
	}


	/**
	 * Prüft, ob es sich um ein Waisen-Produkt (Kind-Produkt) handelt
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return boolean true | false
	 */
	protected function isOrphanProduct()
	{
		return isset ($this->data [$this->getHelper()->getType()] [$this->getHelper()->getSku()]) ? true : false;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Prüft, ob es sich um ein einfaches 'simple' Produkt handelt
	 *
	 * @return boolean true | false
	 */
	public function isSimpleProduct()
	{
		return $this->getHelper()->getType() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE ? true : false;
	}


	/**
	 * Prüft, ob es sich um ein konfigurierbares 'configurable' Produkt handelt
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return boolean true | false
	 */
	protected function isConfigurableProduct()
	{
		return $this->getHelper()->getType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE ? true : false;
	}


	/**
	 * Liefert die Hilfsklasse zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @return Aurednik_Integration_Helper_Mapper_Io_Article
	 */
	protected function getHelper()
	{
		return $this->helper;
	}


	/**
	 * Setzt die Hilfsklasse
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param array $lineData , Produktdaten
	 *
	 */
	protected function setHelper(array $lineData)
	{
		$this->helper->setImportData($lineData);
	}


	/**
	 * Fügt Produkt Bildinformationen hinzu,
	 * um diese Inforamtionen später verarbeiten zu können
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 */
	protected function addImageData($images = null)
	{
		$this->imageData [] = array(
			$this->getHelper()->getSku(),
			$this->getHelper()->getType(),
			$this->getHelper()->getAttributeSet(),
			$this->getHelper()->getWebsite(),
			$this->getHelper()->getStore(),
			$this->getHelper()->getThumbnailImage(),
			$this->getHelper()->getSmallImage(),
			$this->getHelper()->getBaseImage(),
			$this->getHelper()->getGalleryImages()
		);
	}


	/**
	 * Fügt ein Produkt hinzu.
	 * Produkte in dieser Varaible werden aus Magento gelöscht
	 *
	 * @author akniss
	 * @version 1.00 29.08.2014
	 * @access protected
	 */
	protected function addProductToDelete()
	{
		$this->productsDelete[] = $this->getHelper()->getSku();
	}


	/**
	 * Prüft, ob das Produkt aus Magento gelöscht werden kann
	 *
	 * @return boolean
	 *
	 * @author akniss
	 * @version 1.00 29.08.2014
	 * @access protected
	 */
	protected function canProductDelete()
	{
		return $this->getHelper()->canProductDelete();
	}

	//------------------------------------private-area----------------------------------------------------

}
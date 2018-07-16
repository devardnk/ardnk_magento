<?php

/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Aurednik_Block_Home_Product_New extends Mage_Core_Block_Template
{


	/**
	 *
	 * @var NEW_PRODUCT_CONFIG_OPT
	 */
	const NEW_PRODUCT_CONFIG_OPT = 'sdzecom_aurednik/home_new_products/new_product_attr';

	/**
	 *
	 * @var HOME_PRODUCT_CONFIG_OPT
	 */
	const HOME_PRODUCT_CONFIG_OPT = 'sdzecom_aurednik/home_new_products/home_product_attr';

	/**
	 * @var $productCollection
	 * Collection mit allen neuen Produkten
	 */
	private $productCollection = null;

	//---------------------------------- public area ------------------------------------------------------
	/**
	 * Kosntruktor
	 */
	public function __construct()
	{

		$this->addData(array(
			'cache_lifetime' => 3600, //cache für je 60 Minuten
			'cache_tags' => array('SDZeCOM'),
			'cache_key' => __CLASS__
		));

	}


	/**
	 * @author akniss
	 *
	 * @version 1.1
	 *
	 * @access public
	 *
	 * Liefert alle Produkte,
	 * die "neu" sind und auf der Startseite angezeigt werden sollten
	 * Die Reihenfolge der Produkte ist zufällig.
	 *
	 * @return $productCollection
	 */
	public function getProductCollection()
	{

		// Nur laden, falls noch nicht geschehen, da dieser Teil sonst mehrfach aufgerufen wird
		if (is_null($this->productCollection))
		{

			$this->productCollection = Mage::getModel('catalog/product')->getCollection();

			$this->productCollection->addAttributeToSelect('name');
			$this->productCollection->addAttributeToSelect('small_image');
			$this->productCollection->addAttributeToSelect('short_description');
			$this->productCollection->addAttributeToSelect('price');

			$newProductsOption = Mage::getStoreConfig(self::NEW_PRODUCT_CONFIG_OPT);

			$homeProductsOption = Mage::getStoreConfig(self::HOME_PRODUCT_CONFIG_OPT);

			if (is_null($newProductsOption) || is_null($homeProductsOption))
			{
				return null;
			}

			//Nur Produkte laden, die "an_neu" Attribute gesetzt haben
			$this->productCollection->addAttributeToFilter(
				array(
					array('attribute' => Mage::getStoreConfig(self::NEW_PRODUCT_CONFIG_OPT), 'eq' => 1)
				)
			);

			//Nur Produkte laden, die "an_neu_home" Attribute gesetzt haben
			$this->productCollection->addAttributeToFilter(
				array(
					array('attribute' => Mage::getStoreConfig(self::HOME_PRODUCT_CONFIG_OPT), 'eq' => 1)
				)
			);

			//Zufällige Reihenfolge
			$this->productCollection->getSelect()->order('rand()');


			//Begrenzen auf nur 4 Ergebnisse
			$this->productCollection->getSelect()->limit(4);
		}
		return $this->productCollection;
	}


	//---------------------------------- protected area ---------------------------------------------------

	//---------------------------------- private area -----------------------------------------------------
}

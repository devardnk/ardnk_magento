<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Block_Cms_Home_Entity_Highlight extends SDZeCOM_Aurednik_Block_Cms_Home_Entity
{

	/**
	 *
	 * @var SDZeCOM_Aurednik_Helper_Cms_Home $hlpr
	 */
	private $hlpr = null;

	/**
	 *
	 * @var array $configOptions
	 */
	private $configOptions = array();

	//---------------------------------- public area ------------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param number $entityId
	 *
	 * Konstruktor
	 *
	 */
	public function __construct($entityId = 0)
	{

		parent:: __construct($entityId);

		$this->hlpr = new SDZeCOM_Aurednik_Helper_Cms_Home ();

		$this->configOptions = Mage:: getStoreConfig(SDZeCOM_Aurednik_Helper_Cms_Home :: AUREDNIK_CONFIG_OPTIONS . "/" . SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_SETTIGNS);

		$this->addData(array(
			'cache_lifetime' => 86400,
			'cache_tags' => array("SDZeCOM"),
			'cache_key' => __CLASS__
		));
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Url des Bildes
	 *
	 * @return string Url zum Bild
	 */
	public function getImgUrl()
	{

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_IMG_SRC_ATTR]))
		{
			return "";
		}

		return
			SDZeCOM_Library_Helper_Directory:: joinPaths(
				Mage:: getBaseUrl(Mage_Core_Model_Store :: URL_TYPE_MEDIA), $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_IMG_SRC_ATTR])
			);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert den Alt Text des Bildes
	 *
	 * @return string Alt Text des Bildes
	 */
	public function getImgAltText()
	{

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_IMG_ALT_ATTR]))
		{
			return "";
		}

		return $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_IMG_ALT_ATTR]);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert den Alt Text des Bildes
	 *
	 * @return string Alt Text des Bildes
	 */
	public function getLinkHref()
	{

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_LINK_HREF_ATTR]))
		{
			return "";
		}

		return $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_LINK_HREF_ATTR]);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert den Link-Namen
	 *
	 * @return string Name des Links
	 */
	public function getLinkName()
	{

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_LINK_NAME_ATTR]))
		{
			return "";
		}

		return $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_LINK_NAME_ATTR]);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert den Kategorie Namen
	 *
	 * @return string Name der Kategorie
	 */
	public function getCategory()
	{

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_CATEGORY_ATTR]))
		{
			return "";
		}

		return $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_CATEGORY_ATTR]);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert den Teasertext
	 *
	 * @return string Teasertext
	 */
	public function getTeaserText()
	{

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_TEASER_ATTR]))
		{
			return "";
		}

		return $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_TEASER_ATTR]);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert den Preis
	 *
	 * @return number preis
	 */
	public function getPrice()
	{

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_PRICE_ATTR]))
		{
			return "";
		}

		return $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_PRICE_ATTR]);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Überschrift
	 *
	 * @return string Überschrift
	 */
	public function getHeadline()
	{

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_HEADLINE_ATTR]))
		{
			return "";
		}

		return $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_HIGHLIGHT_HEADLINE_ATTR]);
	}

	//---------------------------------- protected area ---------------------------------------------------
	//---------------------------------- private area -----------------------------------------------------
}
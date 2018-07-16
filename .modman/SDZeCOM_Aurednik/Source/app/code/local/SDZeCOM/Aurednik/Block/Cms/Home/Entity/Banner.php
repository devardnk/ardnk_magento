<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Block_Cms_Home_Entity_Banner extends SDZeCOM_Aurednik_Block_Cms_Home_Entity
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

		$this->configOptions = Mage:: getStoreConfig(SDZeCOM_Aurednik_Helper_Cms_Home :: AUREDNIK_CONFIG_OPTIONS . "/" . SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_BANNER_SETTIGNS);

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

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_BANNER_IMG_SRC_ATTR]))
		{
			return "";
		}

		return
			SDZeCOM_Library_Helper_Directory:: joinPaths(
				Mage:: getBaseUrl(Mage_Core_Model_Store :: URL_TYPE_MEDIA), $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_BANNER_IMG_SRC_ATTR])
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

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_BANNER_IMG_ALT_ATTR]))
		{
			return "";
		}

		return $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_BANNER_IMG_ALT_ATTR]);
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

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_BANNER_LINK_HREF_ATTR]))
		{
			return "";
		}

		return $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_BANNER_LINK_HREF_ATTR]);
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

		if (!isset ($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_BANNER_LINK_NAME_ATTR]))
		{
			return "";
		}

		return $this->getAttributeValue($this->configOptions [SDZeCOM_Aurednik_Helper_Cms_Home :: CMS_HOME_BANNER_LINK_NAME_ATTR]);
	}

	//---------------------------------- protected area ---------------------------------------------------
	//---------------------------------- private area -----------------------------------------------------
}
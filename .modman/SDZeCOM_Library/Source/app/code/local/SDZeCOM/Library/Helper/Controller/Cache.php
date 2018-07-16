<?php

/**
 * Hilfsklasse fürs Caching in Controllern
 *
 * @author fkolb
 *
 */
class SDZeCOM_Library_Helper_Controller_Cache
{
	static $CACHE_TAGS = array('SDZECOM_LIBRARY_CONTROLLER_CACHE');

	/**
	 * @var boolean
	 */
	protected $isActive = false;

	/**
	 * Gültiget des Caches in Sekunden
	 * @var int
	 */
	protected $lifetime = 3600;


	/**
	 * Initialisiert das Objekt
	 *
	 */
	public function __construct()
	{
		$cacheGroup = 'sdzecom_library';
		$useCache = Mage::app()->useCache($cacheGroup);
		$this->isActive = $useCache;
	}


	/**
	 * Erstellt anhand des Request-Strings und der StroreID einen eindeutigen Schlüssel für einen Aufruf
	 *
	 * @return string
	 */
	public function getActionCacheKey()
	{
		$cacheKey = Mage::app()->getRequest()->getRequestString();
		$cacheKey .= '_' . Mage::app()->getStore()->getId();

		$cacheKey = md5($cacheKey);
		return $cacheKey;
	}


	/**
	 * Versucht Inhalte aus dem Cache zu laden.
	 *
	 * @return boolean|Ambigous <mixed, false, boolean, string>
	 */
	public function loadActionCache()
	{
		$cacheKey = $this->getActionCacheKey();
		$objCache = Mage::app()->getCache();

		if (!$objCache || !$objCache->test($cacheKey) || !$this->isActive)
		{
			return false;
		}

		return $objCache->load($cacheKey);
	}


	/**
	 * Speichert den Inhalt in den Cache
	 *
	 * @param string $html
	 */
	public function saveActionCache($html)
	{
		$cacheKey = $this->getActionCacheKey();
		$objCache = Mage::app()->getCache();

		if ($objCache)
		{
			$objCache->save($html, $cacheKey, self::$CACHE_TAGS, $this->lifetime);
		}
	}
}
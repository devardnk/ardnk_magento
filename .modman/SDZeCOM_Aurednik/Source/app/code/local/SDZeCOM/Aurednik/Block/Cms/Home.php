<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Block_Cms_Home extends Mage_Core_Block_Template
{

	//---------------------------------- public area ------------------------------------------------------


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert alle aktiven Banner
	 *
	 * @param int $intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID, StoreViewId des aktuellen Storeviews
	 *
	 * @return array von  SDZeCOM_Aurednik_Block_Cms_Home_Entity_Banner
	 *
	 */
	public function getBannerCollection($intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID)
	{
		return $this->_getBannerCollection($intStoreViewId);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert alle aktiven Highlights
	 *
	 * @param int $intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID, StoreViewId des aktuellen Storeviews
	 *
	 * @return array von SDZeCOM_Aurednik_Block_Cms_Home_Entity_Highlight
	 *
	 */
	public function getHighlightCollection($intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID)
	{
		return $this->_getHighlightCollection($intStoreViewId);
	}

	//---------------------------------- protected area ---------------------------------------------------

	//---------------------------------- private area -----------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * Liefert alle aktiven Banner
	 *
	 * @param int $intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID, StoreViewId des aktuellen Storeviews
	 *
	 * @return array von SDZeCOM_Aurednik_Block_Cms_Home_Entity_Highlight
	 *
	 */
	private function _getBannerCollection($intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID)
	{

		$bannerCollect = Mage:: getModel('aurednik/cms_home_entity')->getActiveBannerCollection($intStoreViewId);

		if (!($bannerCollect instanceof Mage_Core_Model_Resource_Db_Collection_Abstract) || $bannerCollect->count() == 0)
		{
			return array();
		}

		$strEntityStoreMainTable = Mage::getModel('aurednik/cms_home_entity_store');

		$bannerBlockCollection = array();

		foreach ($bannerCollect as $current)
		{
			$bannerBlockCollection [$current->getId()] = new SDZeCOM_Aurednik_Block_Cms_Home_Entity_Banner ($current->getId());
		}

		return $bannerBlockCollection;

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert alle aktiven Highlights
	 *
	 * @param int $intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID, StoreViewId des aktuellen Storeviews
	 *
	 * @return array von SDZeCOM_Aurednik_Block_Cms_Home_Entity_Highlight
	 *
	 */
	private function _getHighlightCollection($intStoreViewId = Mage_Core_Model_App :: ADMIN_STORE_ID)
	{

		$highlightCollect = Mage:: getModel('aurednik/cms_home_entity')->getActiveHighlightCollection($intStoreViewId);

		if (!($highlightCollect instanceof Mage_Core_Model_Resource_Db_Collection_Abstract) || $highlightCollect->count() == 0)
		{
			return array();
		}

		$highlightBlockCollection = array();

		foreach ($highlightCollect as $current)
		{
			$highlightBlockCollection [$current->getId()] = new SDZeCOM_Aurednik_Block_Cms_Home_Entity_Highlight ($current->getId());

		}

		return $highlightBlockCollection;
	}
}
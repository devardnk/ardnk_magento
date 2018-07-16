<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Block_Html_Footer extends Mage_Page_Block_Html_Footer
{


	//---------------------------------- public area ------------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * Setzt Cms-Footer Blöcke
	 *
	 * @param int $storeViewId = Mage_Core_Model_App::ADMIN_STORE_ID , StoreViewId des aktuellen Shops
	 *
	 */
	public function getFooterBlocks($storeViewId = Mage_Core_Model_App::ADMIN_STORE_ID)
	{

		$cmsFooterBlocksIds = array(

			Mage::getStoreConfig(
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::AUREDNIK_CONFIG_OPTIONS . '/' .
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::CMS_FOOTTER_SETTINGS . '/' .
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::CMS_FOOTER_BLOCK_1, $storeViewId),

			Mage::getStoreConfig(
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::AUREDNIK_CONFIG_OPTIONS . '/' .
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::CMS_FOOTTER_SETTINGS . '/' .
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::CMS_FOOTER_BLOCK_2, $storeViewId),

			Mage::getStoreConfig(
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::AUREDNIK_CONFIG_OPTIONS . '/' .
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::CMS_FOOTTER_SETTINGS . '/' .
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::CMS_FOOTER_BLOCK_3, $storeViewId),

			Mage::getStoreConfig(
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::AUREDNIK_CONFIG_OPTIONS . '/' .
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::CMS_FOOTTER_SETTINGS . '/' .
				SDZeCOM_Aurednik_Helper_Cms_Block_Data::CMS_FOOTER_BLOCK_4, $storeViewId)

		);

		$cmsFooterBlocks = array();

		foreach ($cmsFooterBlocksIds as $currId)
		{

			$block = Mage::getModel('cms/block')->load(( int )$currId);

			if (is_null($block->getId()))
			{
				continue;
			}

			$cmsFooterBlocks [] = $block;
		}

		return $cmsFooterBlocks;
	}

	//---------------------------------- protected area ---------------------------------------------------
	//---------------------------------- private area -----------------------------------------------------
}

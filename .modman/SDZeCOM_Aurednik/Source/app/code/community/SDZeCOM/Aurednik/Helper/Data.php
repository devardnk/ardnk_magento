<?php
/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Aurednik_Helper_Data extends Mage_Core_Helper_Abstract {
	
	/**
	 * 
	 * @var SHIPPING_ACTIVE_CONFIG_OPTION
	 */
	const SHIPPING_ACTIVE_CONFIG_OPTION = "carriers/aurednikshipping_active";
	
	/**
	 *
	 * @var SHIPPING_CONFIG_OPTIONS
	 */
	const SHIPPING_CONFIG_OPTIONS = "carriers/aurednikshipping";
	
	/**
	 *
	 * @var AUREDNIK_CONFIG_OPTIONS
	 */
	const AUREDNIK_CONFIG_OPTIONS = "sdzecom_aurednik";
	

	/**
	 *
	 * @var PRODUCT_TYPES_CONFIG_OPTIONS
	 */
	const PRODUCT_TYPES_CONFIG_OPTIONS = "product_types";
	
	/**
	 * 
	 * @var PRODUCT_AVAILABILITY_CONFIG_OPTIONS
	 */
	const PRODUCT_AVAILABILITY_CONFIG_OPTIONS = "product_availability";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_ATTR
	 */
	const PRODUCT_AVAILABILITY_ATTR = "availability";
	

	/**
	 *
	 * @var PRODUCT_TYPE_STOCK_ITEM
	 */
	const PRODUCT_TYPE_STOCK_ITEM = "stockitems";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_CONFIG_OPTIONS
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_CONFIG_OPTIONS = "product_availability_stockitems_greenlight";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT = "greenlight_indetification";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_PICTURE
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_PICTURE = "greenlight_picture";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_TEXT
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_TEXT = "greenlight_text";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_CONFIG_OPTIONS
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_CONFIG_OPTIONS = "product_availability_stockitems_yellowlight";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT = "yellowlight_indetification";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_PICTURE
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_PICTURE = "yellowlight_picture";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_TEXT
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_TEXT = "yellowlight_text";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_CONFIG_OPTIONS
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_CONFIG_OPTIONS = "product_availability_stockitems_redlight";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT = "redlight_indetification";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_PICTURE
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_PICTURE = "redlight_picture";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_TEXT
	 */
	const PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_TEXT = "redlight_text";
	
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_FURNITURE_CONFIG_OPTIONS
	 */
	const PRODUCT_AVAILABILITY_FURNITURE_CONFIG_OPTIONS = "product_availability_furniture";
	

	/**
	 *
	 * @var PRODUCT_AVAILABILITY_FURNITURE_INDENT
	 */
	const PRODUCT_AVAILABILITY_FURNITURE_INDENT = "product_availability_furniture";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_FURNITURE_TEXT
	 */
	const PRODUCT_AVAILABILITY_FURNITURE_TEXT = "product_availability_furniture_text";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_DIRECT_ARTICLE_CONFIG_OPTIONS
	 */
	const PRODUCT_AVAILABILITY_DIRECT_ARTICLE_CONFIG_OPTIONS = "product_availability_directarticle";
	
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_DIRECT_ARTICLE_INDENT
	 */
	const PRODUCT_AVAILABILITY_DIRECT_ARTICLE_INDENT = "product_availability_directarticle";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_DIRECT_ARTICLE_TEXT
	 */
	const PRODUCT_AVAILABILITY_DIRECT_ARTICLE_TEXT = "product_availability_directarticle_text";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_OUTSIDE_PLAY_CONFIG_OPTIONS
	 */
	const PRODUCT_AVAILABILITY_OUTSIDE_PLAY_CONFIG_OPTIONS = "product_availability_outsideplay";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_OUTSIDE_PLAY_INDENT
	 */
	const PRODUCT_AVAILABILITY_OUTSIDE_PLAY_INDENT = "product_availability_outsideplay";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_OUTSIDE_PLAY_TEXT
	 */
	const PRODUCT_AVAILABILITY_OUTSIDE_PLAY_TEXT = "product_availability_outsideplay_text";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_CONFIG_OPTIONS
	 */
	const PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_CONFIG_OPTIONS = "product_availability_freightforwaridinggoods";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_INDENT
	 */
	const PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_INDENT = "product_availability_freightforwaridinggoods";
	
	/**
	 *
	 * @var PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_TEXT
	 */
	const PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_TEXT = "product_availability_freightforwaridinggoods_text";

	/**
	 *
	 * @var PRODUCT_TYPE_FURNITURE
	 */
	const PRODUCT_TYPE_FURNITURE = "furnitureidentification";
	
	/**
	 *
	 * @var PRODUCT_TYPE_OUTSIDE_PLAY_CONFIG_OPTION
	 */
	const PRODUCT_TYPE_OUTSIDE_PLAY = "outsideplayidentification";
	
	/**
	 *
	 * @var PRODUCT_TYPE_DIRECT_ARTICLE_CONFIG_OPTION
	 */
	const PRODUCT_TYPE_DIRECT_ARTICLE = "directarticleidentification";
	
	/**
	 *
	 * @var PRODUCT_TYPE_FORWARDING_GOOD_CONFIG_OPTION
	 */
	const PRODUCT_TYPE_FORWARDING_GOOD = "freightforwardinggoodsidentification";
	
	/**
	 *
	 * @var SHIPPING_VARIANTS_ATTR_CONFIG_OPTION
	 */
	const SHIPPING_VARIANTS_ATTR = "shippingvariantsattribute";

	
	/**
	 *
	 * @var SHIPPING_FREIGHT_YESNO_ATTR_CONFIG_OPTION
	 */
	const SHIPPING_FREIGHT_YESNO_ATTR = "freightcostsyesnoattribute";
	
	/**
	 *
	 * @var SHIPPING_FREIGHT_ATTR_CONFIG_OPTION
	 */
	const SHIPPING_FREIGHT_ATTR = "freightcostsattribute";
	
	/**
	 *
	 * @var SHIPPING_COSTS_STOCKITEM_CONFIG_OPTION
	 */
	const SHIPPING_COSTS_STOCKITEM = "carriers/aurednikshipping/stockitemshippingcosts";
	
	/**
	 *
	 * @var MIN_ORDER_VALUE_FREE_SHIPPING_STOCKITEM_CONFIG_OPTION
	 */
	const MIN_ORDER_VALUE_FREE_SHIPPING_STOCKITEM = "carriers/aurednikshipping/minordervaluefreeshippingstockitems";
	
	/**
	 * 
	 * @var ORDER_TYPE_FIELDSET
	 */
	const ORDER_TYPE_FIELDSET = "aurdenik_order_type";
	
	/**
	 *
	 * @var ORDER_COMMENT
	 */
	const ORDER_COMMENT = "order_comment";
	
	
	/**
	 *
	 * @var ORDER_TYPE_ORDER
	 */
	const ORDER_TYPE_ORDER = "order";
	
	/**
	 *
	 * @var ORDER_TYPE_REQUEST
	 */
	const ORDER_TYPE_REQUEST = "request";
	
	/**
	 * @author akniss
	 * @version 1.0
	 * 
	 * @access public
	 * 
	 * gibt das geladene Produkt anhand der ProduktId und storeId zurück
	 * 
	 * @param number $intProductId Id des Produkts
	 * @param number $intStoreId Id des stores in Magento
	 * 
	 * @return boolean false, falls das Produkt nicht existiert oder nicht geladen werden konnte | Mage_Catalog_Model_Product das geladene Produkt
	 */
	public function getProduct ( $intProductId , $intStoreId = 0 ) {

		if ( is_null ( $intProductId ) || $intProductId <= 0 || $intStoreId < 0 ) {
			return FALSE;
		}
		
		$objProductHlpr = new Mage_Catalog_Helper_Product ();
		
		$objPoduct = $objProductHlpr -> getProduct ( $intProductId, $intStoreId );

		if ( is_null ( $objPoduct -> getId () ) ) {
			return FALSE;
		}
		return $objPoduct;	
	}
}
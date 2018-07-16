<?php
/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Aurednik_Helper_Product extends SDZeCOM_Aurednik_Helper_Data {
	
	/**
	 * 
	 * @var $strProductType 
	 */
	private $strProductType = "";
	

	/**
	 *
	 * @var $arrConfigOptions
	 */
	private $arrConfigOptions = array ();
	
	/**
	 * 
	 * @var $dblShippingCostsStockItems
	 */
	private $dblShippingCostsStockItems = null;
	
	
	/**
	 *
	 * @var $intStoreId
	 */
	private $intStoreId = 0;
	

	/**
	 * 
	 * @var $objProduct
	 */
	private $objProduct = null;
	

	/**
	 * Konstruktor
	 */
	public function __construct ( $intProductId , $intStoreId = 0 ) {
		
		//StoreId setzen
		$this -> intStoreId = $intStoreId;
		
		$arrConfigOptions = array (
			self :: SHIPPING_CONFIG_OPTIONS ,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_TYPES_CONFIG_OPTIONS ,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_CONFIG_OPTIONS ,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_CONFIG_OPTIONS ,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_CONFIG_OPTIONS ,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_CONFIG_OPTIONS ,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_FURNITURE_CONFIG_OPTIONS ,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_DIRECT_ARTICLE_CONFIG_OPTIONS ,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_OUTSIDE_PLAY_CONFIG_OPTIONS ,
			self :: AUREDNIK_CONFIG_OPTIONS . "/" . self :: PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_CONFIG_OPTIONS		
		);

		foreach ( $arrConfigOptions as $strCurrentOption ) {
			
			$arrTmp = Mage :: getStoreConfig ( $strCurrentOption , $this -> intStoreId );
		
			if ( ! is_array ( $arrTmp ) || count ( $arrTmp ) == 0 ) {
				continue;
			}
			
			$this -> arrConfigOptions = array_merge ( $this -> arrConfigOptions , $arrTmp );
			
		}
		
		

		if ( is_null ( $this -> arrConfigOptions ) || $this -> arrConfigOptions === FALSE  ) {
			$this -> arrConfigOptions = array ();
			return;
		
		} 
			
		//Produkttyp Möbel
		if ( isset ( $this -> arrConfigOptions [ self :: PRODUCT_TYPE_FURNITURE ] ) ) {
			 $this -> arrConfigOptions [ self :: PRODUCT_TYPE_FURNITURE ] =  
				strtolower ( str_replace ( array ( " " , "\r" , "\n" , "\t" ) , "" ,  $this -> arrConfigOptions [ self :: PRODUCT_TYPE_FURNITURE ] ) );
		}
		
		//Produkttyp Lagerware
		if ( isset ( $this -> arrConfigOptions [ self :: PRODUCT_TYPE_STOCK_ITEM ] ) ) {
			 $this -> arrConfigOptions [ self :: PRODUCT_TYPE_STOCK_ITEM ] = 
				strtolower ( str_replace ( array ( " " , "\r" , "\n" , "\t" ) , "" ,  $this -> arrConfigOptions [ self :: PRODUCT_TYPE_STOCK_ITEM ] ) );
		}
		
		//Produkttyp Direktartikel
		if ( isset ( $this -> arrConfigOptions [ self :: PRODUCT_TYPE_DIRECT_ARTICLE ] ) ) {
			$this -> arrConfigOptions [ self :: PRODUCT_TYPE_DIRECT_ARTICLE ] =
				strtolower ( str_replace ( array ( " " , "\r" , "\n" , "\t" ) , "" ,  $this -> arrConfigOptions [ self :: PRODUCT_TYPE_DIRECT_ARTICLE ] ) );
		}
		
		//Produkttyp Außenspiel
		if ( isset ( $this -> arrConfigOptions [ self :: PRODUCT_TYPE_OUTSIDE_PLAY ] ) ) {
			$this -> arrConfigOptions [ self :: PRODUCT_TYPE_OUTSIDE_PLAY ] = 
				strtolower ( str_replace ( array ( " " , "\r" , "\n" , "\t" ) , "" ,  $this -> arrConfigOptions [ self :: PRODUCT_TYPE_OUTSIDE_PLAY ] ) );
		}
		
		//Produkttyp Speditionsartikel
		if ( isset ( $this -> arrConfigOptions [ self :: PRODUCT_TYPE_FORWARDING_GOOD ] ) ) {
			$this -> arrConfigOptions [ self :: PRODUCT_TYPE_FORWARDING_GOOD ] = 
				strtolower ( str_replace ( array ( " " , "\r" , "\n" , "\t" ) , "" ,  $this -> arrConfigOptions [ self :: PRODUCT_TYPE_FORWARDING_GOOD ] ) );
		}
		
		//Produkt setzen
		$this -> setProduct ( $intProductId );
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 * 
	 * @param int $intProductId
	 *
	 * setzt das Produkt
	 *
	 */
	public function setProduct ( $intProductId ) {
		
		$objProduct = parent :: getProduct ( $intProductId , $this -> intStoreId );
		
		if ( $objProduct == FALSE ) {
			return;
		}
		
		$this -> objProduct = $objProduct;
	
		//Produkttyp setzen	
		if ( isset (  $this -> arrConfigOptions [ self :: SHIPPING_VARIANTS_ATTR ] ) ) {	
			$strProductType = $this -> objProduct -> getAttributeText (
				 $this -> arrConfigOptions [ self :: SHIPPING_VARIANTS_ATTR ] );
		}
		
		if ( ! is_null ( $strProductType ) || $strProductType !== FALSE || strlen ( $strProductType ) > 0 ) {
			$this -> strProductType = 
				strtolower ( str_replace ( array ( " " , "\r" , "\n" , "\t" ) , "" , $strProductType ) );
		}
		
		//Versandpreis für die Lagerware setzen
		if ( isset (  $this -> arrConfigOptions [ self :: SHIPPING_COSTS_STOCKITEM ] ) ) {
			$this -> dblShippingCostsStockItems = 
				$this -> arrConfigOptions [ self :: SHIPPING_COSTS_STOCKITEM ];
		}
		
		//Mindestbestellwert für freien Versand
		if ( isset (  $this -> arrConfigOptions [ self :: MIN_ORDER_VALUE_FREE_SHIPPING_STOCKITEM ] ) ) {
			$this -> intMinOrderValueForFreeShippingStockItems =
			$this -> arrConfigOptions [ self :: MIN_ORDER_VALUE_FREE_SHIPPING_STOCKITEM ];
		}
		 
	}

	/**
	 * @author akniss
	 * 
	 * @version 1.0
	 * 
	 * @access public
	 * 
	 * Prüft ob das übergebene Produkt ein Möbelstück ist
	 * 
	 * @return boolean TRUE falls das Produkt ein Möbelstück ist | FALSE
	 */
	public function isFurniture () {

		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_TYPE_FURNITURE ] ) ) {
			return FALSE;
		}
		
		return  SDZeCOM_Library_Helper_String :: isEqual ( 
			$this -> strProductType ,  $this -> arrConfigOptions [ self :: PRODUCT_TYPE_FURNITURE ] );
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt ein Direktartikel ist
	 *
	 * @return boolean TRUE falls das Produkt ein Direktartikel ist | FALSE
	 */
	public function isDirectArticle () {

		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_TYPE_DIRECT_ARTICLE ] ) ) {
			return FALSE;
		}

		return  SDZeCOM_Library_Helper_String :: isEqual (
			$this -> strProductType ,  $this -> arrConfigOptions [ self :: PRODUCT_TYPE_DIRECT_ARTICLE ] );
		
	
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt ein Außenspiel ist
	 *
	 * @return boolean TRUE falls das Produkt ein Außenspiel ist | FALSE
	 */
	public function isOutsidePlay ( ) {

		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_TYPE_OUTSIDE_PLAY ] ) ) {
			return FALSE;
		}
		
		return  SDZeCOM_Library_Helper_String :: isEqual (
			$this -> strProductType ,  $this -> arrConfigOptions [ self :: PRODUCT_TYPE_OUTSIDE_PLAY ] );
			
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt eine Lagerware ist
	 *
	 * @return boolean TRUE falls das Produkt eine Lagerware ist | FALSE
	 */
	public function isStockItem ( ) {

		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_TYPE_STOCK_ITEM ] ) ) {
			return FALSE;
		}
		
		return  SDZeCOM_Library_Helper_String :: isEqual (
			$this -> strProductType ,  $this -> arrConfigOptions [ self :: PRODUCT_TYPE_STOCK_ITEM ] );

	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt eine  ist
	 *
	 * @param int $intProductId
	 * @param int $intStoreId
	 *
	 * @return boolean TRUE falls das Produkt eine Speditionsware ist | FALSE
	 */
	public function isFreightForwardingGood ( ) {
		
		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_TYPE_FORWARDING_GOOD ] ) ) {
			return FALSE;
		}
		
		return  SDZeCOM_Library_Helper_String :: isEqual (
			$this -> strProductType ,  $this -> arrConfigOptions [ self :: PRODUCT_TYPE_FORWARDING_GOOD ] );		
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft ob das übergebene Produkt eine  ist
	 *
	 * @return boolean TRUE falls das Produkt eine Speditionsware ist | FALSE
	 */
	public function getProductType ( ) {
		return $this -> strProductType;
	}
	

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt die Versandkosten für Versand eines Artikels der Gruppe Lagerware
	 *
	 * @return double $dblShippingCosts Versandkosten für Versand eines Artikels der Gruppe Lagerware | FALSE, falls keiner existiert
	 */
	public function getFixedFreightShippingCosts () {
		
		if ( ! ( isset ( $this -> arrConfigOptions [ self :: SHIPPING_FREIGHT_YESNO_ATTR ] ) && 
			 isset ( $this -> arrConfigOptions [ self :: SHIPPING_FREIGHT_ATTR ] ) ) ) {
			
			return 0;
		}
		
		$intHasFreightCosts = $this -> objProduct -> getData ( $this -> arrConfigOptions [ self :: SHIPPING_FREIGHT_YESNO_ATTR ] );

		if ( is_null ( $intHasFreightCosts ) || strlen ( trim ( $intHasFreightCosts ) ) == 0 || $intHasFreightCosts == 0 ) {
			return 0;
		}
	
		return  $this -> objProduct -> getData ( $this -> arrConfigOptions [ self :: SHIPPING_FREIGHT_ATTR ] );
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt das zu Produkt zurück
	 *
	 * @return Mage_Catalog_Model_Product Produkt
	 */
	public function getProduct ($intProductId = 0 , $intStoreId = 0) {
		return $this -> objProduct;
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt den mindest Bestellwert für den freien Versand zurück (Lagerware)
	 *
	 * @return double Produkt
	 */
	public function getMinOrderValueForFreeShippingStockItems () {
		return $this -> dblMinOrderValueForFreeShippingStockItems;
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.1
	 *
	 * @access public
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück
	 *
	 * @return string Text für Produktverfügbarkeit
	 */
	public function getProductAvailabilityText () {
		
		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_ATTR ] ) ) {	
			return "";
		}

		$strProductAvailability = trim ( $this -> objProduct -> getAttributeText ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_ATTR ] ) );
	
		if ( strlen ( $strProductAvailability ) == 0 ) {
			return "";
		}
		
		if ( $this -> isStockItem () ) {
			
			return $this -> getProductAvailabilityTextStockItems ( $strProductAvailability );

		} else if ( $this -> isFurniture () ) {
			return  $this -> getProductAvailabilityTextFurniture ( $strProductAvailability );
		} else if ( $this -> isDirectArticle () ) {
			return $this -> getProductAvailabilityTextDirectArticle ( $strProductAvailability );
			
		} else if ( $this -> isOutsidePlay () ) {
			return $this -> getProductAvailabilityTextOutsidePlay ( $strProductAvailability );
			
		} else if ( $this -> isFreightForwardingGood() ) {
			return $this -> getProductAvailabilityTextFreightForwardingGoods ( $strProductAvailability );	
		}
	
		return "";
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt das Text für die Produktverfügbarkeit zurück
	 *
	 * @return string Text für Produktverfügbarkeit
	 */
	public function getProductAvailabilityPicture () {

		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_ATTR ] ) ) {
			return "";
		}

		$strProductAvailability = trim ( $this -> objProduct -> getAttributeText ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_ATTR ] ) );
	
		if ( strlen ( $strProductAvailability ) == 0 ) {

			return "";
		}
			
		if ( $this -> isStockItem () ) {
			return $this -> getProductAvailabilityPictureStockItems ( $strProductAvailability );
		}
		return "";
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt das Text für die Produktverfügbarkeit zurück
	 *
	 * @return string Text für Produktverfügbarkeit
	 */
	public function getProductAvailabilityPictureUrl () {
		return SDZeCOM_Library_Helper_Directory :: joinPaths ( 
				Mage :: getBaseUrl ( Mage_Core_Model_Store :: URL_TYPE_MEDIA ) , $this -> getProductAvailabilityPicture ()
			);
	}
	
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 * 
	 * gibt den Text für die Produktverfügbarkeit zurück (Lagerware)
	 * 
	 * @param $strProductAvailability, Id der Produktverfügbarkeit
	 *
	 * @return string Text für Produktverfügbarkeit (Lagerware)
	 */
	private function getProductAvailabilityTextStockItems ( $strProductAvailability ) {

		$strProductAvailability = trim ( $strProductAvailability );
		
		if ( strlen ( $strProductAvailability ) == 0 ) {
			return "";
		}

		switch ( $strProductAvailability ) {
			case  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT ] :
					
				if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT ] ) ||
					 ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_TEXT ] ) ) { return ""; }
					
				return $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_TEXT ];
	
			case  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT ] :
					
				if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT ] ) ||
					 ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_TEXT ] )) { return ""; }
					
				return  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_TEXT ];
					
			case  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT ] :

				if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT ] ) ||
					 ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_TEXT ] ) ) { return ""; }
				return  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_TEXT ];
		}
		
		return "";
	}
	
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt das Bildnamen für die Produktverfügbarkeit zurück (Lagerware)
	 *
	 * @param $strProductAvailability, Id der Produktverfügbarkeit
	 *
	 * @return string Bildname für Produktverfügbarkeit (Lagerware)
	 */
	private function getProductAvailabilityPictureStockItems ( $strProductAvailability ) {
	
		$strProductAvailablity = trim ( $strProductAvailability );

		if ( strlen ( $strProductAvailability ) == 0 ) {
			return "";
		}
	
		switch ( $strProductAvailability ) {
			case  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT ] :

				if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_INDENT ] ) ||
					 ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_PICTURE ] ) ) { return ""; }

				return $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_GREEN_LIGHT_PICTURE ];
	
			case  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT ] :

				if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_INDENT ] ) ||
					 ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_PICTURE ] )) { return ""; }
					
				
				return  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_YELLOW_LIGHT_PICTURE ];
					
			case  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT ] :
					
				if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_INDENT ] ) ||
					 ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_PICTURE ] ) ) { return ""; }
				
				return  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_STOCK_ITEMS_RED_LIGHT_PICTURE ];
		}

		return "";
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück (Möbel)
	 * 
	 * @param $strProductAvailability, Id der Produktverfügbarkeit
	 *
	 * @return string Text für Produktverfügbarkeit (Möbel)
	 */
	private function getProductAvailabilityTextFurniture ( $strProductAvailability ) {
	
		$strProductAvailablity = trim ( $strProductAvailablity );
	
		if ( strlen ( $strProductAvailablity ) == 0 ) {
			return "";
		}
	
		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_FURNITURE_INDENT ] ) ||
			 ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_FURNITURE_TEXT ] ) ) { return ""; }
			
		if ( trim ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_FURNITURE_INDENT ] ) != $strProductAvailability ) {
			return "";
		}
		
		return  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_FURNITURE_TEXT ];	
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück (Direktartikel)
	 * 
	 * @param $strProductAvailability, Id der Produktverfügbarkeit
	 *
	 * @return string Text für Produktverfügbarkeit (Direktartikel)
	 */
	private function getProductAvailabilityTextDirectArticle ( $strProductAvailability ) {
	
		$strProductAvailablity = trim ( $strProductAvailability );
	
		if ( strlen ( $strProductAvailablity ) == 0 ) {
			return "";
		}
	
		
		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_DIRECT_ARTICLE_INDENT ] ) ||
			 ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_DIRECT_ARTICLE_TEXT ] ) ) { return ""; }

		if ( trim ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_DIRECT_ARTICLE_INDENT ] ) != $strProductAvailability ) {
			return "";
		}
		
		return  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_DIRECT_ARTICLE_TEXT ];
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück (Außenspiel)
	 * 
	 * @param $strProductAvailability, Id der Produktverfügbarkeit
	 *
	 * @return string Text für Produktverfügbarkeit (Außenspiel)
	 */
	private function getProductAvailabilityTextOutsidePlay ( $strProductAvailability ) {
	
		$strProductAvailablity = trim ( $strProductAvailability );
	
		if ( strlen ( $strProductAvailability ) == 0 ) {
			return "";
		}
	
		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_OUTSIDE_PLAY_INDENT ] ) ||
			 ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_OUTSIDE_PLAY_TEXT ] ) ) { return ""; }
		
		if ( trim ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_OUTSIDE_PLAY_INDENT ] ) != $strProductAvailability ) {
			return "";
		}
	
		return  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_OUTSIDE_PLAY_TEXT ];		
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * gibt den Text für die Produktverfügbarkeit zurück (Speditionsware)
	 * 
	 * @param $strProductAvailability, Id der Produktverfügbarkeit
	 *
	 * @return string Text für Produktverfügbarkeit (Speditionsware)
	 */
	private function getProductAvailabilityTextFreightForwardingGoods ( $strProductAvailability ) {
	
		$strProductAvailablity = trim ( $strProductAvailability );
	
		if ( strlen ( $strProductAvailability ) == 0 ) {
			return "";
		}
	
		if ( ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_INDENT ] ) ||
			 ! isset ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_TEXT ] ) ) { return ""; }
	
		if ( trim ( $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_INDENT ] ) != $strProductAvailablity ) {
			return "";
		}

		return  $this -> arrConfigOptions [ self :: PRODUCT_AVAILABILITY_FREIGHT_FORWARDING_GOODS_TEXT ];		
	}	
}
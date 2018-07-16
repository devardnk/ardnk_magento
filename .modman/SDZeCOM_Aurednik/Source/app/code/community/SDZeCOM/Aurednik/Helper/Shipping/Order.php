<?php
/**
 *
 * @author akniss
 * 
 * 
 *
 * @version 1.0
 *
 */
class SDZeCOM_Aurednik_Helper_Shipping_Order extends SDZeCOM_Aurednik_Helper_Data {

	/**
	 * 
	 * @var $arrShippingStockItems
	 */
	private $arrShippingStockItems = array ();
	

	/**
	 *
	 * @var $arrShippingDirectArticles
	 */
	private $arrShippingDirectArticles = array ();
	
	/**
	 *
	 * @var $arrShippingFurnitureArticles
	 */
	private $arrShippingFurnitureArticles = array ();
	
	/**
	 *
	 * @var $arrisOutsidePlayArticles
	 */
	private $arrShippingOutsidePlayArticles = array ();
	
	/**
	 *
	 * @var $arrShippingFreightForwardingGoodArticles
	 */
	private $arrShippingFreightForwardingGoodArticles = array ();
	
	/**
	 *
	 * @var $intStoreId
	 */
	private $intStoreId = 0;
	
	/**
	 *
	 * @var $dblMinOrderValueForFreeShippingStockItems
	 */
	private $dblMinOrderValueForFreeShippingStockItems = null;
	
	/**
	 *
	 * @var $dblShippingPriceStockItems
	 */
	private $dblShippingPriceStockItems = null;
	
	
	
	
	/**
	 * Konstruktor
	 */
	public function __construct ( $arrOrderItems = array () , $intStoreId = 0 ) {
		
 		$this -> intStoreId = 0;
	
 		foreach ( $arrOrderItems as $objOrderItem ) {

 			$objShippingHlpr = new SDZeCOM_Aurednik_Helper_Product ( $objOrderItem -> getProduct () -> getId () , $this -> intStoreId );

 			if ( $objShippingHlpr -> isStockItem () ) {
 				$this -> arrShippingStockItems [] = $objShippingHlpr;
 			} else if (  $objShippingHlpr -> isDirectArticle () ) {
 				$this -> arrShippingDirectArticles [] = $objShippingHlpr;
 			} else if ( $objShippingHlpr -> isFurniture () ) {
 				$this -> arrShippingFurnitureArticles [] = $objShippingHlpr;
 			} else if ( $objShippingHlpr -> isOutsidePlay () ) {
				$this -> arrShippingOutsidePlayArticles [] = $objShippingHlpr;
			} else if ( $objShippingHlpr -> isFreightForwardingGood () ) {
				$this -> arrShippingFreightForwardingGoodArticles [] = $objShippingHlpr;
 			}
 		}
 		
 		$this -> dblMinOrderValueForFreeShippingStockItems = Mage :: getStoreConfig ( self :: MIN_ORDER_VALUE_FREE_SHIPPING_STOCKITEM , $intStoreId );
 		
 		$this -> dblShippingPriceStockItems = Mage :: getStoreConfig ( self :: SHIPPING_COSTS_STOCKITEM , $intStoreId );
	}
	
	
 	/**
 	 * @author akniss
 	 * 
 	 * @version 1.0
 	 * 
 	 * @access public
 	 * 
 	 * Liefert die Versandkosten für Lagerware
 	 * 
 	 * @return number Versandkosten Lagerware
 	 */
	public function  getShippingCostsStockItems () {
		
		$dblTotalPrice = 0.0;
		
		
		foreach ( $this -> arrShippingStockItems as $objShippingHlpr ) {	
			$dblTotalPrice += $objShippingHlpr -> getProduct () -> getFinalPrice ();
		
			if ( $dblTotalPrice >= $this ->dblMinOrderValueForFreeShippingStockItems ) {
				return 0.0;
			}
		}

		return $this -> dblShippingPriceStockItems;
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Versandkosten für Direktartikel
	 *
	 * @return number Versandkosten Direktartikel
	 */
	public function  getShippingCostsDirectArticles () {
	
		$dblTotalCosts = 0.0;
	
		foreach ( $this -> arrShippingDirectArticles as $objShippingHlpr ) {
			$dblTotalCosts += $objShippingHlpr -> getFixedFreightShippingCosts ();
	
		}
	
		return $dblTotalCosts;
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Versandkosten für Möbel
	 *
	 * @return number Versandkosten Möbel
	 */
	public function  getShippingCostsFurniture () {
		return 0.0;
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Versandkosten für Außenspiel
	 *
	 * @return number Versandkosten Außenspiel
	 */
	public function  getShippingCostsOutsidePlay () {
		return 0.0;
	}
	
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob die Frachttabelle für Möbel angezeigt werden soll
	 *
	 * @return boolean TRUE | FALSE
	 */
	public function showFreightTableFurniture () {
		
		if ( count ( $this -> arrShippingFurnitureArticles ) == 0 ) {
			return FALSE;	
		}
	
		return TRUE;
		
	}
	

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob die Frachttabelle für Außenspiel angezeigt werden soll
	 *
	 * @return boolean TRUE | FALSE
	 */
	public function showFreightTableOutsidePlay () {
	
		if ( count ( $this -> arrShippingOutsidePlayArticles ) == 0 ) {
			return FALSE;
		}
	
		return TRUE;
	
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Versandkosten für Außenspiel
	 *
	 * @return number Versandkosten Außenspiel
	 */
	public function  getShippingCostsForwardingGoods () {
		$dblTotalCosts = 0.0;
	
		foreach ( $this -> arrShippingFreightForwardingGoodArticles as $objShippingHlpr ) {
			$dblTotalCosts += $objShippingHlpr -> getFixedFreightShippingCosts ();
	
		}
	
		return $dblTotalCosts;
	}
	
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die gesamten Versandkosten der Bestellung
	 *
	 * @return number gesamte Versandkosten der Bestellung
	 */
	public function getTotalShippingCosts () {
		return 
			$this -> getShippingCostsStockItems () + 
			$this -> getShippingCostsFurniture () +
			$this -> getShippingCostsOutsidePlay () +
			$this -> getShippingCostsDirectArticles () +
			$this -> getShippingCostsForwardingGoods (); 
	}
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die gesamten Frachtkosten der Bestellung
	 *
	 * @return number gesamte Frachtkosten der Bestellung
	 */
	public function getTotalShippingFreightCosts () {
		return
		$this -> getShippingCostsFurniture () +
		$this -> getShippingCostsOutsidePlay () +
		$this -> getShippingCostsDirectArticles () +
		$this -> getShippingCostsForwardingGoods ();
	}
	
	
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * gibt den Mindestbestellwert für freie Zustellung zurück (gilt nur für Lagerware )
	 *
	 * @return double $dblMinOrderValue Mindestbestellwert für freie Zustellung | FALSE, falls keiner existiert
	 */
	public function getMinOrderValueForFeeShippingStockItems () {
		return $this -> dblMinOrderValueForFreeShippingStockItems;
	}
	
	/**
	 * 
	 */
	public function getOrderType () {

		if ( count ( $this -> arrShippingFurnitureArticles ) > 0 || count ( $this -> arrShippingDirectArticles ) > 0 ) {
			return self :: ORDER_TYPE_REQUEST;
		}
		
		return self :: ORDER_TYPE_ORDER;
		
	
		
	}

}
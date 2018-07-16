<?php

/**
 *
 * @author akniss
 * @version 1.0
 *
 *
 *
 */
 class SDZeCOM_Aurednik_Model_Quote_Observer {

 	/**
 	 * @author akniss
 	 * @version 1.0

 	 * Speichert den Bestelltyp
 	 *
 	 * @param Varien_Event_Observer $objObserver
 	 */
 	public function saveOrderType( $objObserver ) {
 	
		$objQuote = $objObserver -> getEvent () 
			-> getCart () 
			-> getQuote ();

		if ( is_null ( $objQuote -> getId () ) ) {
			return FALSE;
		}
		

		$objShippingHlpr = new SDZeCOM_Aurednik_Helper_Shipping_Order ( $objQuote -> getAllItems () );

		$objQuote -> setData ( $objShippingHlpr :: ORDER_TYPE_FIELDSET , $objShippingHlpr -> getOrderType () ); 
		
 	}
 }
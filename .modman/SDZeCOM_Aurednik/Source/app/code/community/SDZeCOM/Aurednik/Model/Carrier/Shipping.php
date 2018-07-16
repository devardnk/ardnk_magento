<?php
/**
 * 
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Carrier_Shipping extends 
	Mage_Shipping_Model_Carrier_Abstract implements 
	Mage_Shipping_Model_Carrier_Interface {
	
	/**
	 * 
	 * @var string $_code
	 */
    protected $_code = 'aurednikshipping';    

    
	/**
	 * (non-PHPdoc)
	 * 
	 * @see Mage_Shipping_Model_Carrier_Abstract::collectRates()
	 */
	public function collectRates ( Mage_Shipping_Model_Rate_Request $objRequest ) {

		if ( ! $this -> getConfigFlag ( 'active' ) ) {
			return FALSE;
		}	

		$objResult = Mage :: getModel ( 'shipping/rate_result' );
		$objOrderShippingHlpr = new SDZeCOM_Aurednik_Helper_Shipping_Order ( $objRequest -> getAllItems () );
		$dblhippingPrice =  $objOrderShippingHlpr -> getTotalShippingCosts ();
		
		$objMethod = Mage :: getModel ( 'shipping/rate_result_method' );
		
		$objMethod -> setCarrier ( $this->_code );
		
		$objMethod -> setCarrierTitle ( $this -> getConfigData ( 'title' ) );

		$objMethod -> setMethodTitle ( $this -> getConfigData ( 'name' ) );
		
		$objMethod -> setPrice ( $dblhippingPrice );
		
		$objMethod -> setCost ( $dblhippingPrice );
		
		$objResult -> append ( $objMethod );
		
		return $objResult;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Mage_Shipping_Model_Carrier_Interface::getAllowedMethods()
	 */
    public function getAllowedMethods() {
		return array (
				$this -> _code => $this -> getConfigData ( 'name' )
		);
	}
    
    
    
	
}
<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Carrier_Shipping extends
	Mage_Shipping_Model_Carrier_Abstract implements
	Mage_Shipping_Model_Carrier_Interface
{

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
	public function collectRates(Mage_Shipping_Model_Rate_Request $objRequest)
	{
		if (!$this->getConfigFlag('active'))
		{
			return false;
		}

		$objResult = Mage:: getModel('shipping/rate_result');
		$arrAllItems = $objRequest->getAllItems();
		if (!isset ($arrAllItems [0]))
		{
			return $objResult;
		}

		$quoteItem = array_shift($arrAllItems);
		if (!method_exists($quoteItem, 'getQuote'))
		{
			return $objResult;
		}

		$shippingPrice = 0.0;
		$quote 	= $quoteItem->getQuote();
		$orderShippingHlpr 	= new SDZeCOM_Aurednik_Helper_Shipping_Order ($quote);
		$shippingPrice = $orderShippingHlpr->getTotalShippingCosts();

		$shippingMethod = Mage:: getModel('shipping/rate_result_method');

		if ($shippingPrice == 0)
		{
			$shippingText = Mage::getStoreConfig('carriers/aurednikshipping/textfreeshipping', 0);
		}
		else
		{
			$shippingText = Mage::getStoreConfig('carriers/aurednikshipping/textnonfreeshipping', 0);
		}

		$shippingMethod->setCarrier($this->_code);
		$shippingMethod->setCarrierTitle($shippingText);

		$shippingMethod->setMethod($this->_code);
		//$shippingMethod->setMethodTitle($this->getConfigData('name'));

		$shippingMethod->setPrice($shippingPrice);
		$shippingMethod->setCost($shippingPrice);
		$objResult->append($shippingMethod);

		return $objResult;
	}


	/**
	 * (non-PHPdoc)
	 * @see Mage_Shipping_Model_Carrier_Interface::getAllowedMethods()
	 */
	public function getAllowedMethods()
	{
		return array(
			$this->_code => $this->getConfigData('name')
		);
	}


}
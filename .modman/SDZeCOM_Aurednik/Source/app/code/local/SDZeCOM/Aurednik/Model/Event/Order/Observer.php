<?php

/**
 * Aurednik Order Observer
 *
 * Class SDZeCOM_Aurednik_Model_Event_Order_Observer
 *
 * @author Eugen Gutsche
 */
class SDZeCOM_Aurednik_Model_Event_Order_Observer extends Varien_Object
{
	/**
	 * @var int
	 */
	protected $storeID = 0;

	/**
	 * @var SDZeCOM_Aurednik_Helper_Shipping_Order $orderHelper
	 */
	protected $orderHelper = null;

	/**
	 * @var string, Frachtkostenanmerkung
	 */
	protected $frightCostHintText = '';


	/**
	 * Versandkosten im Warenkorb ausgeben
	 *
	 * @param $observer
	 * @return $this
	 */
	public function addShipping($observer)
	{
		$checkout = Mage::getSingleton('checkout/session');
		$quote = $checkout->getQuote();
		//	$quote->save();
		$shippingAddress = $quote->getShippingAddress();
		$shippingAddress->setShippingMethod('aurednikshipping_aurednikshipping');
		$quote->save();
		//first use the default Country code
		$country = Mage::getStoreConfig('general/country/default');

		//check if the customer has logged in
		if (Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			if ($customer->getPrimaryShippingAddress() && $customer->getPrimaryShippingAddress()->getCountry())
			{
				//use customer's shipping address country if there's one
				$country = $customer->getPrimaryShippingAddress()->getCountry();
			}
		}

		//only set country if it does not exists already
		if (!$shippingAddress->getCountryId())
		{
			$shippingAddress->setCountryId($country);
		}

		//allow shipping rates recalculation
		$shippingAddress->setCollectShippingRates(true);

		//rest qutoe item counts so that shipping calculation is based on the correct quantity
		$quote->collectTotals();

		$shippingAddress->collectShippingRates();

		if ($quote->getItemsCount())
		{
			//get available rates
			$rates = $shippingAddress->getGroupedAllShippingRates();

			if (count($rates))
			{
				//get the top positioned rate. It's based on the position set up in backend
				$topRate = reset($rates);
				$rateToApply = $topRate[0]->getCode();

				try
				{
					Mage::log('RATE: ' . $rateToApply, null, '01_sdz.log');
					//apply shipping
					$shippingAddress->setShippingMethod($rateToApply);
					$quote->save();

					//set checkoutstate to CHECKOUT_STATE_BEGIN
					//prevent the address from being removed when init() in Mage_Checkout_Model_Cart is called
					$checkout->resetCheckout();
				}
				catch (Mage_Core_Exception $e)
				{
					$checkout->addError($e->getMessage());
				}
				catch (Exception $e)
				{
					$checkout->addException($e, Mage::helper('checkout')->__('Cannot set shipping method.'));
					Mage::logException($e);
				}
			}
		}

		return $this;
	}


	/**
	 * Aurednik Block in die Admin Bestellübersicht hinzufügen
	 *
	 * @ticket https://projects.sdzecom.de/issues/6268
	 *
	 * @author Eugen Gutsche
	 * @param Varien_Event_Observer $observer
	 */
	public function getAdditionalOrderViewInfo(Varien_Event_Observer $observer)
	{
		$block = $observer->getBlock();
		if (($block->getNameInLayout() == 'order_info') && ($child = $block->getChild('aurednik.order.info.custom.block')))
		{
			$transport = $observer->getTransport();
			if ($transport)
			{
				$html = $transport->getHtml();
				$html .= $child->toHtml();
				$transport->setHtml($html);
			}
		}
	}


	/**
	 * Zusätzliche Informationen an der Bestellung speichern
	 *
	 * @ticket https://projects.sdzecom.de/issues/6268
	 *
	 * @author egutsche
	 *
	 * @param Varien_Event_Observer $observer
	 */

	public function saveAdditionalOrderData($observer)
	{
		$order = $observer->getOrder();
		$quote = $observer->getQuote();

		$request = Mage:: app()->getRequest();
		$this->storeID = Mage::app()->getStore()->getId();
		$this->orderHelper = new SDZeCOM_Aurednik_Helper_Shipping_Order($quote, $this->storeID);

		$this->setItemOrderData($order);
		$this->setGlobalOrderData($request, $order, $quote);
	}


	/**
	 * Bestellspezifische(Allgemeine) Werte an der Bestellung speichern
	 *
	 * @ticket https://projects.sdzecom.de/issues/6268
	 *
	 * @author Eugen Gutsche
	 *
	 * @param $objRequest
	 * @param $objOrder
	 * @param $quote
	 */
	protected function setGlobalOrderData($objRequest, $objOrder, $quote)
	{
		$strOrderType = trim($objRequest->getPost(SDZeCOM_Aurednik_Helper_Product :: ORDER_TYPE_FIELD));
		$orderComment = trim($objRequest->getPost('aurednik_order_comment'));
		$shippingTables = $this->orderHelper->getShippingTables();
		$additionalOrderAttributes = array(
			'aurednik_customer_number' => $quote->getCustomer()->getAurednikCustomerNumber(),
			'aurednik_order_comment' => $orderComment,
			'aurednik_order_fright_information' => $this->frightCostHintText,
			'aurednik_shipping_tables' => serialize($shippingTables)
		);

		if (strlen($strOrderType) == 0)
		{
			Mage:: throwException($this->__('Order type is not defined'));
		}

		foreach ($additionalOrderAttributes as $attribute => $value)
		{
			$objOrder->setData($attribute, $value);
		}
	}


	/**
	 * Produktspezifische Werte an der Bestellung speichern
	 *
	 * @ticket https://projects.sdzecom.de/issues/6268
	 *
	 * @author Eugen Gutsche
	 *
	 * @param $objOrder
	 */
	protected function setItemOrderData($objOrder)
	{
		/**
		 * @var Mage_Sales_Model_Order_Item $objOrder
		 */
		$allOrderItems = $objOrder->getAllItems();
		$storeSpecificFreightInfoAttribute = $this->orderHelper->getStoreSpecificFreightCostAttribute();

		foreach ($allOrderItems as $orderItem)
		{
			$productID = $orderItem->getProduct()->getID();
			/** @var SDZeCOM_Aurednik_Model_Product $product */
			$product = Mage::getModel('aurednik/product')->load($productID);

			$freightCostAddInfoAttr = $this->orderHelper->getFreightCostInfoAttribute();
			$freightInformation = $product->getAdditionalFreightCostInformation($freightCostAddInfoAttr);
			if (!empty($freightInformation))
			{
				$this->frightCostHintText = $this->orderHelper->getFreightCostHintText($freightInformation);
			}

			$aurednikShippingCostsDe = '';
			$aurednikShippingCostsAt = '';
			switch ($this->storeID)
			{
				case 2: // DE Store
					$aurednikShippingCostsDe = $product->getFreightShippingCostsDe();
					break;
				case 3: // AT Store
					$aurednikShippingCostsAt = $product->getFreightShippingCostsAt();
				default: // DE Store
			}

			$additionalOrderItemAttributes = array(
				'aurednik_shipping_type' => $product->getProductAvailabilityText(),
				'aurednik_shipping_de' => $aurednikShippingCostsDe,
				'aurednik_shipping_at' => $aurednikShippingCostsAt,
				'aurednik_shipping_information' => $product->getAdditionalFreightCostInformation($storeSpecificFreightInfoAttribute),
				'aurednik_info_outside_furniture' => $product->getData('an_424'),
				'aurednik_product_warnings' => $product->getProductWarningNotices()
			);

			$additionalProductData = $product->getAdditionalProductOrderData();
			$additionalOrderItemAttributes = array_merge($additionalOrderItemAttributes, $additionalProductData);

			$orderItem->setProductOptions($additionalOrderItemAttributes);
		}
	}
}

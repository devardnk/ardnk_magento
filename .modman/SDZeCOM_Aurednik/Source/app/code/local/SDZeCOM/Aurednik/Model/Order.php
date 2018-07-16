<?php

class SDZeCOM_Aurednik_Model_Order extends Mage_Sales_Model_Order
{
	protected $order = null;


	public function getCustomOrderVariable($variableName)
	{
		return 'Order Test Variable: ' . $variableName;
	}


	/**
	 * Aurednik Kundennummer liefern
	 *
	 * @author Eugen Gutsche
	 * @return mixed
	 */
	public function getAurednikCustomerNumber()
	{
		return $this->getAurednikOrder()->getData('aurednik_customer_number');
	}


	/**
	 * An der Bestellung hinterlegtes Kundenkommentar liefern
	 *
	 * @author Eugen Gutsche
	 * @return mixed
	 */
	public function getCustomerOrderComment()
	{
		return $this->getAurednikOrder()->getData('aurednik_order_comment');
	}


	/**
	 * Hinweise zur Frach liefern
	 * Beispiel:
	 *    Der Gesamtwert Ihrer Bestellung kann aufgrund von individuellen Frachtkosten abweichend sein.
	 *    Unser Kundenservice setzt sich nach Ihrer Bestellung mit Ihnen in Verbindung
	 *
	 * @author Eugen Gutsche
	 * @return mixed
	 */
	public function getFrightCostHint()
	{
		return $this->getAurednikOrder()->getData('aurednik_order_fright_information');
	}


	/**
	 * Frachttabellen aus der Bestellung liefern
	 *
	 * @author Eugen Gutsche
	 *
	 * @return mixed
	 */
	public function getFrightTables()
	{
		$frightTableHtml = '';
		$serializedData = $this->getAurednikOrder()->getData('aurednik_shipping_tables');
		$frightTables = unserialize($serializedData);

		if (count($frightTables) > 0)
		{
			foreach ($frightTables as $frightTable)
			{
				$frightTableHtml .= $frightTable;
			}
		}

		return $frightTableHtml;
	}


	/**
	 * Lädt die Bestellung mit dern Zusätzlichen Aurednik Daten
	 *
	 * @ticket https://projects.sdzecom.de/issues/6268
	 *
	 * @author Eugen Gutsche
	 *
	 * @return Mage_Core_Model_Abstract|null
	 */
	protected function getAurednikOrder()
	{
		if (empty($this->order))
		{
			$this->order = Mage::getModel('sales/order')->load($this->getId());
		}

		return $this->order;
	}
}
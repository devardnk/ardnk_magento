<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Block_Checkout_Agreements extends FireGento_MageSetup_Block_Checkout_Agreements
{

	/**
	 *
	 * @var $objOrderHlpr
	 */
	private $objOrderHlpr = null;


	/**
	 * Konstruktor
	 */
	public function __construct()
	{
		parent:: __construct();
		$this->objOrderHlpr = new SDZeCOM_Aurednik_Helper_Shipping_Order (Mage:: helper('checkout/cart')->getQuote());
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Prüft, ob eine Bestellung Produkt Warnhinweise enthält
	 *
	 * @return boolean TRUE | boolean FALSE
	 */
	public function hasOrderWarningNotices()
	{
		return $this->objOrderHlpr->hasOrderWarningNotices();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert die Id der Bestellbedinung,
	 * die für die Produkthinweise zuständig ist.
	 *
	 * @return number Id der Bestellbedinung
	 */
	public function getOrderWarningsAgreementId()
	{
		return $this->objOrderHlpr->getOrderProductWarningsAgreementId();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert eine Collection mit Bestellbeidungen
	 *
	 * @return $objAgreements | FALSE falls keine Bestellbedinungen existieren
	 */
	public function getOrdertWarningsNoticesAgreements()
	{

		if (!$this->hasAgreements() && !Mage:: getStoreConfigFlag('checkout/options/enable_agreements'))
		{
			return false;
		}

		if (!$this->hasOrderWarningNotices())
		{
			return false;
		}

		$objAgreements = Mage:: getModel('checkout/agreement')
			->getCollection()
			->addStoreFilter(Mage:: app()->getStore()->getId())
			->addFieldToFilter('is_active', 1)
			->addFieldToFilter('agreement_type', array('in' => array(SDZeCOM_Aurednik_Model_AgreementType :: AGREEMENT_TYPE_PRODUCT_WARNING_NOTICES)));


		if (!is_object($objAgreements) || $objAgreements->count() == 0)
		{
			return false;
		}

		return $objAgreements;
	}
}
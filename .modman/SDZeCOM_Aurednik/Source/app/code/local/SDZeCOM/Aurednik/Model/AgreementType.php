<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_AgreementType extends FireGento_MageSetup_Model_Source_AgreementType
{
	/**
	 *
	 * @var AGREEMENT_TYPE_PRODUCT_WARNING_NOTICES
	 */
	const AGREEMENT_TYPE_PRODUCT_WARNING_NOTICES = 4;


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * Liefert die Bestellbeding Optionen für die Produkt Warnhinweise
	 *
	 * @return array Agreement Typen Optionen
	 */
	public function toOptionArray()
	{

		$arrOptions = parent:: toOptionArray();

		$arrOptions [] = array(
			'value' => self :: AGREEMENT_TYPE_PRODUCT_WARNING_NOTICES,
			'label' => Mage:: helper('aurednik')->__('Checkout Product Warning Notices')
		);

		return $arrOptions;
	}
}

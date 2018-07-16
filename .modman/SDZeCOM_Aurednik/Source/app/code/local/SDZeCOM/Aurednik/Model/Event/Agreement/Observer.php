<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Model_Event_Agreement_Observer extends FireGento_MageSetup_Model_Observer
{

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @param Varien_Event_Observer $objObserver
	 *
	 * Erweitert das Backendformular das Feld für den Bestellbedinungtyp, umd die Produkt Warnhinweise
	 *
	 * @return SDZeCOM_Aurednik_Model_Event_Observer
	 */
	public function addOptionsForAgreements(Varien_Event_Observer $objObserver)
	{
		$objBlock = $objObserver->getEvent()->getBlock();

		if ($objBlock instanceof Mage_Adminhtml_Block_Checkout_Agreement_Edit_Form)
		{

			$objHelper = Mage:: helper('magesetup');

			$objForm = $objBlock->getForm();

			$objFieldset = $objForm->getElement('base_fieldset');

			if (is_null($objFieldset))
			{
				return $this;
			}

			$objFieldset->removeField('agreement_type');

			$objFieldset->addField(
				'agreement_type',
				'select',
				array(
					'label' => $objHelper->__('Display on'),
					'title' => $objHelper->__('Display on'),
					'note' => $objHelper->__('Require Confirmation on Customer Registration and/or Checkout'),
					'name' => 'agreement_type',
					'required' => true,
					'options' => Mage:: getSingleton('aurednik/agreementType')->getOptionArray()
				)
			);
		}

		return $this;
	}
}

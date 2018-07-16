<?php

/**
 * Cronjob Handler
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
abstract class Aurednik_Integration_Model_Cronjob_Abstract extends SDZeCOM_Integration_Model_Cronjob_Abstract
{

	//------------------------------------public-area-----------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Cronjob_Abstract::helper()
	 */
	public function helper()
	{
		return Mage::helper('integration');
	}


	/**
	 * (non-PHPdoc)
	 * @see SDZeCOM_Integration_Model_Cronjob_Abstract::factory()
	 */
	public function factory()
	{
		return Mage::getModel('aurednik_integration/factory');
	}

	//------------------------------------protected-area--------------------------------------------------
	//------------------------------------private-area----------------------------------------------------
}
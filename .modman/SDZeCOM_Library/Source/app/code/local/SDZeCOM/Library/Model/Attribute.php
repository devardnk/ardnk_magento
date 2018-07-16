<?php

/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Library_Model_Attribute extends Mage_Eav_Model_Entity_Attribute
{

	/**
	 *
	 * @var OPTION_VALUE
	 */
	const OPTION_VALUE = "attribute_code";

	/**
	 *
	 * @var OPTION_LABEL
	 */
	const OPTION_LABEL = "attribute_code";


	/**
	 * @return array Option
	 */
	public function toOptionArray()
	{
		return Mage::helper('library/Option')->toOptionArray($this->getCollection(), self::OPTION_LABEL, self::OPTION_VALUE);
	}


}
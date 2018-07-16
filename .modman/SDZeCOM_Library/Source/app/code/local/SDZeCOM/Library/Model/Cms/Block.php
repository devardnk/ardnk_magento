<?php

/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Library_Model_Cms_Block extends Mage_Cms_Model_Block
{

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return array Option
	 */
	public function toOptionArray()
	{
		return $this->getCollection()->toOptionArray();
	}
}
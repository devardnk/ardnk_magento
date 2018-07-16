<?php

/**
 *
 * @author egutsche
 *
 * @version 1.0
 *
 */
class SDZeCOM_Aurednik_Helper_Cms_Block extends SDZeCOM_Aurednik_Helper_Block_Data
{

	protected $_block;


	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return
	 */
	public function getFooterBlock()
	{
		if (!$this->_block)
		{
			$this->_block = Mage::getResourceModel('cms/block_collection')
				->load()
				->toOptionArray();
		}
		return $this->_block;
	}
}	
	

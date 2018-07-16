<?php

/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	/**
	 * Initialisieren des Tabs Block
	 */
	public function __construct()
	{

		parent:: __construct();

		$this->setId('aurednik_cms_home_new');

		$this->setDestElementId('aurednik_cms_home_new');
	}


	/**
	 * (non-PHPdoc)
	 *
	 * @see Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml()
	 */
	protected function _beforeToHtml()
	{

		$this->addTab('order_information', array(
			'label' => Mage:: helper('admin')->__('Information'),
			'title' => Mage:: helper('admin')->__('Information')
		));

		return parent:: _beforeToHtml();
	}
}
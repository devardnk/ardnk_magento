<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_Container extends Mage_Adminhtml_Block_Widget_Grid_Container
{


	/**
	 * Konstruktor
	 */
	public function __construct()
	{

		$this->_blockGroup = 'aurednik';

		$this->_controller = 'adminhtml_cms_home';

		$this->_headerText = Mage:: helper('aurednik')->__('Banner / Highlight');

		parent:: __construct();

		$this->removeButton('add');

		$this->_addButton(
			"add_banner",
			array(
				'label' => $this->__('add banner'),
				'onclick' => "setLocation('" . $this->getUrl('*/cms_new/index', array(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_TYPE => SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type :: TYPE_BANNER)) . "')"
			)
		);


		$this->_addButton(
			"add_highlight",
			array(
				'label' => $this->__('add highlight'),
				'onclick' => "setLocation('" . $this->getUrl('*/cms_new/index', array(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_TYPE => SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type :: TYPE_HIGHLIGHT)) . "')"
			)
		);
	}

}

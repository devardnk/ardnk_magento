<?php

/**
 *
 * @author sdz Aleksej Kniss
 *
 */
class SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_Edit_Form_Container extends Mage_Adminhtml_Block_Widget_Form_Container
{

	/**
	 *
	 * @var FORM_NAME
	 */
	const FORM_NAME = "aurednik_cms_home_edit";


	/**
	 */
	public function __construct()
	{

		parent:: __construct();

		$this->_blockGroup = 'aurednik';

		$this->_controller = 'adminhtml_cms_home_edit_form';

		$this->_mode = 'Tab';

		$this->_headerText = Mage:: helper('aurednik')->__('edit banner / highlight');

		$this->_removeButton('save');

		$this->_removeButton('reset');

		$this->_removeButton('back');

		$this->_removeButton('delete');

		$this->addButton(
			'back',
			array(
				'label' => Mage:: helper('admin')->__('Back'),
				'onclick' => "setLocation('" . $this->getUrl('*/cms_home/index') . "')",
				'class' => 'scalable back'),
			0,
			1);

		$intEditId = $this->getRequest()->getParam(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID);

		$strDeleteUrl = $this->getUrl('*/cms_edit/delete', array(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID => $intEditId));

		$this->addButton(
			'delete',
			array(
				'label' => Mage:: helper('admin')->__('Delete'),
				'class' => 'scalable delete',
				'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to do this?') . '\', \'' . $strDeleteUrl . '\')'),
			1,
			1
		);

		$this->addButton(
			'save',
			array(
				'label' => Mage:: helper('admin')->__('Save'),
				'onclick' => 'document.' . self :: FORM_NAME . '.submit()'),
			2,
			1
		);

	}

}

?>
<?php

/**
 *
 * @author sdz Aleksej Kniss
 *
 */
class SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Container extends Mage_Adminhtml_Block_Widget_Form_Container
{

	/**
	 *
	 * @var FORM_NAME
	 */
	const FORM_NAME = "aurednik_cms_home_new";


	/**
	 */
	public function __construct()
	{

		parent:: __construct();

		$this->_blockGroup = 'aurednik';

		$this->_controller = 'adminhtml_cms_home_new_form';

		$this->_mode = 'Tab';

		$this->_headerText = Mage:: helper('aurednik')->__('new banner / highlight');

		$this->_removeButton('save');

		$this->_removeButton('reset');

		$this->_removeButton('back');


		$this->addButton(
			'save',
			array(
				'label' => Mage:: helper('admin')->__('Save'),
				'onclick' => 'document.' . self :: FORM_NAME . '.submit()'),
			0,
			2
		);

		$this->addButton(
			'back',
			array(
				'label' => Mage:: helper('admin')->__('Back'),
				'onclick' => "setLocation('" . $this->getUrl('*/cms_home/index') . "')",
				'class' => 'scalable back'),
			0,
			1);

	}

}

?>
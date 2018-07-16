<?php

/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 *
 */
class SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

	/**
	 * @var POST_ENTITY_DATA
	 */
	const POST_ENTITY_DATA = "entity";

	/**
	 * @var POST_ENTITY_ATTRIBUTE_DATA
	 */
	const POST_ENTITY_ATTRIBUTE_DATA = "attribute";


	/**
	 * Prepare form before rendering HTML
	 *
	 * @return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm()
	{

		$intType = $this->getRequest()->getParam(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_TYPE);

		$objAttribute = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute ();

		$objEntityType = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type ();

		$objEntity = new SDZeCOM_Aurednik_Model_Cms_Home_Entity ();

		$objForm = new Varien_Data_Form (array(
			'id' => SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Container :: FORM_NAME . "_id",
			'action' => $this->getUrl('*/*/save'),
			'method' => 'post',
			'enctype' => 'multipart/form-data',
			'name' => SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Container :: FORM_NAME
		));

		//Set Enitty
		$objFieldset =
			$objForm->addFieldset('aurednik_cms_home_entity',
				array(
					'legend' => Mage:: helper('admin')->__('entity')));


		$objFieldset->addField(
			SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_NAME,
			'text',
			array(
				'name' => self :: POST_ENTITY_DATA . '[' . SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_NAME . ']',
				'label' => Mage:: helper('admin')->__('Name'),
				'title' => Mage:: helper('admin')->__('Name'),
				'required' => true,
				'disabled' => false,
				'class' => 'required-entry',
				'value' => ''
			));


		$field = $objFieldset->addField('store_id', 'multiselect', array(
			'name' => self :: POST_ENTITY_DATA . '[' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store ::  TABLE_COLUMN_STORE_ID . ']',
			'label' => Mage::helper('cms')->__('Store View'),
			'title' => Mage::helper('cms')->__('Store View'),
			'required' => true,
			'value' => $objEntity->getStore_id(),
			'values' => Mage:: getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
		));

		$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
		$field->setRenderer($renderer);

		$objFieldset->addField(
			SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_TYPE,
			'select',
			array(
				'name' => self :: POST_ENTITY_DATA . '[' . SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_TYPE . ']',
				'label' => Mage:: helper('admin')->__('Type'),
				'title' => Mage:: helper('admin')->__('Type'),
				'required' => true,
				'disabled' => false,
				'options' => array($intType => $objEntityType->load($intType)->getName()),
				'readonly' => true
			));


		$objFieldset->addField(
			SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ACTIVE,
			'select',
			array(
				'name' => self :: POST_ENTITY_DATA . '[' . SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_ACTIVE . ']',
				'label' => Mage:: helper('admin')->__('Active'),
				'title' => Mage:: helper('admin')->__('Active'),
				'required' => true,
				'disabled' => false,
				'options' => array(0 => Mage:: helper('admin')->__('No'), 1 => Mage:: helper('admin')->__('Yes'))
			));

		$objType = $objFieldset->addField(
			SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_SORT,
			'text',
			array(
				'name' => self :: POST_ENTITY_DATA . '[' . SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_SORT . ']',
				'label' => Mage:: helper('admin')->__('Sort'),
				'title' => Mage:: helper('admin')->__('Sort'),
				'required' => false,
				'value' => $objEntity->getSort(),
				'required' => true,
				'class' => 'required-entry'

			));

		$objAttributeCollection = $objAttribute->getCollection()->getByEntityTypeId($intType);

		$objFieldset =
			$objForm->addFieldset(
				'aurednik_cms_home_entity_data',
				array(
					'legend' => Mage:: helper('admin')->__('entity attribute data')
				)
			);

		foreach ($objAttributeCollection as $objCurrentFormAttribute)
		{

			$objFieldset->addField(
				$objCurrentFormAttribute->getName(),
				$objCurrentFormAttribute->getInput_type(),
				array(
					'name' => self :: POST_ENTITY_ATTRIBUTE_DATA . '[' . $objCurrentFormAttribute->getId() . ']',
					'label' => Mage:: helper('aurednik')->__($objCurrentFormAttribute->getName()),
					'title' => Mage:: helper('aurednik')->__($objCurrentFormAttribute->getTitle()),
					'required' => $objCurrentFormAttribute->getRequired() == 1 ? true : false,
					'disabled' => false,
				));
		}

		$objForm->setUseContainer(true);

		$this->setForm($objForm);

		return parent:: _prepareForm();
	}

}
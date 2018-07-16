<?php

/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 *
 */
class SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_Edit_Form_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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

		$intId = $this->getRequest()->getParam(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID);

		$objEntity = new SDZeCOM_Aurednik_Model_Cms_Home_Entity ();

		$objEntity->load($intId);

		$objAttribute = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute ();

		$objAttributeValue = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values ();

		$objEntityType = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type ();

		$objForm = new Varien_Data_Form (
			array(
				'id' => SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_Edit_Form_Container :: FORM_NAME,
				'action' => $this->getUrl('*/*/save'),
				'method' => 'post',
				'enctype' => 'multipart/form-data',
				'name' => SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_Edit_Form_Container :: FORM_NAME
			)
		);

		$this->setForm($objForm);

		$objForm->setUseContainer(true);

		//Set Enitty
		$objFieldset =
			$objForm->addFieldset('aurednik_cms_home_entity',
				array(
					'legend' => Mage:: helper('admin')->__('entity')));

		$objType = $objFieldset->addField(
			SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_ID,
			'text',
			array(
				'name' => self :: POST_ENTITY_DATA . '[' . SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_ID . ']',
				'label' => Mage:: helper('admin')->__('Id'),
				'title' => Mage:: helper('admin')->__('Id'),
				'required' => true,
				'value' => $objEntity->getId(),
				'readonly' => true,
				'class' => 'required-entry'

			));

		$objType = $objFieldset->addField(
			SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_NAME,
			'text',
			array(
				'name' => self :: POST_ENTITY_DATA . '[' . SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_NAME . ']',
				'label' => Mage:: helper('admin')->__('Name'),
				'title' => Mage:: helper('admin')->__('Name'),
				'required' => false,
				'value' => $objEntity->getEntity_name(),
				'class' => 'required-entry'

			));

		$field = $objFieldset->addField(SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store::TABLE_COLUMN_STORE_ID, 'multiselect', array(
			'name' => self :: POST_ENTITY_DATA . '[' . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store :: TABLE_COLUMN_STORE_ID . ']',
			'label' => Mage::helper('cms')->__('Store View'),
			'title' => Mage::helper('cms')->__('Store View'),
			'required' => true,
			'value' => $objEntity->getStore_id(),
			'values' => Mage:: getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
		));

		$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
		$field->setRenderer($renderer);


		$objType = $objFieldset->addField(
			SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_TYPE,
			'select',
			array(
				'name' => self :: POST_ENTITY_DATA . '[' . SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_TYPE . ']',
				'label' => Mage:: helper('admin')->__('Type'),
				'title' => Mage:: helper('admin')->__('Type'),
				'required' => true,
				'disabled' => true,
				'options' => $objEntityType->toOptionArray(),
				'readonly' => true,
				'value' => $objEntity->getType_id()
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
				'options' => array(0 => Mage:: helper('admin')->__('No'), 1 => Mage:: helper('admin')->__('Yes')),
				'value' => $objEntity->getActive()

			));

		$objFieldset->addField(
			SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_SORT,
			'text',
			array(
				'name' => self :: POST_ENTITY_DATA . '[' . SDZeCOM_Aurednik_Model_Cms_Home_Entity ::  TABLE_COLUMN_SORT . ']',
				'label' => Mage:: helper('admin')->__('Sort'),
				'title' => Mage:: helper('admin')->__('Sort'),
				'required' => false,
				'value' => $objEntity->getSort(),
				'class' => 'required-entry',
				'required' => true,
			));

		$objAttrCollection = $objAttribute->getCollection()->getByEntityTypeId($objEntity->getType_id());

		if ($objAttrCollection->count() == 0)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error no attributes'));
			$this->getResponse()->sendResponse();
		}

		$objFieldset =
			$objForm->addFieldset(
				'aurednik_cms_home_entity_data',
				array(
					'legend' => Mage:: helper('admin')->__('entity attribute data')
				)
			);

		foreach ($objAttrCollection as $objCurrentAttr)
		{

			$objEntityAttrValuesCollection = $objAttributeValue->getCollection()->getByEntityIdAndAttributeId($objEntity->getId(), $objCurrentAttr->getId());

			if ($objEntityAttrValuesCollection->count() > 0)
			{

				foreach ($objEntityAttrValuesCollection as $objCurrentAttrValue)
				{

					$objFieldset->addField(
						$objCurrentAttrValue->getId(),
						$objCurrentAttr->getInput_type(),
						array(
							'name' => self :: POST_ENTITY_ATTRIBUTE_DATA . '[' . $objCurrentAttr->getId() . ']',
							'label' => Mage:: helper('aurednik')->__($objCurrentAttr->getTitle()),
							'title' => Mage:: helper('aurednik')->__($objCurrentAttr->getTitle()),
							'required' => $objEntity->getRequired() == 1 ? true : false,
							'value' => $objCurrentAttrValue->getAttribute_value(),
							'class' => 'required-entry'
						));
				}
			}
			else
			{

				$objFieldset->addField(
					$objEntity->getId() . "_" . $objCurrentAttr->getId(),
					$objCurrentAttr->getInput_type(),
					array(
						'name' => self :: POST_ENTITY_ATTRIBUTE_DATA . '[' . $objCurrentAttr->getId() . ']',
						'label' => Mage:: helper('aurednik')->__($objCurrentAttr->getTitle()),
						'title' => Mage:: helper('aurednik')->__($objCurrentAttr->getTitle()),
						'required' => $objEntity->getRequired() == 1 ? true : false,
						'class' => 'required-entry'
					));
			}
		}
		return parent:: _prepareForm();
	}


}
<?php

$installer = $this;

$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('catalog_category');

if ($installer->getAttributeId($entityTypeId, SDZeCOM_Aurednik_Helper_Category::VIEW_MODE_ATTR_CODE) === false)
{
	$attributeId = $installer->addAttribute(
		'catalog_category',
		SDZeCOM_Aurednik_Helper_Category::VIEW_MODE_ATTR_CODE,
		array(
			'type' => 'int',
			'label' => 'Produktansicht',
			'input' => 'select',
			'global' => Mage_Catalog_Model_Resource_Eav_Attribute :: SCOPE_STORE,
			'visible' => true,
			'required' => true,
			'group' => 'Display Settings',
			'user_defined' => true,
			'default' => 0,
			'backend' => 'eav/entity_attribute_backend_array',
			'sort' => 100,
			'source' => 'aurednik/category_source'));
}

$installer->endSetup();

<?php

/**
 *
 * @author akniss
 *
 * Stellt Methoden bereit, um Banner | Highlight zu selektieren
 *
 * @copyright Copyright (c) 2014 SDZeCOM GmbH & Co. KG (http://www.sdzecom.de)
 *
 */
class SDZeCOM_Aurednik_Block_Cms_Home_Entity extends Mage_Core_Block_Template
{

	/**
	 *
	 * @var SDZeCOM_Aurednik_Model_Cms_Home_Entity $entityModel
	 */
	private $entityModel = null;

	/**
	 *
	 * @var SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute $attrCollect
	 */
	private $attrCollect = null;

	//---------------------------------- public area ------------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param number $entityId
	 *
	 * Konstruktor
	 *
	 */
	public function __construct($entityId = 0)
	{
		$this->entityModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity ();

		if ($entityId > 0)
		{
			$this->load($entityId);
		}

		$this->addData(array(
			'cache_lifetime' => 86400,
			'cache_tags' => array("SDZeCOM"),
			'cache_key' => __CLASS__
		));
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param number $attributeId
	 *
	 * Gibt den Wert eines Attributes zurück
	 *
	 * @return string $attributeValue
	 *
	 */
	public function getAttributeValue($attributeId)
	{

		if (is_null($attributeId) || $attributeId <= 0 || !isset ($this->attrCollect [$attributeId]))
		{
			return '';
		}

		$attribute = $this->attrCollect [$attributeId];

		$methodName = 'get' . ucfirst(SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ATTRIBUTE_VALUE);

		$attributeValue = $attribute->{$methodName} ();

		if (is_null($attributeValue))
		{
			return '';
		}

		return $attributeValue;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Liefert ein Enity type Id
	 *
	 * @return number entity type id
	 */
	public function getType()
	{
		return $this->entityModel->getType_id();
	}

	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param number $entityId
	 *
	 * @throws Mage_Core_Exception
	 *
	 * Lädt ein SDZeCOM_Aurednik_Block_Cms_Home_Entity Enity
	 *
	 * @return SDZeCOM_Aurednik_Block_Cms_Home_Entity
	 */
	protected function load($entityId)
	{

		if (is_null($entityId) || $entityId <= 0)
		{
			Mage:: throwException('Entity Id is null or empty');
		}

		$this->entityModel->load($entityId);

		if ($this->entityModel->getId() == 0)
		{
			Mage:: throwException('can not load entity');
		}

		$this->attrCollect = $this->getAttributeCollection();

		return $this;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Liefert ein Array mit allen Attributen und Werten eines Entities
	 *
	 * @return array von SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Attribute_Values_Collection
	 */
	protected function getAttributeCollection()
	{

		$attrModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute ();

		$attrColect = $attrModel->getCollection();

		$attrValuesTable = Mage:: getModel('aurednik/cms_home_entity_attribute_values')->getResource()->getMainTable();

		$attrColect
			->getSelect()
			->joinLeft(
				array($attrValuesTable => $attrValuesTable),
				"main_table.id =" . $attrValuesTable . "." . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ATTRIBUTE_ID,
				array(SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ATTRIBUTE_VALUE => $attrValuesTable . "." . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ATTRIBUTE_VALUE))
			->where(SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ENTITY_ID . "=?", $this->entityModel->getId());

		if (!($attrColect instanceof Mage_Core_Model_Resource_Db_Collection_Abstract) || $attrColect->count() == 0)
		{
			return array();
		}

		$attributes = array();

		foreach ($attrColect as $current)
		{
			$attributes [$current->getId()] = $current;
		}

		return $attributes;
	}

	//---------------------------------- private area -----------------------------------------------------
}
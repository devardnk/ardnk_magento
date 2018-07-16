<?php
$installer = $this;

$installer->startSetup();

$entityMainTable = Mage:: getModel('aurednik/cms_home_entity')->getResource()->getMainTable();
$entityAttributeMainTable = Mage:: getModel('aurednik/cms_home_entity_attribute')->getResource()->getMainTable();
$entityAttributeValuesMainTable = Mage:: getModel('aurednik/cms_home_entity_attribute_values')->getResource()->getMainTable();
$entityTypeMainTable = Mage:: getModel('aurednik/cms_home_entity_type')->getResource()->getMainTable();

$installer->run(
	"
DROP TABLE IF EXISTS " . $entityAttributeValuesMainTable . ";
DROP TABLE IF EXISTS " . $entityAttributeMainTable . ";
DROP TABLE IF EXISTS " . $entityMainTable . ";		
DROP TABLE IF EXISTS " . $entityTypeMainTable . ";		
		
CREATE TABLE `" . $entityTypeMainTable . "` (
`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_TYPE :: TABLE_COLUMN_ID . "` INT(11) NOT NULL AUTO_INCREMENT,
`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_TYPE :: TABLE_COLUMN_NAME . "` VARCHAR(200) NOT NULL,
PRIMARY KEY (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_TYPE :: TABLE_COLUMN_ID . "`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
		
INSERT INTO `" . $entityTypeMainTable . "` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_TYPE :: TABLE_COLUMN_NAME . "`) VALUES ('Banner'), ('Highlight');		

CREATE TABLE `" . $entityMainTable . "` (
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID . "` INT(11) NOT NULL AUTO_INCREMENT,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_NAME . "` VARCHAR(100) NOT NULL,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_TYPE . "` INT(11) NULL DEFAULT '0',
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ACTIVE . "` TINYINT(1) NOT NULL DEFAULT '0',
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_SORT . "` INT(11) NULL DEFAULT '0',
	PRIMARY KEY (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID . "`),
	INDEX `FK_aurednik_cmshome_entity_aurednik_cmshome_type` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_TYPE . "`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
	
CREATE TABLE `" . $entityAttributeMainTable . "` (
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_ID . "` INT(11) NOT NULL AUTO_INCREMENT,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_TYPE_ID . "` INT(11) NOT NULL,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_NAME . "` VARCHAR(50) NULL DEFAULT NULL,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_TITLE . "` VARCHAR(50) NULL DEFAULT NULL,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_INPUT_TYPE . "` VARCHAR(50) NULL DEFAULT NULL,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_REQUIRED . "` SMALLINT(1) NULL DEFAULT NULL,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_BACKEND_MODEL . "` VARCHAR(50) NULL DEFAULT NULL,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_SORT . "` INT(11) NULL DEFAULT '0',
	PRIMARY KEY (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_ID . "`),
	INDEX `FK_aurednik_cmshome_attribute_aurednik_cmshome_entity` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_INPUT_TYPE . "`),
	CONSTRAINT `FK_aurednik_cmshome_attribute_aurednik_cmshome_entity_type` FOREIGN KEY (`type_id`) REFERENCES `aurednik_cmshome_entity_type` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_TYPE :: TABLE_COLUMN_ID . "`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
		
INSERT INTO `" . $entityAttributeMainTable . "` 
	(
		`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_TYPE_ID . "`, 
		`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_NAME . "`, 
		`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_TITLE . "`, 
		`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_INPUT_TYPE . "`, 
		`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_REQUIRED . "`, 
		`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_BACKEND_MODEL . "`, 
		`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute :: TABLE_COLUMN_SORT . "`
	) 
	VALUES
	( 2, 'Kategorie', 'Kategorie', 'text', 0, NULL, 1),
	( 2, 'Ueberschrift', 'Ueberschrift', 'text', 0, NULL, 2),
	( 2, 'Teaser-Text', 'Teaser-Text', 'text', 0, NULL, 3),
	( 2, 'Preis', 'Preis', 'text', 0, NULL, 4),
	( 2, 'Link Href', 'Link Href', 'text', 0, NULL, 5),
	( 2, 'Link Name', 'Link Name', 'text', 0, NULL, 6),
	( 2, 'Bild Src', 'Bild Src', 'Image', 0, NULL, 7),
	( 2, 'Bild Title', 'Bild Title', 'text', 0, NULL, 8),
	( 1, 'Bild Src', 'Bild Src', 'Image', 0, NULL, 1),
	( 1, 'Bild Title', 'Bild Title', 'text', 0, NULL, 2),
	( 1, 'Link Href', 'Link Href', 'text', 0, NULL, 3),
	( 1, 'Link Name', 'Link Name', 'text', 0, NULL, 4);
				
CREATE TABLE `" . $entityAttributeValuesMainTable . "` (
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ID . "` INT(11) NOT NULL AUTO_INCREMENT,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ENTITY_ID . "` INT(11) NOT NULL DEFAULT '0',
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ATTRIBUTE_ID . "` INT(11) NOT NULL,
	`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ATTRIBUTE_VALUE . "` TEXT NULL,
	PRIMARY KEY (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ID . "`),
	INDEX `attribute_entity_id` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ENTITY_ID . "`),
	INDEX `attribute_id` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ATTRIBUTE_ID . "`),
	INDEX `FK_aurednik_cmshome_entity_id` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ENTITY_ID . "`),
	CONSTRAINT `FK_aurednik_cmshome_entity_id` FOREIGN KEY (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values :: TABLE_COLUMN_ENTITY_ID . "`) REFERENCES `aurednik_cmshome_entity` (`" . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID . "`) ON UPDATE CASCADE ON DELETE CASCADE
) COLLATE='utf8_general_ci' ENGINE=InnoDB;"


);

//Kunden erweitern
if ($installer->getAttributeId(1, SDZeCOM_Aurednik_Helper_Data::AUREDNIK_CUSTOMER_NUMBER_FIELD) === false)
{
	$installer->addAttribute(
		'customer',
		SDZeCOM_Aurednik_Helper_Data::AUREDNIK_CUSTOMER_NUMBER_FIELD,
		array(
			'label' => 'Kundennummer (Aurednik)',
			'type' => 'varchar',
			'input' => 'text',
			'required' => false,
			'global' => true,
			'visible' => true,
			'user_defined' => true,
			'sort_order' => 100,
			'position' => 100
		)
	);
	//used in Forms ist sehr wichtig, gibt an in welchen Formularen, das Attribut verwendet werden soll
	Mage::getSingleton('eav/config')
		->getAttribute('customer', SDZeCOM_Aurednik_Helper_Data::AUREDNIK_CUSTOMER_NUMBER_FIELD)
		->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit'))
		->save();
}

$installer->endSetup();


$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');

//Bestellung erweitern
if ($installer->getAttributeId(5, SDZeCOM_Aurednik_Helper_Data :: ORDER_TYPE_FIELD) === false)
{
	//Bestellung erweitern
	$installer->addAttribute(
		'order',
		SDZeCOM_Aurednik_Helper_Data :: ORDER_TYPE_FIELD,
		array(
			'type' => 'varchar',
			'visible' => true,
			'required' => false
		)
	);
}


if ($installer->getAttributeId(5, SDZeCOM_Aurednik_Helper_Data :: ORDER_COMMENT_FIELD) === false)
{

	$installer->addAttribute(
		'order',
		SDZeCOM_Aurednik_Helper_Data :: ORDER_COMMENT_FIELD,
		array(
			'type' => 'varchar',
			'visible' => true,
			'required' => false
		)
	);
}

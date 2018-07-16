<?php
$objInstaller = $this;

$objInstaller -> startSetup ();

$objQuoteInstaller = new Mage_Sales_Model_Mysql4_Setup ( 'core_setup' );

$objQuoteInstaller -> startSetup ();

$objQuoteInstaller -> addAttribute ( 'quote' , SDZeCOM_Aurednik_Helper_Data :: ORDER_TYPE_FIELDSET , array (
		'type' => 'varchar' , 
		'visible' => true , 
		'required' => false
) );

$objQuoteInstaller -> endSetup ();

$objInstaller -> installEntities ();

$objInstaller -> endSetup ();
<?php

$installer = $this;

$installer->startSetup();

/**
 * Create table 'newsletter/subscriber'
 */

$installer->run(

	' ALTER TABLE ' . 'newsletter_subscriber'
	. ' ADD COLUMN ' . 'subscriber_firstname' . ' VARCHAR(20)'
	. ' AFTER ' . 'subscriber_email;' .

	' ALTER TABLE ' . 'newsletter_subscriber'
	. ' ADD COLUMN ' . 'subscriber_lastname' . ' VARCHAR(20)'
	. ' AFTER ' . 'subscriber_firstname;' .

	'ALTER TABLE ' . 'newsletter_subscriber'
	. ' ADD COLUMN ' . 'subscriber_company' . ' VARCHAR(50)'
	. ' AFTER ' . 'subscriber_lastname;'

);

// vd::D($installer->getConnection ());
// $installer->getConnection ()
// 	->getTable ( 'newsletter/subscriber' )
// 	->addColumn ( 'company', Varien_Db_Ddl_Table::TYPE_TEXT, null, array (
// 		'lenth' 	=> '50',
// 		'default' 	=> '0' 
// 	), 	'company' );
// 	exit;
$installer->endSetup();

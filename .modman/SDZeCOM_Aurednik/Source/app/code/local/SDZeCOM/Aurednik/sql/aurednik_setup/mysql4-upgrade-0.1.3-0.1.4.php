<?php

/**
 * Erweitern der Bestellung um weitere Felder
 * @ticket https://projects.sdzecom.de/issues/6268
 *
 * @author egutsche
 *
 */
$installer = $this;
$installer->startSetup();

$table = 'sales_flat_order';

$aurednik_order_columns = array(
	'aurednik_customer_number' => 'varchar(50)',
	'aurednik_order_comment' => 'text',
	'aurednik_shipping_tables' => 'text',
	'aurednik_order_fright_information' => 'text'
);

foreach ($aurednik_order_columns as $column => $type)
{
	$installer->run(
		' ALTER TABLE ' . $table
		.' ADD COLUMN ' . $column . ' ' . $type . ';'
	);
}

$installer->endSetup();
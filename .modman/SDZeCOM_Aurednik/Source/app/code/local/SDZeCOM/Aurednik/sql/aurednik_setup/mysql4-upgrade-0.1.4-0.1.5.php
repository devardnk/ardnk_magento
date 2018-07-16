<?php

/**
 * Extend order table with additional column
 * The column contains an json string with additional order data
 * that can be accessed in different areas like template, mail, ...
 *
 * @ticket https://projects.sdzecom.de/issues/11678
 *
 * @author egutsche
 */

$installer = $this;
$installer->startSetup();

$table = 'sales_flat_order';
$aurednik_order_columns = array(
	'aurednik_additional_order_data' => 'text'
);

foreach ($aurednik_order_columns as $column => $type)
{
	$installer->run(
		' ALTER TABLE ' . $table
		.' ADD COLUMN ' . $column . ' ' . $type . ';'
	);
}

$installer->endSetup();
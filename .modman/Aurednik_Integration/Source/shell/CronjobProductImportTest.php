<?php
require '../app/Mage.php';

Mage::app('admin')->setUseSessionInUrl(false);

umask(0);

try {
    Mage::getConfig()->init()->loadEventObservers('crontab');
    Mage::app()->addEventArea('crontab');

    $schedule = new Mage_Cron_Model_Schedule();

    /** @var Aurednik_Integration_Model_Cronjob_Catalog_Product_Import $cron */
    $cron = Mage::getModel('aurednik_integration/cronjob_catalog_product_import');
    $cron->execute($schedule);

    echo '<pre><b>' . __METHOD__ . '</b><br>' . print_r($schedule->getMessages(), true) . '</pre>';
}
catch (Exception $e) {
    Mage::printException($e);
}
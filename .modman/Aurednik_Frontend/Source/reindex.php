<?php 

set_time_limit(0);

chdir(dirname(__FILE__));
require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}

Mage::app('admin')->setUseSessionInUrl(false);
umask(0);

/*AUTO REINDEX*/
$app = Mage::app ( $mageRunCode, $mageRunType );
for($i=1; $i<=9; $i++)
{
    $process = Mage::getSingleton('index/indexer')->getProcessById($i);
    $state = $process->getStatus();
    
    if($process->getStatus() == 'require_reindex')
    {
        $process->reindexAll();
    }
}
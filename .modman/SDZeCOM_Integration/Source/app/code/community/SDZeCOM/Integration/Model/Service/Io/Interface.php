<?php

interface SDZeCOM_Integration_Model_Service_Io_Interface
{
    public function fetchMultiple($pattern = null);

    /**
     * @param array $files
     * @return array
     */
    public function readMulitple(array $files);

    /**
     * @param $src
     * @return bool|string filename on success, false on failure (if thats not an exception)
     * @throws Exception
     * @throws Varien_Io_Exception
     */
    public function read($src);

    public function backupMulitple(array $files);

    public function backup($src, $prependDatetime = true);

    /**
     * @return array
     */
    public function getErrors();
}
<?php

/**
 *
 * @author mwalter
 */
abstract class SDZeCOM_Integration_Model_Service_In_Abstract extends SDZeCOM_Integration_Model_Service_Abstract
{
    /**
     * @var SDZeCOM_Integration_Model_Service_Io_Interface
     */
    protected $serviceIo = null;
    /**
     * @var SDZeCOM_Integration_Model_Mapper_Io_Interface
     */
    protected $mapper = null;
    /** @var array contains indexes that will be refreshed */
    protected $indexes = array();

    /**
     * Process Data
     *
     * @param array $products contains an array of products that need to be process
     * @return array contains error messages
     */
    abstract protected function processData(array $products);

    /**
     * Main method to execute service
     *
     * @return array|bool|null
     */
    public function execute()
    {
        ini_set('memory_limit', '1024M');

        // Fetch and Manage all files matching the pattern
        $files = $this->fetchFiles();

        if (count($files) == 0) {
            // No files fetched
            return null;
        }
   
        // Map Files to Array
        $products = $this->getMapper()->mapFromFiles($files);
     
        // Process Data
        $errorMessages = $this->processData($products);

        if (count($errorMessages) > 0) {
            return $errorMessages;
        }

        return true;
    }

    /**
     * Fetch and Manage all files matching the pattern
     *
     * @return array
     */
    protected function fetchFiles()
    {
        // @todo DEBUG:
//        return array(
//            // 'Artikel__20140116_085743.txt' => '/Volumes/Data/Entwicklung/code/sdzecom/aurednik/magento/public/var/sdzecom_integration/in/2014/01/20/20140120_105317_Artikel__20140116_085242.txt'
//            // 'Verfuegbarkeit__20140116_083314.txt' => '/Volumes/Data/Entwicklung/code/sdzecom/aurednik/magento/public/var/sdzecom_integration/in/2014/01/21/20140121_095321_Verfuegbarkeit__20140116_083314.txt'
//            'Verkaufspreis__20140116_083314.txt' => '/Volumes/Data/Entwicklung/code/sdzecom/aurednik/magento/public/var/sdzecom_integration/in/2014/01/21/20140121_153836_Verkaufspreis__20140116_090305.txt'
//        );

        $pattern = $this->getConfig('filename_pattern');

        $readFiles = $this->getServiceIo()->fetchMultiple($pattern);
        return $readFiles;
    }

    /**
     * Process all index Events that where created during the update
     */
    protected function processIndexReindex(array $index = null)
    {
        if (is_null($index)) {
            $index = $this->indexes;
        }
        $pCollection = Mage::getSingleton('index/indexer')->getProcessesCollection();
        foreach ($pCollection as $process) {
            /** @var $process Mage_Index_Model_Process */
            $indexerCode = $process->getIndexerCode();
            if (!array_key_exists($indexerCode, $index)) {
                continue;
            }

            if ($index[$indexerCode] == 0) {
                continue;
            }

            // Index has to be refreshed
            $process->reindexEverything();
        }
    }

    /**
     * @param SDZeCOM_Integration_Model_Service_Io_Interface $serviceIo
     */
    public function setServiceIo(SDZeCOM_Integration_Model_Service_Io_Interface $serviceIo)
    {
        $this->serviceIo = $serviceIo;
    }

    /**
     * @return SDZeCOM_Integration_Model_Service_Io_Interface
     */
    protected function getServiceIo()
    {
        return $this->serviceIo;
    }

    /**
     * @param \SDZeCOM_Integration_Model_Mapper_Io_Interface $mapper
     */
    public function setMapper(SDZeCOM_Integration_Model_Mapper_Io_Interface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return SDZeCOM_Integration_Model_Mapper_Io_Interface
     */
    protected function getMapper()
    {
        return $this->mapper;
    }

    /**
     * @param array $indexes
     */
    public function setIndexes($indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * @return array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }
}
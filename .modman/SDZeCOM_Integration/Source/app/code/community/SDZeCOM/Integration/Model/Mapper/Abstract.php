<?php

/**
 *
 * @author mwalter
 */
abstract class SDZeCOM_Integration_Model_Mapper_Abstract
    implements SDZeCOM_Integration_Model_Mapper_Io_Interface
{
    /**
     * @var array contains mapped data
     */
    protected $data = array();

    /**
     * @param $filename
     * @return mixed
     */
    abstract protected function mapFromFile($filename);

    public function mapFromFiles(array $files)
    {
        foreach ($files as $filename) {
            $this->mapFromFile($filename);
        }
        return $this->getData();
    }

    /**
     * @param array $data
     * @param string $key
     */
    public function addData( $data, $key = null)
    {
        if (is_null($key)) {
            $this->data[] = $data;
        }
        else {
            $this->data[$key] = $data;
        }
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @return array|null
     */
    public function getData($key = null)
    {
        if (is_null($key)) {
            return $this->data;
        }
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return null;
    }


    /**
     * @return SDZeCOM_Integration_Helper_Data
     */
    protected function helper()
    {
        return Mage::helper('sdzecom_integration');
    }
}
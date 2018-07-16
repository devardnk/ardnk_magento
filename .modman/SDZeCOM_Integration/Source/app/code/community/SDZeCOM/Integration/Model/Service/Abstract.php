<?php

/**
 *
 * @author mwalter
 */
abstract class SDZeCOM_Integration_Model_Service_Abstract
{
    /** @var array */
    protected $config = null;

    /**
     * @return SDZeCOM_Integration_Helper_Data
     */
    public function helper()
    {
        return Mage::helper('sdzecom_integration');
    }

    /**
     * @return SDZeCOM_Integration_Model_Factory
     */
    public function factory()
    {
        return Mage::getModel('sdzecom_integration/factory');
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $key
     * @return array|null
     */
    public function getConfig($key = null)
    {
        if (is_null($key) || is_null($this->config)) {
            return $this->config;
        }
        if(array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }
        return null;
    }

}
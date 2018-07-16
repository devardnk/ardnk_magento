<?php

/**
 *
 * @author mwalter
 *
 */
class SDZeCOM_Integration_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LOG_FILENAME = 'sdzecom_integration.log';

    public function log($msg, $level = null)
    {
        Mage::log($msg, $level, self::LOG_FILENAME);
    }

    /**
     * @param $group
     * @param string $key
     * @return mixed
     */
    public function getConfig($group, $key = '')
    {
        $path = 'sdzecom_integration' . '/' . $group ;
        if ($key != '') {
            $path .= '/' . $key;
        }
        return Mage::getStoreConfig($path);
    }


}

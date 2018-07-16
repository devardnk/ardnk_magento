<?php
/**
 * Class SDZeCOM_Integration_Model_Factory
 *
 * @author mwalter
 */
class SDZeCOM_Integration_Model_Factory
{
    /**
     * @param array $settings
     * @param string $ftpDir
     * @return SDZeCOM_Integration_Model_Service_Io_Ftp
     */
    public function getServiceIoFtp(array $settings, $ftpDir = '')
    {
        // Connect to FTP and change to directory
        $ftp = $this->getModelFtp();
        $ftp->open($settings);

        if ($ftpDir) {
            $ftp->cd($ftpDir);
        }

        // Use Service to handle fetching and backup
        /** @var SDZeCOM_Integration_Model_Service_Io_Ftp $service */
        $service = Mage::getModel('sdzecom_integration/service_io_ftp');
        $service->setFtp($ftp);
        return $service;
    }

    /**
     * @return SDZeCOM_Integration_Helper_Data
     */
    protected function helper()
    {
        return Mage::helper('sdzecom_integration');
    }

    /**
     * @return SDZeCOM_Io_Ftp
     */
    protected function getModelFtp()
    {
        return new SDZeCOM_Io_Ftp();
    }
} 
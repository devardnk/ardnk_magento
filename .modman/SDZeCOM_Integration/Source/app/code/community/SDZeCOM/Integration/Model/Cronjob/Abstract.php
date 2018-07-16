<?php
/**
 *
 * @author mwalter
 */
abstract class SDZeCOM_Integration_Model_Cronjob_Abstract
{
    protected $time = 0;

    /**
     * @return array|bool|null
     */
    abstract protected function executeService();

    /**
     * Executes the Job
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function execute(Mage_Cron_Model_Schedule $schedule)
    {
        $this->startTimer();

        //
        $errorMessages = $this->executeService();

        // Set Message to Schedule
        $message = 'Runtime: ' . $this->getRuntime() . '<br>' . "\n";
        if (is_null($errorMessages)) {
            $message .= 'No files found';
        }
        else {
            $message .= 'Errors: ' . print_r($errorMessages, true);
        }

        $schedule->setMessages($message);
    }

    /**
     * @return mixed
     */
    protected function startTimer()
    {
        return $this->time = microtime(true);
    }

    /**
     * @return string
     */
    protected function getRuntime()
    {
        return round(microtime(true) - $this->time, 2) . 's';
    }

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


}
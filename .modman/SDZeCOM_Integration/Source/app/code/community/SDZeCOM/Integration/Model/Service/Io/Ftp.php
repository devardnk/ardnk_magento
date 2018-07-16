<?php

class SDZeCOM_Integration_Model_Service_Io_Ftp
    extends SDZeCOM_Integration_Model_Service_Abstract
    implements SDZeCOM_Integration_Model_Service_Io_Interface
{
    const FTP_BACKUP_DIR_DEFAULT = 'backup';
    const WORKING_DIR_DEFAULT = 'sdzecom_integration';
    const WORKING_DIR_IN = 'in';
    const WORKING_DIR_OUT = 'out';
    const FETCH_MULTIPLE_PATTERN_DEFAULT = '/.*/';

    /**
     * @var SDZeCOM_Io_Ftp
     */
    protected $ftp = null;
    /**
     * @var string
     */
    protected $ftpBackupDir = '';
    /** @var string */
    protected $workingDirname = '';
    /** @var array Contains errors that occured during ftp commands */
    protected $errors = array();

    public function fetchMultiple($pattern = null)
    {
        if (is_null($pattern)) {
            $pattern = self::FETCH_MULTIPLE_PATTERN_DEFAULT;
        }

        // List FTP Directory containing files to import
        $files = $this->getFtp()->lsFilter('/' . $pattern . '/');

        // Check and Apply Files per Import
        $maxFilesPerImport = 1;
        if ($maxFilesPerImport !== '' and $maxFilesPerImport > 0) {
            $files = array_slice($files, 0, $maxFilesPerImport);
        }

        // Fetch Import files
        $readFiles = $this->readMulitple($files);

        // Backup Files
        $this->backupMulitple(array_keys($readFiles));

        return $readFiles;
    }

    /**
     * @param array $files
     * @return array
     */
    public function readMulitple(array $files)
    {
        $result = array();
        foreach ($files as $key => $filename) {
            try {
                // Fetch file
                $target = $this->read($filename);

                if ($target === false) {
                    // File could not be loaded, continue with next file
                    $this->addError($filename, 'file could not be loaded');
                    continue;
                }

                $result[$filename] = $target;
            }
            catch (Exception $e) {
                // File could not be loaded, continue with next file
                $this->addError($filename, $e->getMessage());
                continue;
            }
        }
        return $result;
    }

    /**
     * @param $src
     * @return bool|string filename on success, false on failure (if thats not an exception)
     * @throws Exception
     * @throws Varien_Io_Exception
     */
    public function read($src)
    {
        try {
            $workingDir = $this->getWorkingDir('in') . DS . date('Y') . DS . date('m') . DS . date('d');
            if (!is_dir($workingDir)) {
                mkdir($workingDir, 0777, true);
            }
            $target = $workingDir . DS . date('Ymd_His') . '_' . basename($src);

            $result = $this->getFtp()->read($src, $target);

            if ($result === false) {
                return false; // @todo we could throw an exception here
            }

            return $target;
        }
        catch (Varien_Io_Exception $ex) {
            throw $ex;
        }
    }

    public function backupMulitple(array $files)
    {
        foreach ($files as $key => $filename) {
            $this->backup($filename);
        }
    }

    public function backup($src, $prependDatetime = true)
    {
        $backupFile = $src;
        if ($prependDatetime) {
            $backupFile = date('Ymd_His') . '__' . $backupFile;
        }
        $dest = $this->getFtpBackupDir() . '/' . $backupFile;

        return $this->getFtp()->mv($src, $dest);
    }

    public function rm($file)
    {
        return $this->getFtp()->rm($file);
    }

    public function put($file, $data)
    {
        return $this->getFtp()->write($file, $data);
    }

    public function ls()
    {
        return $this->getFtp()->ls();
    }

    public function cd($dir)
    {
        return $this->getFtp()->cd($dir);
    }

    public function setFtp(SDZeCOM_Io_Ftp $ftp)
    {
        $this->ftp = $ftp;
    }

    public function getFtp()
    {
        return $this->ftp;
    }

    public function setFtpBackupDir($backupDir)
    {
        $this->ftpBackupDir = $backupDir;
    }

    public function getFtpBackupDir()
    {
        $backupDir = $this->ftpBackupDir;
        if ($this->ftpBackupDir == '') {
            $backupDir = self::FTP_BACKUP_DIR_DEFAULT;
        }
        return $backupDir;
    }

    /**
     * @param string $workingDir
     */
    public function setWorkingDirname($workingDir)
    {
        $this->workingDirname = $workingDir;
    }

    /**
     * @return string
     */
    public function getWorkingDirname()
    {
        $workdingDir = $this->workingDirname;
        if ($workdingDir == '') {
            $workdingDir = self::WORKING_DIR_DEFAULT;
        }
        return $workdingDir;
    }

    protected function getWorkingDir($type = null)
    {
        if ($type == 'in') {
            $subdir = self::WORKING_DIR_IN;
        }
        else {
            $subdir = self::WORKING_DIR_OUT;
        }
        return Mage::getBaseDir('var') . DS . $this->getWorkingDirname() . DS . $subdir;
    }

    public function _initErrors()
    {
        $this->errors = array();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    protected function addError($key, $msg)
    {
        $this->errors[$key] = $msg;
    }
}
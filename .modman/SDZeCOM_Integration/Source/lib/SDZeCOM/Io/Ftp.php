<?php
/**
 * Class SDZeCOM_Io_Ftp
 *
 * @author mwalter
 */
class SDZeCOM_Io_Ftp extends Varien_Io_Ftp
{
    public function lsFilter($filter)
    {
        $list = array();

        $files = $this->ls();
        foreach ($files as $key => $file) {
            $filePath = $file['id'];
            $filename = $file['text'];
            if (preg_match($filter, $filename)) {
                $list[] = $filename;
            }
        }

        return $list;
    }
}
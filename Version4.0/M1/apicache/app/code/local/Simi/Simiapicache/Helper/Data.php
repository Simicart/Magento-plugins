<?php

class Simi_Simiapicache_Helper_Data extends Mage_Core_Helper_Data
{
    public function flushCache() {
        $path = Mage::getBaseDir('var') . DS . 'cache' . DS . 'simiapi_json';
        if (is_dir($path)) {
            $this->_removeFolder($path);
        }
    }
    
    private function _removeFolder($folder)
    {
        if (is_dir($folder)) {
            $dir_handle = opendir($folder);
        }
        if (!$dir_handle) {
            return false;
        }
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($folder . "/" . $file)) {
                    unlink($folder . "/" . $file);
                } else {
                    $this->_removeFolder($folder . '/' . $file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($folder);
        return true;
    }
}
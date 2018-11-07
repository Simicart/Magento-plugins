<?php

class Simi_Simiapicache_Helper_Data extends Mage_Core_Helper_Data
{
    public function flushCache($path = null) {
        if(!$path){
            $path =  Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json';
        }
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

    public function removeFileCache($fileName, $path){
        $path = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json' . DS . $path;
        $filePath = $path . DS . md5($fileName) . ".json";
        if (is_dir($path)) {
            $dir_handle = opendir($path);
        }
        if ($dir_handle && file_exists($filePath)) {
            try{
                unlink($filePath);
            }catch(Exception $e){

            }
        }
    }

    public function removeOnList($id,$folderList = 'products_list',$type=true){
        $string = '"entity_id":"'.$id.'"';
        $path = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json' . DS . $folderList;
        if(is_dir($path)){
            $dir = new DirectoryIterator($path);
            foreach ($dir as $file) {
                $content = file_get_contents($file->getPathname());
                if (strpos($content, $string) !== false) {
                    // Bingo
                    try{
                        unlink($file->getPathname());
                        if(!$type) break;
                    }catch(Exception $e){

                    }
                }
            }
        }

    }
}
<?php

class Simi_Simideeplink_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnable(){
        $is_enable = Mage::getStoreConfig('simideeplink/general/enable');
        if($is_enable == 1){
            return true;
        }

        return false;
    }
}
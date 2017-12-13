<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 11/20/17
 * Time: 4:28 PM
 */
class Simi_Simipwa_Helper_Data extends Mage_Core_Helper_Data
{
    public function IsEnableForWebsite(){
        return Mage::getStoreConfig('simipwa/general/enable');
    }
}
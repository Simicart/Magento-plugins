<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Ztheme
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Ztheme Helper
 * 
 * @category    
 * @package     Ztheme
 * @author      Developer
 */
class Simi_Ztheme_Helper_Data extends Mage_Core_Helper_Abstract {

    public function addSpotproduct($installer) {
        $model = Mage::getModel('ztheme/spotproduct')->getCollection()->getFirstItem();
        $spotproducts = Mage::getModel('ztheme/config')->toOptionArray();
        $key = Mage::getModel('ztheme/config')->toKeySpotArray();
        if ($model->getData('spotproduct_id') == null) {
            $nSpot = count($spotproducts);
            for ($i = 0; $i < $nSpot; $i++) {
                $query = "INSERT INTO `{$installer->getTable('ztheme_spotproduct')}` (`position`,`spotproduct_name`,`spotproduct_key`,`status`)
                    VALUES (" . ($i + 1) . ",'" . $spotproducts[$i]['label'] . "','" . $key[$i] . "',1);";
                $installer->run($query);
            }
        }
    }
}

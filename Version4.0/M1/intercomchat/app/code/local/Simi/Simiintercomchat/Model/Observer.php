<?php

/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category
 * @package     Simiintercomchat
 * @copyright   Copyright (c) 2012
 * @license
 */

/**
 * Simiintercomchat Model
 *
 * @category
 * @package     Simiintercomchat
 * @author      Developer
 */
class Simi_Simiintercomchat_Model_Observer {

    public function addIntercomchatSettings($observer) {
        $storeviewObject = $observer->getObject();
        $enable = Mage::getStoreConfig("simiintercomchat/general/enable");
        if ($storeviewObject && $enable) {
            $storeviewInfo = $storeviewObject->storeviewInfo;
            $storeviewInfo['intercom_chat'] = array(
                'intercom_app_id' => Mage::getStoreConfig("simiintercomchat/general/intercom_app_id"),
                'intercom_ios_app_key' => Mage::getStoreConfig("simiintercomchat/general/intercom_ios_app_id"),
                'intercom_android_app_key' => Mage::getStoreConfig("simiintercomchat/general/intercom_android_app_id"),
            );
            $storeviewObject->storeviewInfo = $storeviewInfo;
        }
    }
}

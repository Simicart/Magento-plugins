<?php

/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category
 * @package     Simifbconnect
 * @copyright   Copyright (c) 2012
 * @license
 */

/**
 * Simifbconnect Model
 *
 * @category
 * @package     Simifbconnect
 * @author      Developer
 */
class Simi_Simifbconnect_Model_Observer
{

    public function addFbConnectSetting($observer) 
    {
        $storeviewObject = $observer->getObject();
        if ($storeviewObject) {
            $storeviewInfo = $storeviewObject->storeviewInfo;
            $storeviewInfo['facebook_connect'] = array(
                'invite_link' => Mage::getStoreConfig("simifbconnect/fbappinvite/invite_link"),
                'image_description_link' => Mage::getStoreConfig("simifbconnect/fbappinvite/image_description_link")
            );
            $storeviewObject->storeviewInfo = $storeviewInfo;
        }
    }

}

<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Simivideo
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simivideo Observer Model
 * 
 * @category    Magestore
 * @package     Simi_Simivideo
 * @author      Magestore Developer
 */
class Simi_Simivideo_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Simivideo_Model_Observer
     */
    public function connectorCatalogGetProductDetailReturn($observer) {
        if ($this->getConfig("enable") == 1) {
            try {
                $idOnWishlist = '0';
                $observerObject = $observer->getObject();
                $observerData = $observer->getObject()->getData();
                $productId = $observerData['data'][0]['product_id'];
                $videoCollection = Mage::getModel('simivideo/simivideo')->getCollection();
                $videoArray = array();
                foreach ($videoCollection as $video)
                {
                    if (in_array($productId, explode(",", $video->getData('product_ids')))) 
                    {
                        $videoArray[] = array(
                            'title'=>$video->getData('video_title'),
                            'key'=>$video->getData('video_key')
                            );
                    }
                }
                if (count($videoArray)>=1)
                    $observerData['data'][0]['youtube'] = $videoArray;
                $observerObject->setData($observerData);
            } catch (Exception $exc) {
                
            }
        }
    }

    public function connectorConfigGetPluginsReturn($observer) {
      if ($this->getConfig("enable") == 0) {
        $observerObject = $observer->getObject();
        $observerData = $observer->getObject()->getData();
        $contactPluginId = NULL;
        $plugins = array();
        foreach ($observerData['data'] as $key => $plugin) {
          if ($plugin['sku'] == 'simi_simivideo') continue;
          $plugins[] = $plugin;
        }
        $observerData['data'] = $plugins;
        $observerObject->setData($observerData);
      }
    }

    public function getConfig($value) {
        return Mage::getStoreConfig("simivideo/general/" . $value);
    }

}

<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category  
 * @package   Simivideo
 * @copyright   Copyright (c) 2012 
 * @license   
 */

/**
 * Simi Model
 * 
 * @category  
 * @package   Simivideo
 * @author    Developer
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

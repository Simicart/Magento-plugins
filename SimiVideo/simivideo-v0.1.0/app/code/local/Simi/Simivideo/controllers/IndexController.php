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
 * @package     Simi_Simivideo
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simivideo Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_Simivideo
 * @author      Magestore Developer
 */
class Simi_Simivideo_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function checkInstallAction() {
        echo "1";
        exit();
    }

    public function installDbAction() {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();

       $installer->run("

DROP TABLE IF EXISTS {$this->getTable('simivideo_videos')};

CREATE TABLE {$this->getTable('simivideo_videos')} (
  `video_id` int(11) unsigned NOT NULL auto_increment,
  `video_url` varchar(255) NULL default '',
  `video_key` varchar(255) NULL default '',
  `video_title` varchar(255) NULL default '',
  `product_ids` text NULL default '',
  `status` int(11) NULL, 
  PRIMARY KEY (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

        $installer->endSetup();
        echo "success";
    }

}

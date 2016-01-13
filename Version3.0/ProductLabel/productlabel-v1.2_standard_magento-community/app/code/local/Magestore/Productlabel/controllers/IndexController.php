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
 * @package     Magestore_Productlabel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Productlabel Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @author      Magestore Developer
 */
class Magestore_Productlabel_IndexController extends Mage_Core_Controller_Front_Action {

   public function checkInstallAction(){
		echo "1";
		exit();
   }
   
   public function installDbAction(){
		 $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("		
			DROP TABLE IF EXISTS {$setup->getTable('product_label')};
			CREATE TABLE {$setup->getTable('product_label')} (
			  `label_id` int(11) unsigned NOT NULL auto_increment,
			  `name` varchar(255) default '',
			  `description` text default '',
			  `status` smallint(6) NOT NULL default '2',
			  `from_date` datetime default NULL,
			  `to_date` datetime default NULL,
			  `priority` int(11) unsigned default '0',
			  `conditions_serialized` mediumtext default NULL,
			  `text` varchar(255) NOT NULL default '',
			  `image` varchar(255) NOT NULL default '',
			  `position` smallint(6) NOT NULL default '1',
			  `display` smallint(6) NOT NULL default '0',
			  `category_text` varchar(255) NOT NULL default '',
			  `category_image` varchar(255) NOT NULL default '',
			  `category_position` smallint(6) NOT NULL default '1',
			  `category_display` smallint(6) NOT NULL default '0',
			  `is_auto_fill` smallint(6) NOT NULL default '1',
			  `created_time` datetime NULL,
			  `update_time` datetime NULL,
			  `condition_selected` varchar(50) NOT NULL,
			  `threshold` int(11) unsigned default NULL,
			  PRIMARY KEY (`label_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			DROP TABLE IF EXISTS {$setup->getTable('product_label_entity')};
			CREATE TABLE {$setup->getTable('product_label_entity')} (
			  `product_label_entity_id` int(11) unsigned NOT NULL auto_increment,
			  `same_on_two_page` smallint(6) NOT NULL default '1',
			  `text` varchar(255) NOT NULL default '',
			  `text_frontend` varchar(255) NOT NULL default '',
			  `image` varchar(255) NOT NULL default '',
			  `position` smallint(6) NOT NULL default '1',
			  `display` smallint(6) NOT NULL default '0',
			  `category_text` varchar(255) NOT NULL default '',
			  `category_text_frontend` varchar(255) NOT NULL default '',
			  `category_image` varchar(255) NOT NULL default '',
			  `category_position` smallint(6) NOT NULL default '1',
			  `category_display` smallint(6) NOT NULL default '0',
			  `created_time` datetime NULL,
			  `update_time` datetime NULL,
			  `product_id` int(11) unsigned  NOT NULL,
			  FOREIGN KEY (`product_id`) REFERENCES {$setup->getTable('catalog/product')}  (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			  PRIMARY KEY (`product_label_entity_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			 DROP TABLE IF EXISTS {$setup->getTable('product_label_entity_value')};
			   CREATE TABLE {$setup->getTable('product_label_entity_value')} (
			  `value_id` int(10) unsigned NOT NULL auto_increment,
			  `product_label_entity_id` int(11) unsigned NOT NULL,
			  `store_id` smallint(5) unsigned  NOT NULL,
			  `attribute_code` varchar(63) NOT NULL default '',
			  `value` text NOT NULL,
			  UNIQUE(`product_label_entity_id`,`store_id`,`attribute_code`),
			  INDEX (`product_label_entity_id`),
			  INDEX (`store_id`),
			  FOREIGN KEY (`product_label_entity_id`) REFERENCES {$setup->getTable('product_label_entity')} (`product_label_entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			  FOREIGN KEY (`store_id`) REFERENCES {$setup->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			  PRIMARY KEY (`value_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			 DROP TABLE IF EXISTS {$setup->getTable('product_label_value')};
			   CREATE TABLE {$setup->getTable('product_label_value')} (
			  `value_id` int(10) unsigned NOT NULL auto_increment,
			  `label_id` int(11) unsigned NOT NULL,
			  `store_id` smallint(5) unsigned  NOT NULL,
			  `attribute_code` varchar(63) NOT NULL default '',
			  `value` text NOT NULL,
			   UNIQUE(`label_id`,`store_id`,`attribute_code`),
			  INDEX (`label_id`),
			  INDEX (`store_id`),
			  FOREIGN KEY (`label_id`) REFERENCES {$setup->getTable('product_label')} (`label_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			  FOREIGN KEY (`store_id`) REFERENCES {$setup->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			  PRIMARY KEY (`value_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			DROP TABLE IF EXISTS {$setup->getTable('product_label_flat_data')};
			CREATE TABLE {$setup->getTable('product_label_flat_data')} (
			  `product_label_flat_data_id` int(11) unsigned NOT NULL auto_increment,
			  `text` varchar(255) NOT NULL default '',
			  `image` varchar(255) NOT NULL default '',
			  `position` smallint(6) NOT NULL default '1',
			  `display` smallint(6) NOT NULL default '0',
			  `category_text` varchar(255) NOT NULL default '',
			  `category_image` varchar(255) NOT NULL default '',
			  `category_position` smallint(6) NOT NULL default '1',
			  `category_display` smallint(6) NOT NULL default '0',
			  `label_id` int(11) unsigned NOT NULL,
			  `product_id` int(11) unsigned  NOT NULL,
			  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
			  `priority` int(11) unsigned default '0',
			  `from_time` int(11) default 0,
			  `to_time` int(11) default 0,
			  `update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			   FOREIGN KEY (`product_id`) REFERENCES {$setup->getTable('catalog/product')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			   FOREIGN KEY (`label_id`) REFERENCES {$setup->getTable('product_label')} (`label_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			   FOREIGN KEY (`store_id`) REFERENCES {$setup->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			   PRIMARY KEY (`product_label_flat_data_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			ALTER TABLE {$setup->getTable('product_label')}
				ADD `is_apply` smallint(6) NOT NULL default '2';
		");
		$labels = array(
			array('image' => 'saleoff', 'name' => 'SALE OFF'),
			array('image' => 'new', 'name' => 'NEW'),
			array('image' => 'hot', 'name' => 'HOT'),
			array('image' => 'newarrivar', 'name' => 'NEW ARRIVAR'),
			array('image' => 'bigsale', 'name' => 'BIG SALE'),
			array('image' => 'freeship', 'name' => 'FREE SHIP'));


		foreach ($labels as $label) {
			$model = Mage::getModel('productlabel/productlabel')->setStoreId(0);
			$data['name'] = $label['name'];
			$data['description'] = 'Product label template ' . $label['name'] . ' for customers. Copyright (c) 2013 Magestore';
			$data['status'] = 2;
			$data['priority'] = 0;
			$data['display'] = 1;
			$data['text'] = $label['name'];
			$data['image'] = $label['image'] . '_big.png';
			$data['category_display'] = 1;
			$data['category_text'] = $label['name'];
			$data['category_image'] = $label['image'] . '_small.png';
			$data['is_auto_fill'] = 0;
			$data['condition_selected'] = '';
			$data['threshold'] = 10;
			$model->setData($data);
			try {
				$model->save();
			} catch (Exception $exc) {
				echo $exc->getMessage();
			}
		}
        $installer->endSetup();
        echo "success";
	}
}
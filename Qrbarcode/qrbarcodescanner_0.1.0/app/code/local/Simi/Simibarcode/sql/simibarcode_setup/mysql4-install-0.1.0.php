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
 * @category 	Magestore
 * @package 	Magestore_Simibarcode
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create simibarcode table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simibarcode')}; 
CREATE TABLE {$this->getTable('simibarcode')} (
    `barcode_id` int(11) unsigned NOT NULL auto_increment,        
    `barcode` varchar(255) default '',  
    `qrcode` varchar(255) default '',  
    `barcode_status` tinyint(3) NOT NULL default '1',
    `product_entity_id` int(11),
    `product_name` varchar(255) default '',
    `product_sku` varchar(255) default '',
    `created_date` datetime,
    UNIQUE (`barcode`),
    UNIQUE (`qrcode`),
    PRIMARY KEY  (`barcode_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

DROP TABLE IF EXISTS {$this->getTable('simibarcode_template')};
CREATE TABLE {$this->getTable('simibarcode_template')} (
    `barcode_template_id` int(11) unsigned NOT NULL auto_increment,  
    `barcode_template_name` varchar(255) default '',
    `html` text default '',
    `template` varchar(255) default '',
    `type` tinyint(3) NOT NULL default '1', 
    `status` tinyint(3) NOT NULL default '1',        
    PRIMARY KEY  (`barcode_template_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;    

DROP TABLE IF EXISTS {$this->getTable('simiqrcode_template')};
CREATE TABLE {$this->getTable('simiqrcode_template')} (
    `qrcode_template_id` int(11) unsigned NOT NULL auto_increment,  
    `qrcode_template_name` varchar(255) default '',
    `html` text default '',
    `template` varchar(255) default '',
    `type` tinyint(3) NOT NULL default '1', 
    `status` tinyint(3) NOT NULL default '1',        
    PRIMARY KEY  (`qrcode_template_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;    

");

// get_class(Mage::getModel('simibarcode/barcodetemplate'));
// add Barcode Templates
$data = array();
$data[] = array('barcode_template_name' => 'Barcode',
    'html' => '<div style="width: 220px; text-align: center;">
                <img style="width: 200px;" src="{{media url="/simibarcode/source/barcode.jpg"}}"/>
                <span style="float: left; text-align: center; width: 100%; font-size: 17px;">010091930191421</span>
               </div>',
    'template' => 'image_barcode.phtml',
    'status' => 1);

$data[] = array('barcode_template_name' => 'Product Name & Barcode',
    'html' => '<div style="width: 220px; text-align: center;">
                    <span style="float: left; width: 100%; font-size: 17px; text-align: left; margin-left: 14px;">Product Name</span>
                    <img style="width: 200px;" src="{{media url="/simibarcode/source/barcode.jpg"}}"/>
                    <span style="float: left; text-align: center; width: 100%; font-size: 17px;">010091930191421</span>
               </div>',
    'template' => 'name_barcode.phtml',
    'status' => 1);

$data[] = array('barcode_template_name' => 'Product Name & Price & Barcode',
    'html' => '<div style="width: 220px; text-align: center;">
                    <span style="float: left; font-size: 17px; text-align: left; width: 47%; margin-left: 13px;">Product Name</span>
                    <span style="font-size: 17px; float: left; text-align: left; margin-left: 55px; width: 20%;">Price</span>
                    <img style="width: 200px;" src="{{media url="/simibarcode/source/barcode.jpg"}}"/>
                    <span style="float: left; text-align: center; width: 100%; font-size: 17px;">010091930191421</span>
               </div>',
    'template' => 'name_price_barcode.phtml',
    'status' => 1);
foreach ($data as $template) {
    Mage::getModel('simibarcode/barcodetemplate')->addData($template)->save();
}

// add Qrcode Templates
$dataQr = array();
$dataQr[] = array('qrcode_template_name' => 'QR code',
    'html' => '<div style="width: 220px; text-align: center;">
                 <img style="width: 200px;" src="{{media url="/simibarcode/source/qrcode.jpg"}}"/>
               </div>',
    'template' => 'image_qrcode.phtml',
    'status' => 1);

$dataQr[] = array('qrcode_template_name' => 'Product Name & QR code',
    'html' => '<div style="width: 220px; text-align: center;">
                    <span style="float: left; width: 100%; font-size: 17px; text-align: left; margin-left: 14px;">Product Name</span>
                    <img style="width: 200px;" src="{{media url="/simibarcode/source/qrcode.jpg"}}"/>
               </div>',
    'template' => 'name_qrcode.phtml',
    'status' => 1);

$dataQr[] = array('qrcode_template_name' => 'Product Name & Price & QR code',
    'html' => '<div style="width: 220px; text-align: center;">
                   <span style="float: left; font-size: 17px; text-align: left; width: 47%; margin-left: 13px;">Product Name</span>
                   <span style="font-size: 17px; float: left; text-align: left; margin-left: 55px; width: 20%;">Price</span>
                   <img style="width: 200px;" src="{{media url="/simibarcode/source/qrcode.jpg"}}"/>
               </div>',
    'template' => 'name_price_qrcode.phtml',
    'status' => 1);
foreach ($dataQr as $template) {
    Mage::getModel('simibarcode/qrcodetemplate')->addData($template)->save();
}
$installer->endSetup();


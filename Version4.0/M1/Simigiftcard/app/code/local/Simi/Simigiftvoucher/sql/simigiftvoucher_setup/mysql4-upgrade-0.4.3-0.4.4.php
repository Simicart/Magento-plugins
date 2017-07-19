<?php
/**
 * Simi
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Simicart.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Simicart.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @module     Giftvoucher
 * @author      Simi Developer
 *
 * @copyright   Copyright (c) 2016 Simi (http://www.Simicart.com/)
 * @license     http://www.Simicart.com/license-agreement.html
 *
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$model = Mage::getModel('simigiftvoucher/gifttemplate');
//Simple template 
$data = array();
$data[0]['template_name'] = Mage::helper('simigiftvoucher')->__('Simple');
$data[0]['style_color'] = '#DC8C71';
$data[0]['text_color'] = '#949392';
$data[0]['caption'] = Mage::helper('simigiftvoucher')->__('Gift Card');
$data[0]['notes'] = '';
$data[0]['images'] = 'default.png,giftcard_simple_01.png,giftcard_simple_02.png,giftcard_simple_03.png,'
        . 'giftcard_simple_04.png,giftcard_simple_05.png,giftcard_simple_06.png,giftcard_simple_07.png,'
        . 'giftcard_simple_08.png,giftcard_simple_09.png,giftcard_simple_10.png,giftcard_simple_11.png,'
        . 'giftcard_simple_12.png,giftcard_simple_13.png,giftcard_simple_14.png,giftcard_simple_15.png,'
        . 'giftcard_simple_16.png,giftcard_simple_17.png,giftcard_simple_18.png';
$data[0]['design_pattern'] = Simi_Simigiftvoucher_Model_Designpattern::PATTERN_SIMPLE;

foreach ($data as $template) {
    $model->setData($template);
    try {
        $model->save();
    } catch (Exception $exc) {
        
    }
}

$installer->endSetup();

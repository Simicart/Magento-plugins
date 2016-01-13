<?php

$installer = $this;
$installer->startSetup();

$installer->run("

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

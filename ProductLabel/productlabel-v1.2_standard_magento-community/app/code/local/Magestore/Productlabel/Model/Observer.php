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
 * Productlabel Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @author      Magestore Developer
 */
class Magestore_Productlabel_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Productlabel_Model_Observer
     */
    public function controllerActionPredispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }
	public function catalogRuleApply($observer){
        $process = Mage::getSingleton('index/indexer')->getProcessByCode('product_label_indexer');
        $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        return $this;
    }
    public function viewProductLabelTabData($observer) {
        $product = $observer->getEvent()->getProduct();
        $productId = $observer->getEvent()->getProduct()->getId();
        $model = Mage::getModel('productlabel/productlabelentity')->setStoreId($product['store_id'])->load($productId, 'product_id');
        if (!Mage::registry('productlabelentity_data') && $model->getProductId())
            Mage::register('productlabelentity_data', $model);
    }

    public function saveProductLabelTabData($observer) {

        $product = $observer->getEvent()->getProduct();
        if ($labelData = Mage::app()->getRequest()->getPost()) {
            $store = $labelData['store_id'] ? $labelData['store_id'] : 0;
            if (isset($labelData['image']['delete'])) {
                Mage::helper('productlabel')->deleteImageFile($labelData['image']['value']);
                if ($labelData['same_on_two_page']) {
                    Mage::helper('productlabel')->deleteImageFile($labelData['category_image']['value']);
                }
            }

            $image = Mage::helper('productlabel')->uploadImage('image');



            if ($image || (isset($labelData['image']['delete']) && $labelData['image']['delete'])) {
                $labelData['image'] = $image;
            } else {
                unset($labelData['image']);
            }

            if (isset($labelData['category_image']['delete']) && ($labelData['category_image']['value'] != $labelData['image']['value'])) {
                Mage::helper('productlabel')->deleteImageFile($labelData['category_image']['value']);
            }

            $image = Mage::helper('productlabel')->uploadImage('category_image');



            if ($image || (isset($data['category_image']['delete']) && $labelData['category_image']['delete'])) {
                $labelData['category_image'] = $image;
            } else {
                unset($labelData['category_image']);
            }
            
            // auto copy data from product label on category page
            if ($labelData['same_on_two_page']) {
                $labelData['category_display'] = isset($labelData['display']) ? $labelData['display'] : '';
                $labelData['category_position'] = isset($labelData['position']) ? $labelData['position'] : '';
                $labelData['category_text'] = isset($labelData['text']) ? $labelData['text'] : '';
                $labelData['category_image'] = isset($labelData['image']) ? $labelData['image'] : '';
            }
            else
                $labelData['same_on_two_page'] = 0;

            $model = Mage::getModel('productlabel/productlabelentity')->load($product->getId(), 'product_id');
            $labelData['product_id'] = $product->getId();
            $model->addData($labelData)->setId($model->getId());
            $model->setStoreId($store);

            try {
                $model->save();
                $labels = Mage::getModel('productlabel/productlabel')->setStoreId(0)->getCollection()
                        ->addFieldToFilter('is_apply', 1);

                if ($labels->getData()) {
                    foreach ($labels as $label) {
                        if ($label->getStatus() == 1) {
                            switch ($label->getConditionSelected()) {
                                case 'onsale':$this->validateOnSaleProduct($product, $label);
                                    break;
                                case 'newproduct':$this->validateNewProduct($product, $label);
                                    break;
                                case 'custom':$this->validateProduct($product, $label);
                                    break;
                            }
                        }
                    }
                }


                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productlabel')->__('Unable to find product label to save'));
    }

    public function validateProduct($product, $label) {
        $label_data = Mage::getModel('productlabel/productlabelflatdata')->getCollection()->addFieldToFilter('product_id', $product->getId())
                ->addFieldToFilter('label_id', $label->getId());
        if ($label->getConditions()->validate($product)) {

            if (!$label_data->getData()) {
                foreach (Mage::app()->getStores() as $store) {
                    $model = Mage::getModel('productlabel/productlabelflatdata');
                    $model->setLabelId($label->getId());
                    $model->setProductId($product->getId());
                    $model->setFromTime(strtotime($label->getFromDate()));
                    $model->setToTime(strtotime($label->getToDate()));
                    $model->setText(Mage::helper('productlabel')->convertBackendString($label->getText(), $product));
                    $model->setImage($label->getImage());
                    $model->setPosition($label->getPosition());
                    $model->setDisplay($label->getDisplay());
                    $model->setCategoryText(Mage::helper('productlabel')->convertBackendString($label->getCategoryText(), $product));
                    $model->setCategoryImage($label->getCategoryImage());
                    $model->setCategoryPosition($label->getCategoryPosition());
                    $model->setCategoryDisplay($label->getCategoryDisplay());
                    $model->setStoreId($store->getId());
                    try {
                        $model->save();
                    } catch (Exception $exc) {
                        echo $exc->getMessage();
                    }
                }
            } else {
                foreach ($label_data as $l) {
                    $model = Mage::getModel('productlabel/productlabelflatdata');
                    $fromtime = $label->getFromTime();
                    $totime = $label->getTotime();
                    $model->setFromTime(strtotime($fromtime[$product->getId()]));
                    $model->setToTime(strtotime($totime[$product->getId()]));
                    $model->setText(Mage::helper('productlabel')->convertBackendString($label->getText(), $product));
                    $model->setImage($label->getImage());
                    $model->setPosition($label->getPosition());
                    $model->setDisplay($label->getDisplay());
                    $model->setCategoryText(Mage::helper('productlabel')->convertBackendString($label->getCategoryText(), $product));
                    $model->setCategoryImage($label->getCategoryImage());
                    $model->setCategoryPosition($label->getCategoryPosition());
                    $model->setCategoryDisplay($label->getCategoryDisplay());
                    $model->setId($l->getId());
                    try {
                        $model->save();
                    } catch (Exception $exc) {
                        echo $exc->getMessage();
                    }
                }
            }
        } else {
            if ($label_data->getData()) {
                foreach ($label_data as $l) {
                    Mage::getModel('productlabel/productlabelflatdata')->setId($l->getId())->delete();
                }
            }
        }
    }

    public function validateOnSaleProduct($product, $label) {
        $label_data = Mage::getModel('productlabel/productlabelflatdata')->getCollection()->addFieldToFilter('product_id', $product->getId())
                ->addFieldToFilter('label_id', $label->getId());

        if ($label->validateOnsaleProduct($product)) {

            if (!$label_data->getData()) {
                foreach (Mage::app()->getStores() as $store) {
                    $model = Mage::getModel('productlabel/productlabelflatdata');

                    $model->setLabelId($label->getId());
                    $model->setProductId($product->getId());
                    $fromtime = $label->getFromTime();
                    $totime = $label->getTotime();
                    $model->setFromTime(strtotime($fromtime[$product->getId()]));
                    $model->setToTime(strtotime($totime[$product->getId()]));
                    $model->setText(Mage::helper('productlabel')->convertBackendString($label->getText(), $product));
                    $model->setImage($label->getImage());
                    $model->setPosition($label->getPosition());
                    $model->setDisplay($label->getDisplay());
                    $model->setCategoryText(Mage::helper('productlabel')->convertBackendString($label->getCategoryText(), $product));
                    $model->setCategoryImage($label->getCategoryImage());
                    $model->setCategoryPosition($label->getCategoryPosition());
                    $model->setCategoryDisplay($label->getCategoryDisplay());
                    $model->setStoreId($store->getId());
                    try {
                        $model->save();
                    } catch (Exception $exc) {
                        echo $exc->getMessage();
                    }
                }
            } else {
                foreach ($label_data as $l) {
                    $model = Mage::getModel('productlabel/productlabelflatdata');
                    $fromtime = $label->getFromTime();
                    $totime = $label->getTotime();
                    $model->setFromTime(strtotime($fromtime[$product->getId()]));
                    $model->setToTime(strtotime($totime[$product->getId()]));
                    $model->setText(Mage::helper('productlabel')->convertBackendString($label->getText(), $product));
                    $model->setImage($label->getImage());
                    $model->setPosition($label->getPosition());
                    $model->setDisplay($label->getDisplay());
                    $model->setCategoryText(Mage::helper('productlabel')->convertBackendString($label->getCategoryText(), $product));
                    $model->setCategoryImage($label->getCategoryImage());
                    $model->setCategoryPosition($label->getCategoryPosition());
                    $model->setCategoryDisplay($label->getCategoryDisplay());
                    $model->setId($l->getId());
                    try {
                        $model->save();
                    } catch (Exception $exc) {
                        echo $exc->getMessage();
                    }
                }
            }
        } else {
            if ($label_data->getData()) {
                foreach ($label_data as $l) {
                    Mage::getModel('productlabel/productlabelflatdata')->setId($l->getId())->delete();
                }
            }
        }
    }

    public function validateNewProduct($product, $label) {
        $label_data = Mage::getModel('productlabel/productlabelflatdata')->getCollection()->addFieldToFilter('product_id', $product->getId())
                ->addFieldToFilter('label_id', $label->getId());

        if ($label->validateNewProduct($product)) {

            if (!$label_data->getData()) {
                foreach (Mage::app()->getStores() as $store) {
                    $model = Mage::getModel('productlabel/productlabelflatdata');

                    $model->setLabelId($label->getId());
                    $model->setProductId($product->getId());
                    $fromtime = $label->getFromTime();
                    $totime = $label->getTotime();
                    $model->setFromTime(strtotime($fromtime[$product->getId()]));
                    $model->setToTime(strtotime($totime[$product->getId()]));
                    $model->setText(Mage::helper('productlabel')->convertBackendString($label->getText()));
                    $model->setImage($label->getImage());
                    $model->setPosition($label->getPosition());
                    $model->setDisplay($label->getDisplay());
                    $model->setCategoryText(Mage::helper('productlabel')->convertBackendString($label->getCategoryText()));
                    $model->setCategoryImage($label->getCategoryImage());
                    $model->setCategoryPosition($label->getCategoryPosition());
                    $model->setCategoryDisplay($label->getCategoryDisplay());
                    $model->setStoreId($store->getId());
                    try {
                        $model->save();
                    } catch (Exception $exc) {
                        echo $exc->getMessage();
                    }
                }
            } else {
                foreach ($label_data as $l) {
                    $model = Mage::getModel('productlabel/productlabelflatdata');
                    $fromtime = $label->getFromTime();
                    $totime = $label->getTotime();
                    $model->setFromTime(strtotime($fromtime[$product->getId()]));
                    $model->setToTime(strtotime($totime[$product->getId()]));
                    $model->setText(Mage::helper('productlabel')->convertBackendString($label->getText(), $product));
                    $model->setImage($label->getImage());
                    $model->setPosition($label->getPosition());
                    $model->setDisplay($label->getDisplay());
                    $model->setCategoryText(Mage::helper('productlabel')->convertBackendString($label->getCategoryText(), $product));
                    $model->setCategoryImage($label->getCategoryImage());
                    $model->setCategoryPosition($label->getCategoryPosition());
                    $model->setCategoryDisplay($label->getCategoryDisplay());
                    $model->setId($l->getId());
                    try {
                        $model->save();
                    } catch (Exception $exc) {
                        echo $exc->getMessage();
                    }
                }
            }
        } else {
            if ($label_data->getData()) {
                foreach ($label_data as $l) {
                    Mage::getModel('productlabel/productlabelflatdata')->setId($l->getId())->delete();
                }
            }
        }
    }

}
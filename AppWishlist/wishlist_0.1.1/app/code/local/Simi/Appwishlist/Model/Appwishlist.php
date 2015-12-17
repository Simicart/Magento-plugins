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
 * @package     Magestore_Appwishlist
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Appwishlist Appwishlist Model
 * 
 * @category    Magestore
 * @package     Magestore_Appwishlist
 * @author      Magestore Developer
 * 
 */
class Simi_Appwishlist_Model_Appwishlist extends Simi_Connector_Model_Catalog_Product {

    /**
     * Config key 'Display Wishlist Summary'
     */
    const XML_PATH_WISHLIST_LINK_USE_QTY = 'wishlist/wishlist_link/use_qty';

    function _getWishlistFromCustomer($customer = null) {
        if (!$customer)
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer->getId() && ($customer->getId() != '')) {
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
            return $wishlist;
        } else
            return null;
    }

    protected function _initProduct($productId) {
        $storeId = Mage::app()->getStore()->getId();
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId($storeId)
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    /**
     * Get wishlist
     *
     */
    public function getWishlistProducts($data) {

        $limit = $data->limit;
        if (!$limit)
            $limit = 10;
        $offset = $data->offset;
        if (!$offset)
            $offset = 0;
        $width = $data->width;
        $height = $data->height;
        $sort_option = $data->sort_option;

        $customerId = $this->_getSession()->getCustomer()->getId();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customerId && ($customerId != '')) {
            $wishlist = $this->_getWishlistFromCustomer($customer);
            $wishListItemCollection = $wishlist->getItemCollection();
            $sharingUrl = $wishlist->getSharingCode();
            $sharingUrl = Mage::getStoreConfig('appwishlist/general/sharing_message') . ' ' . Mage::getUrl('wishlist/shared/index/code/' . $sharingUrl);
            if ($wishlist->getShared() == '0') {
                $wishlist->setShared('1');
                try {
                    $wishlist->save();
                } catch (Exception $e) {
                    
                }
            }

            $sort = $this->_helperCatalog()->getSortOption($sort_option);
            if ($sort) {
                $wishListItemCollection->setOrder($sort[0], $sort[1]);
            }
            $information = $this->getWishlistProductFromItems($wishListItemCollection, $offset, $limit, $width, $height);

            $count = $wishListItemCollection->getSize();

            $wishlistInfo = array('wishlist_items_qty' => $count, 'sharing_message' => $sharingUrl
                , 'cart_qty' => Mage::helper('checkout/cart')->getSummaryCount(),
                'sharing_url' => Mage::getUrl('wishlist/shared/index/code/' . $wishlist->getSharingCode()),
            );
            $information['wishlist_info'] = array($wishlistInfo);
            $information['other'] = array($wishlistInfo);
            return $information;
        } else {
            $information = $this->statusError(array('You have not logged in'));
            return $information;
        }
    }

    /**
     * Remove Item from wishlist
     *
     */
    public function removeProductFromWishlist($data) {
        $result = false;
        $id = $data->wishlist_item_id;
        $item = Mage::getModel('wishlist/item')->load($id);
        $wishlist = $this->_getWishlistFromCustomer();
        if ($item->getId()) {
            if ($wishlist) {
                $result = true;
                try {
                    $item->delete();
                    $wishlist->save();
                } catch (Mage_Core_Exception $e) {
                    $result = false;
                } catch (Exception $e) {
                    $result = false;
                }
                Mage::helper('wishlist')->calculate();
            }
        }
        $collection = $wishlist->getItemCollection();
        $count = 0;
        if (Mage::getStoreConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY)) {
            $count = $collection->getItemsQty();
        } else {
            $count = $collection->getSize();
        }
        $sharingUrl = $wishlist->getSharingCode();
        $sharingUrl = Mage::getStoreConfig('appwishlist/general/sharing_message') . ' ' . Mage::getUrl('wishlist/shared/index/code/' . $sharingUrl);
        $data = array('wishlist_items_qty' => $count, 'sharing_message' => $sharingUrl
            , 'cart_qty' => Mage::helper('checkout/cart')->getSummaryCount(),
            'sharing_url' => Mage::getUrl('wishlist/shared/index/code/' . $wishlist->getSharingCode()),
        );
        $information = $this->getWishlistProducts($data);


        if ($result) {
            $information['status'] = 'SUCCESS';
            $information['wishlist_info'] = array($data);
            $information['other'] = array($data);
            return $information;
        }
        $information['status'] = 'FAIL';
        $information = $this->statusError(array('Remove Product Failed'));
        $information['wishlist_info'] = array($data);
        $information['other'] = array($data);
        return $information;
    }

    /**
     * Add item to wishlist
     *
     */
    public function addProductToWishlist($data) {
        $result = false;
        $productId = $data->product_id;
        $customerId = $this->_getSession()->getCustomer()->getId();
        if (($productId != null) && ($productId != '')) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $product = Mage::getModel('catalog/product')->load($productId);
            if ($product && ($product->getId())) {
                $optionsByProduct = $product->getOptions();
                if ($customerId && ($customerId != '')) {

                    $requestParams = array();
                    $requestParams['product'] = $productId;
                    $productType = $product->getTypeID();

                    $product = $this->_initProduct($data->product_id);
                    $information = $this->statusSuccess();
                    if (!$product) {
                        $information = $this->statusError();
                        return $information;
                    }

					$groupProductValid = true;
                    if ($productType == 'grouped'){                        
                     $groupProductValid = false;
                        foreach ($data->options as $index => $option) {
                            if ($option->option_qty >= 1)
                                $groupProductValid = true;
                        }
                    }
                    
                    if ($groupProductValid) {
                        $params = Mage::helper('connector/catalog')->getCartParams($data, $product->getTypeId());                        
                    }
                    else {
                        $information = $this->statusError(array('Please specify the quantity of product(s).'));
                        return $information;
                    }


                    $wishlist = $this->_getWishlistFromCustomer($customer);
                    $item = $wishlist->addNewItem($product, $params);
                    try {
                        $result = $wishlist->save();
                    } catch (Exception $e) {
                        $result = false;
                    }
                }
                if ($result) {
                    
                    $collection = $wishlist->getItemCollection();
                    $count = 0;
                    if (Mage::getStoreConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY)) {
                        $count = $collection->getItemsQty();
                    } else {
                        $count = $collection->getSize();
                    }

                    $sharingUrl = $wishlist->getSharingCode();
                    $sharingUrl = Mage::getStoreConfig('appwishlist/general/sharing_message') . ' ' . Mage::getUrl('wishlist/shared/index/code/' . $sharingUrl);
                    $data = array('wishlist_items_qty' => $count, 'sharing_message' => $sharingUrl, 'wishlist_item_id' => $item->getWishlistItemId()
                        , 'cart_qty' => Mage::helper('checkout/cart')->getSummaryCount(),
                        'sharing_url' => Mage::getUrl('wishlist/shared/index/code/' . $wishlist->getSharingCode()),
                    );


                    $information = $this->getWishlistProducts($data);
                    $information['status'] = 'SUCCESS';
                    $information['wishlist_info'] = array($data);
                    $information['other'] = array($data);
                    return $information;
                }
            }
        }
        $information = $this->statusError(array('Add Product Failed'));
        return $information;
    }

    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     */
    public function addWishlistProductToCart($data) {
        $result = false;
        $wishlist = $this->_getWishlistFromCustomer();
        $itemId = $data->wishlist_item_id;
        if ($itemId) {
            foreach ($wishlist->getItemCollection() as $wishlistItem) {
                if ($wishlistItem->getData('wishlist_item_id') == $itemId)
                    $item = $wishlistItem;
            }
            if ($item && ($this->_checkIfSelectedAllRequiredOptions($item))) {
                $product = $item->getProduct();
                $isSaleAble = $product->isSaleable();
                if ($isSaleAble) {

                    $item = Mage::getModel('wishlist/item')->load($itemId);
                    $item->setQty('1');
                    $session = Mage::getSingleton('wishlist/session');
                    $cart = Mage::getSingleton('checkout/cart');

                    try {
                        $options = Mage::getModel('wishlist/item_option')->getCollection()
                            ->addItemFilter(array($itemId));
                        $item->setOptions($options->getOptionsByItem($itemId));

                        $item->mergeBuyRequest($buyRequest);
                        if ($item->addToCart($cart, true)) {
                            $cart->save()->getQuote()->collectTotals();
                        }
                        $wishlist->save();
                        Mage::helper('wishlist')->calculate();
                        $result = true;
                    } catch (Mage_Core_Exception $e) {
                        
                    } catch (Exception $e) {
                        
                    }
                }
            }
        }
        $information = $this->getWishlistProducts($data);

        if ($result) {
            $information['status'] = 'SUCCESS';
            return $information;
        }
        $information['status'] = 'FAIL';
        $information = $this->statusError(array('Add Product to Cart Failed'));
        return $information;
    }

    public function getWishlistQty() {
        $wishlist = $this->_getWishlistFromCustomer();
        if ($wishlist)
            $collection = $wishlist->getItemCollection();
        if ($collection) {
            $count = $collection->getSize();
            $data = array('wishlist_items_qty' => $count);
            $information = $this->statusSuccess();
            $information['wishlist_info'] = array($data);
            $information['other'] = array($data);
            return $information;
        }
        $information = $this->statusError(array('Cannot Get Qty'));
        return $information;
    }

    public function getWishlistProductFromItems($wishListItemCollection, $offset, $limit, $width, $height) {
        $productList = array();
        $wishListItemCollection->setPageSize($offset + $limit);
        $product_total = $wishListItemCollection->getSize();

        if ($offset > $product_total)
            return $this->statusError(array('No information'));
        $check_limit = 0;
        $check_offset = 0;
        foreach ($wishListItemCollection as $item) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;

            $product = $item->getProduct();


            $options = $this->_getOptionsSelectedFromItem($item, $product);
            $ratings = Mage::getModel('connector/review')->getRatingStar($product->getId());
            $total_rating = $this->getHelper()->getTotalRate($ratings);
            $avg = $this->getHelper()->getAvgRate($ratings, $total_rating);
            $prices = $this->getOptionModel()->getPriceModel($product);
            $manufacturer_name = "";
            try {
                $manufacturer_name = $product->getAttributeText('manufacturer') == false ? '' : $product->getAttributeText('manufacturer');
            } catch (Exception $e) {
                
            }
            $isSaleAble = $product->isSaleable();
            if ($isSaleAble) {
                $itemOptions = Mage::getModel('wishlist/item_option')->getCollection()
                    ->addItemFilter(array($item->getData('wishlist_item_id')));
                foreach ($itemOptions as $itemOption) {
                    $optionProduct = Mage::getModel('catalog/product')->load($itemOption->getProductId());
                    if (!$optionProduct->isSaleable()) {
                        $isSaleAble = false;
                        break;
                    }
                }
            }

            $productSharingMessage = Mage::getStoreConfig('appwishlist/general/product_sharing_message') . ' ' . $product->getProductUrl();

            $info_product = array(
                'product_id' => $product->getId(),
                'product_name' => $product->getName(),
                'product_type' => $product->getTypeId(),
                'product_regular_price' => Mage::app()->getStore()->convertPrice($product->getPrice(), false),
                'product_price' => Mage::app()->getStore()->convertPrice($product->getFinalPrice(), false),
                'product_rate' => $avg,
                'stock_status' => $isSaleAble,
                'product_review_number' => $ratings[5],
                'product_image' => $this->getImageProduct($product, null, $width, $height),
                'manufacturer_name' => $manufacturer_name,
                'is_show_price' => true,
                'wishlist_item_id' => $item->getWishlistItemId(),
                'options' => $options,
                'selected_all_required_options' => $this->_checkIfSelectedAllRequiredOptions($item, $options),
                'product_qty' => $item->getQty(),
                'product_sharing_message' => $productSharingMessage,
                'product_sharing_url' => $product->getProductUrl(),
            );
            if ($prices) {
                $info_product = array_merge($info_product, $prices);
            }
            Mage::helper("connector/tax")->getProductTax($product, $info_product);

            $event_name = $this->getControllerName() . '_product_detail';
            $event_value = array(
                'object' => $this,
                'product' => $product
            );
            $data_change = $this->changeData($info_product, $event_name, $event_value);
            $productList[] = $data_change;
        }
        $information = '';
        $information = $this->statusSuccess();
        $information['message'] = array($product_total);
        $information['data'] = $productList;

        return $information;
    }

    function _checkIfSelectedAllRequiredOptions($item, $options = null) {
        $selected = true;
        $product = $item->getProduct();

        $itemOptions = Mage::getModel('wishlist/item_option')->getCollection()
            ->addItemFilter(array($item->getData('wishlist_item_id')));
        if (!$options)
            $options = $this->_getOptionsSelectedFromItem($item, $product);
        $productObjData = new Varien_Object();
        $productObjData->product_id = $product->getData('entity_id');
        $product_information = Mage::getModel('connector/catalog_product')->getDetail($productObjData);
        $product_options = $product_information['data'][0]['options'];

        foreach ($product_options as $product_option) {
            if ($product_option['is_required'] == 'YES') {
                $selected = false;
                foreach ($options as $option) {
                    if (($option['option_title'] == $product_option['option_title']) && ($option['option_value']) && ($option['option_value'] != ''))
                        $selected = true;
                }
            }
        }
        return $selected;
    }

    function _getOptionsSelectedFromItem($item, $product) {
        // cloning the core from there, update it after updating core
        $options = array();
        if (version_compare(Mage::getVersion(), '1.5.0.0', '>=') === true) {
            $helper = Mage::helper('catalog/product_configuration');
            if ($product->getTypeId() == "simple") {
                $options = Mage::helper('connector/checkout')->convertOptionsCart($helper->getCustomOptions($item));
            } elseif ($product->getTypeId() == "configurable") {
                $options = Mage::helper('connector/checkout')->convertOptionsCart($helper->getConfigurableOptions($item));
            } elseif ($product->getTypeId() == "bundle") {
                $options = Mage::helper('connector/checkout')->getOptions($item);
            }
        } else {
            if ($product->getTypeId() != "bundle") {
                $options = Mage::helper('connector/checkout')->getUsedProductOption($item);
            } else {
                $options = Mage::helper('connector/checkout')->getOptions($item);
            }
        }
        return $options;
    }

}

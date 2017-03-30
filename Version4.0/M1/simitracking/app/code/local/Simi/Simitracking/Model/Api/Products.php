<?php

class Simi_Simitracking_Model_Api_Products extends Simi_Simiconnector_Model_Api_Products {

    protected $_stockitem;


    public function setBuilderQuery() {
        $data = $this->getData();
        $this->_helperProduct = Mage::helper('simiconnector/products');
        $this->_helperProduct->setData($data);
        if ($data['resourceid']) {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::PRODUCT_DETAIL);
            $this->builderQuery = Mage::getModel('catalog/product')->load($data['resourceid']);
            
            $dataToSave = $data['contents'];
            $storeId = false;
            if (isset($dataToSave->store_id)) {
                $storeId = $dataToSave->store_id;
            } else if (isset($data['params']) && isset($data['params']['store_id'])) {
                $storeId = $data['params']['store_id'];
            }
            if ($storeId != false) {
                $this->builderQuery->setStoreId($storeId);
                Mage::app()->setCurrentStore($storeId);
            } else 
                $this->builderQuery->setStoreId(0);
        } else {
            $this->builderQuery = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')
                    ->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
            if (isset($data['params']['store_id']) && $data['params']['store_id'] != '0' && $data['params']['store_id'] != '') {
                $this->builderQuery->setStoreId($data['params']['store_id']);
            }
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::PRODUCT_LIST);
        }
    }

    /*
     * Update Product
     */

    public function update() {
        Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::PRODUCT_EDIT);
        $data = $this->getData();
        $dataToSave = $data['contents'];
        $product = $this->builderQuery;

        //product update
        if ($dataToSave->name) {
            $product->setData('name', $dataToSave->name);
        }
        if ($dataToSave->description) {
            $product->setData('description', $dataToSave->description);
        }
        if ($dataToSave->short_description) {
            $product->setData('short_description', $dataToSave->short_description);
        }
        $product->save();
        //stock item
        if (isset($dataToSave->is_in_stock) || isset($dataToSave->qty)) {
            $this->_stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
            if (isset($dataToSave->qty)) {
                $this->_stockItem->setData('qty', $dataToSave->qty);
            }
            if (isset($dataToSave->is_in_stock)) {
                $this->_stockItem->setData('is_in_stock', $dataToSave->is_in_stock);
            }
            $this->_stockItem->save();
        }

        $this->builderQuery = $product;
        $this->_RETURN_MESSAGE = Mage::helper('customer')->__('The product has been updated.');
        return $this->show();
    }

    /*
     * Listing Products
     */

    public function index() {
        $collection = $this->builderQuery;
        $this->filter();
        $data = $this->getData();
        $parameters = $data['params'];
        $page = 1;
        if (isset($parameters[self::PAGE]) && $parameters[self::PAGE]) {
            $page = $parameters[self::PAGE];
        }

        $limit = self::DEFAULT_LIMIT;
        if (isset($parameters[self::LIMIT]) && $parameters[self::LIMIT]) {
            $limit = $parameters[self::LIMIT];
        }

        $offset = $limit * ($page - 1);
        if (isset($parameters[self::OFFSET]) && $parameters[self::OFFSET]) {
            $offset = $parameters[self::OFFSET];
        }
        $collection->setPageSize($offset + $limit);

        $all_ids = array();
        $info = array();
        $total = $collection->getSize();

        if ($offset > $total)
            throw new Exception($this->_helper->__('Invalid method.'), 4);

        $fields = array();
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }

        $check_limit = 0;
        $check_offset = 0;

        foreach ($collection as $entity) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;
            $info_detail = $entity->toArray($fields);
            $all_ids[] = $entity->getId();
            $imagelink = Mage::helper('simiconnector/products')->getImageProduct($entity, null, $parameters['image_width'], $parameters['image_height']);

            $info_detail['images'] = array(array(
                    'url' => $imagelink,
                    'position' => 1,
            ));
            $info[] = $info_detail;
        }
        return $this->getList($info, $all_ids, $total, $limit, $offset);
    }

    /**
     * Show Detail
     * override
     */
    public function show() {
        $entity = $this->builderQuery;
        $data = $this->getData();
        $parameters = $data['params'];
        $fields = array();
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        $info = $entity->toArray($fields);
        $product = Mage::getModel('catalog/product')->load($entity->getId());
        $media_gallery = $product->getMediaGallery();
        $images = array();

        foreach ($media_gallery['images'] as $image) {
            if ($image['disabled'] == 0) {
                $imagelink = $this->_helperProduct->getImageProduct($entity, $image['file'], $parameters['image_width'], $parameters['image_height']);
                $images[] = array(
                    'url' => $imagelink,
                    'position' => $image['position'],
                );
            }
        }
        if (count($images) == 0) {
            $imagelink = $this->_helperProduct->getImageProduct($entity, null, $parameters['image_width'], $parameters['image_height']);
            $images[] = array(
                'url' => $imagelink,
                'position' => 1,
            );
        }
        if (!Mage::registry('product') && $entity->getId()) {
            Mage::register('product', $entity);
        }

        $block_att = Mage::getBlockSingleton('catalog/product_view_attributes');
        $_additional = $block_att->getAdditionalData();

        $ratings = Mage::helper('simiconnector/review')->getRatingStar($entity->getId());
        $total_rating = Mage::helper('simiconnector/review')->getTotalRate($ratings);
        $avg = Mage::helper('simiconnector/review')->getAvgRate($ratings, $total_rating);

        $info['additional'] = $_additional;
        $info['images'] = $images;
        $info['app_prices'] = Mage::helper('simiconnector/price')->formatPriceFromProduct($entity, true);
        $info['app_tier_prices'] = Mage::helper('simiconnector/tierprice')->formatTierPrice($entity);
        $info['app_reviews'] = array(
            'rate' => $avg,
            'number' => $ratings[5],
            '5_star_number' => $ratings[4],
            '4_star_number' => $ratings[3],
            '3_star_number' => $ratings[2],
            '2_star_number' => $ratings[1],
            '1_star_number' => $ratings[0],
            'form_add_reviews' => Mage::helper('simiconnector/review')->getReviewToAdd(),
        );
        if ($this->_stockItem) {
            $info['stock_item'] = $this->_stockItem->getData();
        }
        $this->detail_info = $this->getDetail($info);
        return $this->detail_info;
    }

}

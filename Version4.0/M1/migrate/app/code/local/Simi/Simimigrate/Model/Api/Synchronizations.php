<?php

class Simi_Simimigrate_Model_Api_Synchronizations extends Simi_Simimigrate_Model_Api_Abstract {
    
    
    public function setBuilderQuery() {
        $data = $this->getData();
        if (!$data['resourceid']) {
            
        } else {
            
        }
    }
    
    public function show() {
        $data = $this->getData();
        if ($data['resourceid'] == 'magento') {
            $params = $data['params'];
            return ['synchronization'=> $this->synchronizeMagento($params)];
        }
    }
    
    public function synchronizeMagento($data) {
        $result = array();
        $userEmail = $data['user_email'];
        $websiteUrl = $data['website_url'];
        $simicartAppConfigId = $data['simicart_app_config_id'];
        $simicartCustomerId = $data['simicart_customer_id'];
        
        $url = $websiteUrl . '/simiconnector/rest/v2/migrate_packages/all';
        $json = @file_get_contents($url);
        $data = json_decode($json, 1);

        /*
         * Update app info
         */
        $appModel = Mage::getModel('simimigrate/app')
                ->getCollection()
                ->addFieldToFilter('simicart_app_config_id',$simicartAppConfigId)
                ->getFirstItem();
        $result['new_account'] = false;
        if (!$appModel->getId()) {
            $appModel->setData('simicart_app_config_id', $simicartAppConfigId);
            $result['new_account'] = true;
        }
        $appModel->setData('user_email', $userEmail);
        $appModel->setData('website_url', $websiteUrl);
        $appModel->setData('simicart_customer_id', $simicartCustomerId);
        $appModel->save();
        $appId = $appModel->getId();
        
        /*
         * Clean existing data
         */
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');

        $query = "";
        $storeViewTable = $resource->getTableName('simimigrate/storeview');
        $query .= "DELETE FROM " . $storeViewTable . " WHERE app_id=" . $appId . ";";

        $storeTable = $resource->getTableName('simimigrate/store');
        $query .= "DELETE FROM " . $storeTable . " WHERE app_id=" . $appId . ";";

        $categoryTable = $resource->getTableName('simimigrate/category');
        $query .= "DELETE FROM " . $categoryTable . " WHERE app_id=" . $appId . ";";

        $productTable = $resource->getTableName('simimigrate/product');
        $query .= "DELETE FROM " . $productTable . " WHERE app_id=" . $appId . ";";

        $customerTable = $resource->getTableName('simimigrate/customer');
        $query .= "DELETE FROM " . $customerTable . " WHERE app_id=" . $appId . ";";

        $writeConnection->query($query);
        /*
         * Add data
         */
        $queryInsert = "";
        //storeviews
        
        if (isset($data['migrate_package']['storeviews']['migrate_storeviews'])) {
            $storeViews = $data["migrate_package"]["storeviews"]['migrate_storeviews'];
            foreach ($storeViews as $storeView) {
                $newRecord = "INSERT INTO " . $storeViewTable
                        . "(app_id, storeview_id, group_id, website_id, code, name, sort_order, is_active) "
                        . "VALUES ('" .
                        $appId . "', '" .
                        $this->checkString($storeView['store_id']) . "', '" .
                        $this->checkString($storeView['group_id']) . "', '" .
                        $this->checkString($storeView['website_id']) . "', '" .
                        $this->checkString($storeView['code']) . "', '" .
                        $this->checkString($storeView['name']) . "', '" .
                        $this->checkString($storeView['sort_order']) . "', '" .
                        $this->checkString($storeView['is_active']) . "'); ";
                $queryInsert .= $newRecord;
            }
            $result['storeviews'] = count($storeViews);
        }

        //stores
        if (isset($data['migrate_package']['stores']['migrate_stores'])) {
            $stores = $data["migrate_package"]["stores"]['migrate_stores'];
            foreach ($stores as $store) {
                $newRecord = "INSERT INTO " . $storeTable
                        . "(app_id, group_id, website_id, root_category_id, default_store_id, name) "
                        . "VALUES ('" .
                        $appId . "', '" .
                        $this->checkString($store['group_id']) . "', '" .
                        $this->checkString($store['website_id']) . "', '" .
                        $this->checkString($store['root_category_id']) . "', '" .
                        $this->checkString($store['default_store_id']) . "', '" .
                        $this->checkString($store['name']) . "'); ";
                $queryInsert .= $newRecord;
            }
            $result['stores'] = count($stores);
        }

        //categories
        if (isset($data['migrate_package']['categories']['migrate_categories'])) {
            $categories = $data["migrate_package"]["categories"]['migrate_categories'];
            foreach ($categories as $category) {
                $newRecord = "INSERT INTO " . $categoryTable
                        . "(app_id, category_id, parent_id, path, position, level, children_count, name, url_path) "
                        . "VALUES ('" .
                        $appId . "', '" .
                        $this->checkString($category['entity_id']) . "', '" .
                        $this->checkString($category['parent_id']) . "', '" .
                        $this->checkString($category['path']) . "', '" .
                        $this->checkString($category['position']) . "', '" .
                        $this->checkString($category['level']) . "', '" .
                        $this->checkString($category['children_count']) . "', '" .
                        $this->checkString($category['name']) . "', '" .
                        $this->checkString($category['url_path']) . "'); ";
                $queryInsert .= $newRecord;
            }
            $result['categories'] = count($categories);
        }

        //products
        if (isset($data['migrate_package']['products']['migrate_products'])) {
            $products = $data["migrate_package"]["products"]['migrate_products'];

            foreach ($products as $product) {
                $newRecord = "INSERT INTO " . $productTable
                        . "(app_id, product_id, sku, has_options, required_options, name, is_salable) "
                        . "VALUES ('" .
                        $appId . "', '" .
                        $this->checkString($product['entity_id']) . "', '" .
                        $this->checkString($product['sku']) . "', '" .
                        $this->checkString($product['has_options']) . "', '" .
                        $this->checkString($product['required_options']) . "', '" .
                        $this->checkString($product['name']) . "', '" .
                        $this->checkString($product['is_salable']) . "'); ";
                $queryInsert .= $newRecord;
            }
            $result['products'] = count($products);
        }
        //customers
        if (isset($data['migrate_package']['customers']['migrate_customers'])) {
            $customers = $data["migrate_package"]["customers"]['migrate_customers'];
            foreach ($customers as $customer) {
                $newRecord = "INSERT INTO " . $customerTable
                        . "(app_id, customer_id, website_id, group_id, email) "
                        . "VALUES ('" .
                        $appId . "', '" .
                        $customer['entity_id'] . "', '" .
                        $customer['website_id'] . "', '" .
                        $customer['group_id'] . "', '" .
                        $customer['email'] . "'); ";
                $queryInsert .= $newRecord;
            }
            $result['customers'] = count($customers);
        }
        if ($queryInsert) {
            $writeConnection->query($queryInsert);
        }
        return $result;
    }
    
    public function checkString($input)
    {
        $input = addslashes($input);
        $input = strip_tags($input);
        return $input;
    }
}

<?php

class Simi_Simitracking_Model_Api_Bestsellers extends Simi_Simiconnector_Model_Api_Addresses {

    public function setBuilderQuery() {
        $data = $this->getData();
        $this->_helperProduct = Mage::helper('simiconnector/products');
        $this->_helperProduct->setData($data);
        Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::PRODUCT_LIST);
        $collection = Mage::getResourceModel('sales/report_bestsellers_collection')
            ->setModel('catalog/product')
            ;

        $this->builderQuery  = Mage::getResourceModel('sales/report_bestsellers_collection')
            ->setModel('catalog/product');
        if (isset($data['params']['store_id']) && $data['params']['store_id'] != '0' && $data['params']['store_id'] != '') {
            $this->builderQuery->addStoreFilter($data['params']['store_id']);
        }
    }

    public function index() {
        $collection = $this->builderQuery;
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

        $all_ids = array();
        $info = array();
        $total = count($collection);

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

            $product_report = array(
                'ordered_qty'=> $entity->getData('qty_ordered'),
                'name'=> $entity->getData('product_name')
            );
            if (!$entity->getData('product_name')) {
                $id = $entity->getData('product_id');
                $entity = Mage::getModel('catalog/product')->load($id);
                if(!$entity->getId())
                    throw new Exception($this->_helper->__('Please update your Admin product report'), 4);
            }
            $info_detail = array_merge($product_report, $entity->getData());
            $info_detail['entity_id'] = $entity->getData('product_id')?$entity->getData('product_id'):$entity->getId();
            $all_ids[] = $info_detail['entity_id'];            
            $info[] = $info_detail;
        }
        return $this->getList($info, $all_ids, $total, $limit, $offset);
    }

}

<?php

class Simi_Simitracking_Model_Api_Bestsellers extends Simi_Simiconnector_Model_Api_Addresses {

    public function setBuilderQuery() {
        $data = $this->getData();
        $this->_helperProduct = Mage::helper('simiconnector/products');
        $this->_helperProduct->setData($data);

        Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::PRODUCT_LIST);
        $this->builderQuery = $collection = Mage::getResourceModel('reports/product_collection')
                        ->addAttributeToSelect('*')
                        ->addOrderedQty()->addMinimalPrice()
                        ->addTaxPercents()
                        ->addStoreFilter()
                        ->setOrder('ordered_qty', 'desc');
        if (isset($data['params']['store_id']) && $data['params']['store_id'] != '0' && $data['params']['store_id'] != '') {
            $this->builderQuery->addStoreFilter($data['params']['store_id']);
        }
    }

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
            
            if (!$entity->getName()) {
                $entity = Mage::getModel('catalog/product')->load($entity->getId());
            }
            $info_detail = $entity->toArray($fields);
            $all_ids[] = $entity->getId();            
            $info[] = $info_detail;
        }
        return $this->getList($info, $all_ids, $total, $limit, $offset);
    }

}

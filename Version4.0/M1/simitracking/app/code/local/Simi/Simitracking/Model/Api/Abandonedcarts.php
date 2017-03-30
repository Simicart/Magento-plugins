<?php

class Simi_Simitracking_Model_Api_Abandonedcarts extends Simi_Simiconnector_Model_Api_Customers {

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::ABANDONED_CARTS_DETAILS);
            $this->builderQuery = Mage::getModel('sales/quote')->load($data['resourceid']);
        } else {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::ABANDONED_CARTS_LIST);
            $this->builderQuery = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter('grand_total', array('neq' => '0'));
        }
    }
    
     /*
     * Show Abandoned Cart Detail
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
        //product array
        $productCollection = $this->builderQuery->getItemsCollection();
        $productInfoArray = array();
        foreach ($productCollection as $entity) {
            if ($entity->getData('parent_item_id') != NULL)
                continue;

            $options = array();
            if (version_compare(Mage::getVersion(), '1.5.0.0', '>=') === true) {
                $helper = Mage::helper('catalog/product_configuration');
                if ($entity->getProductType() == "simple") {
                    $options = Mage::helper('simiconnector/checkout')->convertOptionsCart($helper->getCustomOptions($entity));
                } elseif ($entity->getProductType() == "configurable") {
                    $options = Mage::helper('simiconnector/checkout')->convertOptionsCart($helper->getConfigurableOptions($entity));
                } elseif ($entity->getProductType() == "bundle") {
                    $options = Mage::helper('simiconnector/checkout')->getOptions($entity);
                }
            } else {
                if ($entity->getProductType() != "bundle") {
                    $options = Mage::helper('simiconnector/checkout')->getUsedProductOption($entity);
                } else {
                    $options = Mage::helper('simiconnector/checkout')->getOptions($entity);
                }
            }

            $pro_price = $entity->getCalculationPrice();
            if (Mage::helper('tax')->displayCartPriceInclTax() || Mage::helper('tax')->displayCartBothPrices()) {
                $pro_price = Mage::helper('checkout')->getSubtotalInclTax($entity);
            }

            $quoteitem = $entity->toArray($fields);
            $quoteitem['option'] = $options;
            $quoteitem['image'] = Mage::helper('simiconnector/products')->getImageProduct($entity->getProduct(), null, $parameters['image_width'], $parameters['image_height']);
            $productInfoArray[] = $quoteitem;
        }
        $info['products'] = $productInfoArray;
        return $this->getDetail($info);
    }
    
    protected function _whereFilter(&$query, $parameters)
    {
        if (isset($parameters[self::FILTER])) {
            foreach ($parameters[self::FILTER] as $key => $value) {
                if ($key == 'or') {
                    $filters = array();
                    foreach ($value as $k => $v) {
                        $filters[] = $this->_addCondition($k, $v, true);
                    }
                    if (count($filters)) $query->addFieldToFilter($filters);
                } else {
                    $filter = $this->_addCondition($key, $value);
                    $query->addFieldToFilter($key, $filter);
                }
            }
        }
    }

}

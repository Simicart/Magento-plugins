<?php

class Simi_Simitracking_Model_Api_Orders extends Simi_Simiconnector_Model_Api_Orders {

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::ORDER_DETAIL);
            $this->builderQuery = Mage::getModel('sales/order')->load($data['resourceid']);
            if (!$this->builderQuery->getId()) {
                $this->builderQuery = Mage::getModel('sales/order')->loadByIncrementId($data['resourceid']);
            }
            if (!$this->builderQuery->getId()) {
                throw new Exception(Mage::helper('simiconnector')->__('Cannot find the Order'), 6);
            }
        } else {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::ORDER_LIST);
            $this->builderQuery = Mage::getModel('sales/order')->getCollection();
            if (isset($data['params'])) {
                if (isset($data['params']['from_date']) && isset($data['params']['to_date'])) {
                    $fromDate = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $data['params']['from_date'])));
                    $toDate = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $data['params']['to_date'])) + 86399);

                    /* Get the collection */
                    $this->builderQuery = $this->builderQuery
                            ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate));
                }
                if (isset($data['params']['customer_email']) && isset($data['params']['customer_email']['like'])) {
                    $query = $data['params']['customer_email']['like'];
                    $pos = strpos($query, '%');
                    if ($pos === false) {
                        $this->builderQuery->addFieldToFilter('customer_id', array('neq' => null));
                    }
                }
            }
        }
    }

    public function update() {
        $data = $this->getData();
        $order = $this->builderQuery;
        $param = $data['contents'];
        $order_helper = Mage::helper('simiconnector/orders');
        $result = null;
        if ($param->status == 'cancel') {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::CANCEL_ORDER);
            $result = $order_helper->cancelOrder($order);
        } elseif ($param->status == 'invoice') {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::INVOICE_ORDER);
            $result = $order_helper->invoiceOrder($order);
        } elseif ($param->status == 'ship') {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::SHIP_ORDER);
            $result = $order_helper->shipOrder($order);
        } elseif ($param->status == 'hold') {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::HOLD_ORDER);
            $result = $order_helper->holdOrder($order);
        } elseif ($param->status == 'unhold') {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::UNHOLD_ORDER);
            $result = $order_helper->unHoldOrder($order);
        } else {
            $order->setState($param->status, true);
            $order->save();
        }
        if (null != $result) {
            $return_data = $this->show();
            $return_data[$this->getSingularKey()]['message'] = $result['message'];
            return $return_data;
        }
        return $this->show();
    }

    public function show() {
        Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::ORDER_DETAIL);
        $order = Simi_Simiconnector_Model_Api_Abstract::show();
        $order[$this->getSingularKey()]['payment_method'] = $this->builderQuery->getPayment()->getMethodInstance()->getCode();
        $order[$this->getSingularKey()]['payment_description'] = $this->builderQuery->getPayment()->getMethodInstance()->getTitle();
        $order[$this->getSingularKey()]['billing_address'] = Mage::helper('simiconnector/address')->getAddressDetail($this->builderQuery->getBillingAddress(), null);
        $shippingAdress = Mage::helper('simiconnector/address')->getAddressDetail($this->builderQuery->getShippingAddress(), null);
        if (count($shippingAdress) != 0) {
            $order[$this->getSingularKey()]['shipping_address'] = $shippingAdress;
        }
        $order[$this->getSingularKey()]['order_items'] = $this->_getProductFromOrderHistoryDetail($this->builderQuery);
        $order[$this->getSingularKey()]['total'] = Mage::helper('simiconnector/total')->showTotalOrder($this->builderQuery);
        $order[$this->getSingularKey()]['action'] = $this->getOrderAction();
        $order[$this->getSingularKey()]['base_currency_symbol'] = Mage::app()->getLocale()->currency($this->builderQuery->getData('base_currency_code'))->getSymbol();
        $order[$this->getSingularKey()]['order_currency_symbol'] = Mage::app()->getLocale()->currency($this->builderQuery->getData('order_currency_code'))->getSymbol();
        $order[$this->getSingularKey()]['global_currency_symbol'] = Mage::app()->getLocale()->currency($this->builderQuery->getData('global_currency_code'))->getSymbol();
        $order[$this->getSingularKey()]['store_currency_symbol'] = Mage::app()->getLocale()->currency($this->builderQuery->getData('store_currency_code'))->getSymbol();
        return $order;
    }

    public function index() {
        Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::ORDER_LIST);
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

            $data = $entity->toArray($fields);
            $data['base_currency_symbol'] = Mage::app()->getLocale()->currency($entity->getData('base_currency_code'))->getSymbol();
            $data['order_currency_symbol'] = Mage::app()->getLocale()->currency($entity->getData('order_currency_code'))->getSymbol();
            $data['global_currency_symbol'] = Mage::app()->getLocale()->currency($entity->getData('global_currency_code'))->getSymbol();
            $data['store_currency_symbol'] = Mage::app()->getLocale()->currency($entity->getData('store_currency_code'))->getSymbol();
            $info[] = $data;
            $all_ids[] = $entity->getId();
        }
        $orders = $this->getList($info, $all_ids, $total, $limit, $offset);
        $orders['layers']['layer_filter'][] = array(
            'attribute' => 'status',
            'title' => 'Status',
            'filter' => $this->get_orders_statuses(),
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $groups = $this->getGroups();
            $orders['layers']['layer_filter'][] = array(
                'attribute' => 'store',
                'title' => 'Store',
                'filter' => $groups['store'],
            );
            $orders['website'] = $groups['website'];
        }
        return $orders;
    }

    public function getOrderAction() {
        $cur_order = $this->builderQuery;
        $antions = array();
        if ($cur_order->canCancel()) {
            $actions[] = array(
                'key' => 'cancel',
                'value' => 'Cancel'
            );
        }

        if ($cur_order->canHold()) {
            $actions[] = array(
                'key' => 'hold',
                'value' => 'Hold'
            );
        }

        if ($cur_order->canUnhold()) {
            $actions[] = array(
                'key' => 'unhold',
                'value' => 'Unhold'
            );
        }

        if ($cur_order->canShip()) {
            $actions[] = array(
                'key' => 'ship',
                'value' => 'Ship'
            );
        }

        if ($cur_order->canInvoice()) {
            $actions[] = array(
                'key' => 'invoice',
                'value' => 'Invoice'
            );
        }
        return $actions;
    }

    protected function get_orders_statuses() {
        $statuses = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
        return $statuses;
    }

    protected function _getStoreModel() {
        return Mage::getSingleton('adminhtml/system_store');
    }

    protected function getGroups() {
        $return_data = array();
        $stores = array();
        $website_data = array();
        $data = $this->_getStoreModel()->getStoresStructure(false);
        foreach ($data as $website) {
            $item1 = array(
                'website_id' => $website['value'],
                'website_label' => $website['label'],
            );
            foreach ($website['children'] as $group) {
                $item2 = array(
                    'store_id' => $group['value'],
                    'store_label' => $group['label'],
                );
                foreach ($group['children'] as $storeview) {
                    $item3 = array(
                        'store_id' => $storeview['value'],
                        'store_label' => $storeview['label'],
                    );
                    $stores[] = $item3;
                    $item2['storeview'][] = $item3;
                }
                $item1['store'][] = $item2;
            }
            $website_data[] = $item1;
        }
        $return_data['website'] = $website_data;
        $return_data['store'] = $stores;
        return $return_data;
    }

}

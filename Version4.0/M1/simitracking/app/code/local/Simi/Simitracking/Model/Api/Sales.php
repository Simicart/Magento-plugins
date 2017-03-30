<?php

class Simi_Simitracking_Model_Api_Sales extends Simi_Simiconnector_Model_Api_Abstract {

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            if ($data['resourceid'] == 'total') {
                Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::TOTAL_DETAIL);
                $this->builderQuery = $this->getTotalSale();
            } else if ($data['resourceid'] == 'lifetime') {
                $this->builderQuery = $this->getLifetimeSale();
            } else if ($data['resourceid'] == 'refresh') {
                $collectionsNames = array('sales/report_order',
                    'sales/report_invoiced',
                    'sales/report_refunded',
                    'sales/report_bestsellers',
                    'tax/report_tax',
                    'sales/report_shipping');
                foreach ($collectionsNames as $collectionName) {
                    Mage::getResourceModel($collectionName)->aggregate();
                }
                $this->builderQuery = $this->getLifetimeSale();
            }
        } else {
            
        }
    }

    public function getTotalSale() {
        $dataModel = Mage::getResourceModel('reports/order_collection');
        $dataModel->checkIsLive();
        $this->_applyFilter($dataModel);
        $dataModel->calculateTotals(true);
        $dataModel->load();
        return $dataModel->getFirstItem();
    }

    public function getLifetimeSale() {
        $dataModel = Mage::getResourceModel('reports/order_collection')
                ->calculateSales(true);
        $dataModel->load();
        return $dataModel->getFirstItem();
    }

    protected function _applyFilter($dataModel) {
        $data = $this->getData();
        if (isset($data['params'])) {
            if (isset($data['params']['from_date']) && isset($data['params']['to_date'])) {
                $fromTimeStamp = $data['params']['from_date'].' 12:00:00';
                $toTimeStamp = $data['params']['to_date'].' 00:00:00';

                $dateStart = Mage::app()->getLocale()->date($fromTimeStamp, Varien_Date::DATETIME_INTERNAL_FORMAT);
                $dateEnd = Mage::app()->getLocale()->date($toTimeStamp, Varien_Date::DATETIME_INTERNAL_FORMAT);

                $dateStart->setHour(0);
                $dateStart->setMinute(0);
                $dateStart->setSecond(0);
                
                $dateEnd->setHour(23);
                $dateEnd->setMinute(59);
                $dateEnd->setSecond(59);
                
                $dateStart->setTimezone('Etc/UTC');
                $dateEnd->setTimezone('Etc/UTC');

                $fromDate = $dateStart->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                $toDate = $dateEnd->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

                $dataModel->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate));
            }

            if (isset($data['params']['store_id']) && $data['params']['store_id'] != '0' && $data['params']['store_id'] != '') {
                $dataModel->addFieldToFilter('store_id', $data['params']['store_id']);
            } else if (isset($data['params']['website'])) {
                $storeIds = Mage::app()->getWebsite($data['params']['website'])->getStoreIds();
                $dataModel->addFieldToFilter('store_id', array('in' => $storeIds));
            } else if (isset($data['params']['group'])) {
                $storeIds = Mage::app()->getGroup($data['params']['group'])->getStoreIds();
                $dataModel->addFieldToFilter('store_id', array('in' => $storeIds));
            }
        }
    }

    public function show() {
        $data = $this->getData();
        if ($data['resourceid']) {
            if (($data['resourceid'] == 'total') || ($data['resourceid'] == 'lifetime')) {
                return parent::show();
            } else if (($data['resourceid'] == 'saleinfo') || ($data['resourceid'] == 'refresh')) {
                Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::SALE_TRACKING);
                $this->builderQuery = Mage::getModel('reports/grouped_collection');
                $this->builderQuery->setResourceCollection('sales/report_order_collection');

                //month , day, year
                $period = 'day';
                if (isset($data['params']['period'])) {
                    $period = $data['params']['period'];
                }
                $stores = array();
                if (isset($data['params']['store_id']) && $data['params']['store_id'] != '0' && $data['params']['store_id'] != '') {
                    $stores[] = $data['params']['store_id'];
                }
                $totalsCollection = Mage::getResourceModel('sales/report_order_collection')
                        ->setPeriod($period);
                if (isset($data['params']) && isset($data['params']['from_date']) && isset($data['params']['to_date'])) {
                    $timeDifference = time() - Mage::getModel('core/date')->timestamp(time());
                    $fromTimeStamp = Mage::getModel('core/date')->timestamp(strtotime(str_replace('-', '/', $data['params']['from_date'])) - $timeDifference);
                    $toTimeStamp = Mage::getModel('core/date')->timestamp(strtotime(str_replace('-', '/', $data['params']['to_date']) . " GMT") - $timeDifference + 86399);
                    $fromDate = date('Y-m-d H:i:s', $fromTimeStamp);
                    $toDate = date('Y-m-d H:i:s', $toTimeStamp);
                    $totalsCollection->setDateRange($fromDate, $toDate);
                }
                $totalsCollection->addStoreFilter($stores)
                        ->setAggregatedColumns(
                                array('orders_count' => 'sum(orders_count)',
                                    'total_qty_ordered' => 'sum(total_qty_ordered)',
                                    'total_income_amount' => 'sum(total_income_amount)',
                                    'total_invoiced_amount' => 'sum(total_invoiced_amount)',
                                    //'total_refunded_amount' => 'sum(total_refunded_amount)',
                                    //'total_tax_amount' => 'sum(total_tax_amount)',
                                    //'total_shipping_amount' => 'sum(total_shipping_amount)',
                                    //'total_discount_amount' => 'sum(total_discount_amount)',
                                    //'total_canceled_amount' => 'sum(total_canceled_amount)',
                                    'period' => 'period'));

                $collection = Mage::getModel('reports/grouped_collection');
                Mage::helper('reports')->prepareIntervalsCollection(
                        $collection, $fromDate, $toDate, $period
                );
                $collection->setColumnGroupBy('period');
                $collection->setResourceCollection($totalsCollection);

                $arrayReturn = array();
                foreach ($collection->getItems() as $item) {
                    $dataArray = $item->toArray();
                    if ($period == 'day') {
                        $dayAndMonth = explode('-', substr($dataArray['period'], 5));
                        $dataArray['period'] = $dayAndMonth[1] . '/' . $dayAndMonth[0];
                    }
                    $arrayReturn[] = $dataArray;
                }
                $totalSale = $this->getTotalSale();
                $lifetimeSale = $this->getLifetimeSale()->toArray();
                
                if (isset($lifetimeSale['lifetime'])) {
                    $lifetimeSale['lifetime'] = (string)round($lifetimeSale['lifetime'], 2);
                } 
                if (isset($lifetimeSale['average'])) {
                    $lifetimeSale['average'] = (string)round($lifetimeSale['average'], 2);
                }
                return array('sale' => array('total_chart' => $arrayReturn, 'total_sale' => $totalSale->toArray(), 'lifetime_sale' => $lifetimeSale));
                //zend_debug::dump($totalsCollection->getItems());die;
            }
        }
    }

}

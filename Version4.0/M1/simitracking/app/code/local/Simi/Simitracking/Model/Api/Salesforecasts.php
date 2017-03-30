<?php

class Simi_Simitracking_Model_Api_Salesforecasts extends Simi_Simiconnector_Model_Api_Abstract {

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            
        }
    }

    public function show() {
        $data = $this->getData();
        if ($data['resourceid']) {
            if ($data['resourceid'] == 'day') {
                $grownValue = 0.2; //per year
                $totalDays = 90;
                if (isset($data['params']['number_of_days']) && $data['params']['number_of_days'] != '0' && $data['params']['number_of_days'] != '') {
                    $totalDays = $data['params']['number_of_days'];
                }
                // get latest 366 days sales report
                $period = 'day';
                $stores = array();
                if (isset($data['params']['store_id']) && $data['params']['store_id'] != '0' && $data['params']['store_id'] != '') {
                    $stores[] = $data['params']['store_id'];
                }
                $totalsCollection = Mage::getResourceModel('sales/report_order_collection')
                        ->setPeriod($period);

                $fromTimeStamp = Mage::getModel('core/date')->timestamp(time() - 366 * 86400);
                $toTimeStamp = Mage::getModel('core/date')->timestamp(time());
                $fromDate = date('Y-m-d H:i:s', $fromTimeStamp);
                $toDate = date('Y-m-d H:i:s', $toTimeStamp);
                $totalsCollection->setDateRange($fromDate, $toDate);

                $totalsCollection->addStoreFilter($stores)
                        ->setAggregatedColumns(
                                array('orders_count' => 'sum(orders_count)',
                                    'total_qty_ordered' => 'sum(total_qty_ordered)',
                                    'total_income_amount' => 'sum(total_income_amount)',
                                    'total_invoiced_amount' => 'sum(total_invoiced_amount)',
                                    'period' => 'period'));

                $collection = Mage::getModel('reports/grouped_collection');
                Mage::helper('reports')->prepareIntervalsCollection(
                        $collection, $fromDate, $toDate, $period
                );

                $collection->setColumnGroupBy('period');
                $collection->setResourceCollection($totalsCollection);

                $resultArray = $collection->getItems();
                $totalCollected = count($resultArray);
                
                
                // create forecast
                $forecastArray = array();
                $dayToReport = time();
                for ($i = 0; $i < $totalDays; $i++) {
                    $dayToReport += 86400;
                    $highestOrderCount = 0;
                    $highestSales = 0;
                    //last week
                    $weekItem = $resultArray[$totalCollected + $i%7 - 7]->toArray();
                    if (isset($weekItem['orders_count'])) {
                        $calculatedOrderCount = (int)($weekItem['orders_count']*pow(($grownValue + 1), ($i/365)));
                        if ($calculatedOrderCount > $highestOrderCount)
                            $highestOrderCount = $calculatedOrderCount;
                    }
                    if (isset($weekItem['total_invoiced_amount'])) {
                        $calculatedInvoiceAmout = $weekItem['total_invoiced_amount']*pow(($grownValue + 1), ($i/365));
                        if ($calculatedInvoiceAmout > $highestSales)
                            $highestSales = round($calculatedInvoiceAmout, 2);
                    }
                    
                    //last 30 days
                    $monthItem = $resultArray[$totalCollected + $i%30 - 30]->toArray();
                    if (isset($monthItem['orders_count']) && ($monthItem['orders_count'] > $highestOrderCount)) {
                        $calculatedOrderCount = (int)($monthItem['orders_count']*pow(($grownValue + 1), ($i/365)));
                        if ($calculatedOrderCount > $highestOrderCount)
                            $highestOrderCount = $calculatedOrderCount;
                    }
                    if (isset($monthItem['total_invoiced_amount'])) {
                        $calculatedInvoiceAmout = $monthItem['total_invoiced_amount']*pow(($grownValue + 1), ($i/365));
                        if ($calculatedInvoiceAmout > $highestSales)
                            $highestSales = round($calculatedInvoiceAmout, 2);
                    }

                    //last 365 days
                    $yearItem = $resultArray[$totalCollected + $i - 365]->toArray();
                    if (isset($yearItem['orders_count'])) {
                        $calculatedOrderCount = (int)($yearItem['orders_count']*pow(($grownValue + 1), ($i/365)));
                        if ($calculatedOrderCount > $highestOrderCount)
                            $highestOrderCount = $calculatedOrderCount;
                    }
                    if (isset($yearItem['total_invoiced_amount'])) {
                        $calculatedInvoiceAmout = $yearItem['total_invoiced_amount']*pow(($grownValue + 1), ($i/365));
                        if ($calculatedInvoiceAmout > $highestSales)
                            $highestSales = round($calculatedInvoiceAmout, 2);
                    }
                     
                    $forecastArray[$i] = array('period' => date('Y-m-d', $dayToReport), 
                        'orders_count' => $highestOrderCount, 
                        'orders_count_upper' => round($highestOrderCount*1.2), 
                        'orders_count_lower' => round($highestOrderCount*0.8), 
                        'total_invoiced_amount' => $highestSales, 
                        'total_invoiced_amount_upper' => $highestSales*1.2, 
                        'total_invoiced_amount_lower' => $highestSales*0.8);
                }
                return array('salesforecast' => array('day' => $forecastArray));
            }
        }
    }

}

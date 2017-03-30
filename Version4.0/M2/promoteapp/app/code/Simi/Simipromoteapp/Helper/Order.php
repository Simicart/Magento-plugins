<?php

namespace Simi\Simipromoteapp\Helper;

class Order extends Data
{
    public function processData($data){
        return array(
            'from_date' => $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Datetime')
                ->formatDateTime($data['from_date']),
            'to_date' => $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Datetime')
                ->formatDateTime($data['to_date']),
        );
    }

    public function initCollectionData($data){
        return $this->simiObjectManager->get('Magento\Sales\Model\Order')->getCollection()
            ->addAttributeToFilter('created_at', array('from'=>$data['from_date'], 'to'=>$data['to_date']));
            //->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE));

    }

    public function getInfo($data){        
        // parse data
        $data_process = $this->processData($data);
        
        // init collection
        $collection = $this->initCollectionData($data_process);

        $result = array();
        // get total order
        $result['total_orders'] = $collection->getSize();

        // get Order via Apps
        $table_transaction = $this->resource->getTableName('simiconnector_transactions');
        $collection->getSelect()->join(array(
            'transaction'=> $table_transaction),
            'transaction.order_id = main_table.entity_id'
        );

        $result['total_apps'] = $collection->count();

        // get Order via website
        $result['total_website'] = $result['total_orders'] - $result['total_apps'];

        if($result['total_orders'] == 0){
            $result['by_app'] = 0;
            $result['by_website'] = 0;

            $result['total_apps'] = 0;
            $result['total_website'] = 0;
        } else {
            $result['by_app'] = ($result['total_apps'] / $result['total_orders']) * 100;
            $result['by_website'] = ($result['total_website'] / $result['total_orders']) * 100;
        }

        return $result;
    }
}

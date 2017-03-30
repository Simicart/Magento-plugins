<?php

namespace Simi\Simipromoteapp\Helper;

class Report extends Data
{
    public function getCollectionEmailReport($data){
        $collection =  $this->simiObjectManager
            ->get('Simi\Simipromoteapp\Model\Simipromoteapp')
            ->getCollection()
            ->addFieldToSelect('template_id')
            ->addFieldToSelect('is_open')
        ;

        $collection->getSelect()
            ->columns(array('total_sent' => new \Zend_Db_Expr('count(*)')))
            ->columns(array('open_rate' => new \Zend_Db_Expr('sum(main_table.is_open)')))
        ;

        if($data['from'] != null && $data['to'] != null)
            $collection->getSelect()
            ->where('main_table.created_time between "'.$data['from'].'" and "'.$data['to'].'"');
        
        $collection->getSelect()->group('main_table.template_id');
        
        return $collection;
    }

    public function getTotalEmail($data, $type){
        $collection = $this->getCollectionEmailReport($data);
        $total = 0;
        if($type == 1){
            foreach($collection as $dt){
                $total += $dt['total_sent'];
            }
        } else if($type == 2){
            foreach($collection as $dt){
                $total += $dt['open_rate'];
            }
        }
        return $total;
    }

    /* report for highchart */
    public function reportEmail($data){
        $list_dates = $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Datetime')
                ->date_range($data['from'],$data['to'], "+1 day", "Y-m-d");
        $result = array();
        if(sizeof($list_dates) > 0){
            foreach($list_dates as $dr){
                $db = $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Db');
                $main_table_table = $db->getDbTableName();

                $collection = $this->simiObjectManager
                    ->get('Simi\Simipromoteapp\Model\Simipromoteapp')
                    ->getCollection()
                    ->addFieldToSelect('template_id')
                    ->addFieldToSelect('is_open')
                ;

                $collection->getSelect()
                    ->columns(array('total_sent' => new \Zend_Db_Expr('count(*)')))
                    ->columns(array('open_rate' => new \Zend_Db_Expr('sum(main_table.is_open)/IFNULL(count(*) ,0) * 100')))
                    ;

                $collection->getSelect()
                        ->where('UNIX_TIMESTAMP(DATE_FORMAT(main_table.created_time,"%Y-%m-%d")) = UNIX_TIMESTAMP("'.
                                $dr.'")');

                foreach($collection as $rs){
                    $result[] = array(
                        'current_date' => $dr,
                        'open_rate' => number_format($rs->getData('open_rate'),2) != null ?
                            number_format($rs->getData('open_rate'),2) : 0,
                        'total_sent' => $rs->getData('total_sent') != null ?  $rs->getData('total_sent') : 0,
                    );
                }


            }
        }
        return $result;
    }
}

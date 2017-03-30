<?php

/**
 * Promoteapp Content helper
 */

namespace Simi\Simipromoteapp\Helper;

use Simi\Simipromoteapp\Model\Status;

class Customer extends Data
{
    public function saveCustomerEmail($data){
        try{
            $model = $this->simiObjectManager->create('Simi\Simipromoteapp\Model\Simipromoteapp');
            $model->setData('customer_name',$data['name']);
            $model->setData('customer_email',$data['email']);
            $model->setData('template_id',$data['template_id']);
            $model->setData('is_open', Status::STATUS_DISABLED);
            $model->setData('created_time',$this->simiObjectManager
                            ->get('\Magento\Framework\Stdlib\DateTime\DateTimeFactory')->create()->gmtDate());
            $model->save();
        } catch (\Exception $ex){

        }
    }

    public function getReportObj($email,$template_id){
        try{
            $report = $this->simiObjectManager->get('Simi\Simipromoteapp\Model\Simipromoteapp')->getCollection()
                        -> addFieldToFilter('template_id',$template_id)
                        -> addFieldToFilter('customer_email',$email)
                        -> getFirstItem();

            if($report->getId())
                return $report;
            else
                return null;
        } catch (\Exception $ex){
            return null;
        }
    }
}

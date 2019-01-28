<?php

class Simicart_Simihr_Block_Department_List extends Mage_Core_Block_Template
{

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getJobList($department)
    {
        $jobFulltime = [];
        $jobParttime = [];

        if($department != 'all') {
            $departments = Mage::getResourceModel('simihr/department_collection')->addFieldToFilter('name', $department)->getData();
            $ids = $departments[0]['job_offer_ids'];
            $ids = explode(",",$ids);
        } else {
            $jobs = Mage::getResourceModel('simihr/jobOffers_collection')->addFieldToFilter('status', 1)->getData();
            foreach ($jobs as $job) {
                $allJobs[] = $job['id'];
            }
            $ids = $allJobs;
        }

            $jobFulltime[] = Mage::getResourceModel('simihr/jobOffers_collection')
                ->addFieldToSelect ('*')
                ->addFieldToFilter('id', array('in'=>$ids))
                ->addFieldToFilter('job_type', 'full-time')
                ->addFieldToFilter('status', 1)
                ->getData();
            $jobParttime[] = Mage::getResourceModel('simihr/jobOffers_collection')
                ->addFieldToSelect ('*')
                ->addFieldToFilter('id', array('in'=>$ids))
                ->addFieldToFilter('job_type', 'part-time')
                ->addFieldToFilter('status', 1)
                ->getData();

//        print_r($jobFulltime);die();
        return array(
                "full-time" => $jobFulltime,
                "part-time" => $jobParttime
            );
    }

    /**
     * @return array
     */
    public function searchList() {
        if(isset($_GET['q'])) {
            $allJobs = [];


            $search = $_GET['q'];

            if(isset($_GET['department']) && $_GET['department'] != 'all') {
                $departments = Mage::getResourceModel('simihr/department_collection')->addFieldToFilter('status', 1)->addFieldToFilter('name', $_GET['department'])->getData();
                $ids = $departments[0]['job_offer_ids'];
                $ids = explode(",",$ids);
            } else {
                $jobs = Mage::getResourceModel('simihr/jobOffers_collection')->addFieldToFilter('status', 1)->getData();
                foreach ($jobs as $job) {
                    $allJobs[] = $job['id'];
                }
                $ids = $allJobs;
            }

            if(isset($_GET['part-time']) || (!isset($_GET['full-time']) && !isset($_GET['part-time']))) {
                $jobParttime = Mage::getResourceModel('simihr/jobOffers_collection')
                    ->addFieldToSelect ('*')
                    ->addFieldToFilter('id', array('in'=>$ids))
                    ->addFieldToFilter('job_type', 'part-time')
                    ->addFieldToFilter(
                        array('name','requirements'),
                        array(
                            array('like' => '%'.$search.'%'),
                            array('like' => '%'.$search.'%')
                        )
                    )
                    ->addFieldToFilter('status', 1)
                    ->getData();
            }
            if(isset($_GET['full-time']) || (!isset($_GET['full-time']) && !isset($_GET['part-time']))) {
                $jobFulltime = Mage::getResourceModel('simihr/jobOffers_collection')
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('id', array('in'=>$ids))
                    ->addFieldToFilter('job_type', 'full-time')
                    ->addFieldToFilter(
                        array('name','requirements'),
                        array(
                            array('like' => '%'.$search.'%'),
                            array('like' => '%'.$search.'%')
                        )
                    )
                    ->addFieldToFilter('status', 1)
                    ->getData();
            }
            return array(
                "full-time" => $jobFulltime,
                "part-time" => $jobParttime
            );
        }
    }
}
<?php

class Simicart_Simihr_Block_Department extends Mage_Core_Block_Template
{

	public function _prepareLayout()
	{ 	    
		return parent::_prepareLayout();
	}
	public function getListDepartment() {
<<<<<<< HEAD
        $collection = Mage::getResourceModel('simihr/department_collection')->addFieldToFilter('status', 1)->setOrder('sort_order', 'DESC')->getData();
        return $collection;
    }
    public function countAlljobs($name) {
        $allJobs = 0;
        $departments = Mage::getResourceModel('simihr/department_collection')->addFieldToFilter('name', $name)->getData();
        // ->addFieldToFilter('deadline', array('neq' => ''))

        $totalJob = explode(",",$departments[0]['job_offer_ids']);

        if (isset($departments[0])) {
            $jobs = Mage::getResourceModel('simihr/jobOffers_collection')->addFieldToFilter('id', array('in' => $totalJob))->addFieldToFilter('deadline', array('neq' => ''))->getData();

            $allJobs =count($jobs);
        } 
        return $allJobs;
    }
    public function getTitle(){
        $title = Mage::getResourceModel('simihr/content_collection')->addFieldToFilter('name', 'department_title')->getData();
        if (isset($title[0])) {
            $title = $title[0];
        } else $title = '';
        return $title;
    }
   
=======
        $collection = Mage::getResourceModel('simihr/department_collection')->addFieldToFilter('status', 1)->setOrder('sort_order', 'DESC')->setPageSize(4)->getData();

        return $collection;
    }
    public function countAlljobs() {
        $jobs = Mage::getResourceModel('simihr/jobOffers_collection')->addFieldToFilter('status', 1)->getData();
        foreach ($jobs as $job) {
            $allJobs[] = $job['id'];
        }
        return count($allJobs);
    }
>>>>>>> 285bf9b53deaeb11971b0ee3c14850d633380d58
}
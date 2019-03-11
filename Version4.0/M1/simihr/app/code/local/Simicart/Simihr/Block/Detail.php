<?php

class Simicart_Simihr_Block_Detail extends Mage_Core_Block_Template
{

	public function _prepareLayout()
	{ 	    
		return parent::_prepareLayout();
	}
    public function getJobInfo($job, $jobType) {
        $collection = Mage::getResourceModel('simihr/jobOffers_collection')->addFieldToFilter('status', 1)->addFieldToFilter('name', $job)->addFieldToFilter('job_type', $jobType)->getData();
        return $collection;
    }
    public function getTitle(){
        $title = Mage::getResourceModel('simihr/content_collection')->addFieldToFilter('name', 'detail_title')->getData();
        if (isset($title[0])) {
            $title = $title[0];
        } else $title = '';
        return $title;
    }
}
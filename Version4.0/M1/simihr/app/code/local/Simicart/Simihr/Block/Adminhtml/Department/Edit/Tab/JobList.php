<?php

class Simicart_Simihr_Block_Adminhtml_Department_Edit_Tab_JobList extends Mage_Adminhtml_Block_Widget_Grid{

    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('simihr_department_edit_tab_jobList');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

//        $id  = (int) $this->getRequest()->getParam('id');
//        if ($id) {
//            $this->setDefaultFilter(array('in_jobs'=>1));
//        }
    }

    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'simihr/jobOffers_collection';
    }

    protected function _prepareCollection()
    {

        // Get and set our collection for the grid
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        // ->addFieldToFilter('department_id', $id)
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

//    protected function _prepareMassactionColumn()
//    {
//        parent::_prepareMassactionColumn();
//        $jobIds = Mage::getModel('simihr/department')->getCollection();
//        $this->getColumn('massaction')->setValues($ids);
//        return $this;
//    }

    protected function _prepareColumns()
    {
        // Add the columns that should appear in the grid

        $this->addColumn('in_jobs', array(
            'header_css_class' => 'a-center',
            'header' => $this->__('Selected'),
            'index' => 'id',
            'type' => 'checkbox',
            'align' => 'center',
//            'values'=> $this->_getSelectedJobs(),
            'name' => 'in_jobs',
            'renderer' => 'Simicart_Simihr_Block_Adminhtml_Renderer_Checkbox',
            'sortable'  => false,
        ));

        $this->addColumn('name',
            array(
                'header'=> $this->__('Name'),
                'index' => 'name',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );

        $this->addColumn('img_url',
            array(
                'header'=> $this->__('Image'),
                'width' => '50px',
                'index' => 'img_url',
                'renderer' => 'Simicart_Simihr_Block_Adminhtml_Renderer_Image',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('job_type',
            array(
                'header'=> $this->__('Job Type'),
                'index' => 'job_type',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('requirements',
            array(
                'header'=> $this->__('Requirement'),
                'index' => 'requirements',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );


        return parent::_prepareColumns();
    }

    protected function _getSelectedJobs()
    {
        $job = $this->getRequest()->getPost('selected_jobs');
        if ($job === null) {
            $id  = (int) $this->getRequest()->getParam('id');
            $job = Mage::getResourceModel('simihr/department_collection')->addFieldToFilter('id', $id)->getData();
            $ids = explode(",",$job[0]['job_offer_ids']);
            return ($ids);
        }
        return $job;
//        $jobs = $this->getRequest()->getPost('selected_jobs');
//        if ($jobs === null) {
//            $ids = [];
//            $jobs = Mage::getResourceModel('simihr/jobOffers_collection')->getData();
//            foreach ($jobs as $job) {
//                $ids[] = $job['id'];
//            }
//            return $ids;
//        }
//        return $job;
    }


    protected function _addColumnFilterToCollection($column){
        // Set custom filter for in product flag
        if ($column->getId() == 'in_jobs') {
            $jobIds = $this->_getSelectedJobs();
            if (empty($jobIds)) {
                $jobIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', array('in'=>$jobIds));
            }
            else {
                if($jobIds) {
                    $this->getCollection()->addFieldToFilter('id', array('nin'=>$jobIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/gridJobList', array('_current'=>true));
    }

}
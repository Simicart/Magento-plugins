<?php
class Simicart_Simihr_DepartmentController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		 $this->loadLayout();
      	$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
  //       $this->getLayout()->getBlock('content')->append($block);
  //       $this->_initLayoutMessages('core/session');
         $this->renderLayout();
	}

    public function listAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->_initLayoutMessages('core/session');
        $this->renderLayout();
    }
    public function searchAction() {
            $this->loadLayout();
            $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
            $this->getLayout()->getBlock('content')->append($block);
            $this->_initLayoutMessages('core/session');
            $this->renderLayout();
    }

    public function searchAjaxAction() {
	    $jobCollection = [];
        if(isset($_GET['q'])) {
            $search = $_GET['q'];
            $allJobs = [];

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
            $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("full_time"=>$jobFulltime, "part_time"=>$jobParttime)));

        }
    }

    public function installDbAction(){
        $setup = new Mage_Core_Model_Resource_Setup('core_setup'); // 1.9 doesnt require param
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->dropTable( $setup->getTable('simihr_department'));
        $setup->getConnection()->dropTable( $setup->getTable('simihr_jobOffers'));
        $setup->getConnection()->dropTable( $setup->getTable('simihr_submissions'));
<<<<<<< HEAD
        $setup->getConnection()->dropTable( $setup->getTable('simihr_content'));
=======
>>>>>>> 285bf9b53deaeb11971b0ee3c14850d633380d58

        $table = $installer->getConnection()
            ->newTable($installer->getTable('simihr/department'))
            ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'auto_increment' => true,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), ' Department ID')
            ->addColumn('name', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
                'nullable'  => false,
            ), 'Name')
            ->addColumn('dep_img', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable'  => false,
            ), 'dep_img')
            ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable'  => false,
            ), 'Email')
            ->addColumn('mobile', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable'  => false,
            ), 'Mobile')
            ->addColumn('status', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
                'nullable'  => false,
            ), 'Status')
            ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT,1000000000,  array(
                'nullable'  => false,
            ), 'description')
            ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER,null,  array(
            ), 'Sort order')
            ->addColumn('job_offer_ids', Varien_Db_Ddl_Table::TYPE_TEXT,255,  array(
            ), 'Job Offer IDs')

            ->setComment('Department Table');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('simihr/jobOffers'))
            ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'auto_increment' => true,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), ' Job Offer ID')
            ->addColumn('department_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                'nullable'  => false,
            ), 'Department ID')
            ->addColumn('name', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
                'nullable'  => false,
            ), 'Name')
            ->addColumn('img_url', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable'  => false,
            ), 'img_url')
            ->addColumn('job_type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable'  => false,
            ), 'job_type')
<<<<<<< HEAD
            ->addColumn('overall', Varien_Db_Ddl_Table::TYPE_TEXT, 1000000000, array(
            ), 'overall')
=======
>>>>>>> 285bf9b53deaeb11971b0ee3c14850d633380d58
            ->addColumn('requirements', Varien_Db_Ddl_Table::TYPE_TEXT, 1000000000, array(
            ), 'requirements')
            ->addColumn('work_related', Varien_Db_Ddl_Table::TYPE_TEXT, 1000000000, array(
            ), 'work_related')
            ->addColumn('benifits', Varien_Db_Ddl_Table::TYPE_TEXT, 1000000000, array(
            ), 'benifits')
<<<<<<< HEAD
            ->addColumn('overall_vn', Varien_Db_Ddl_Table::TYPE_TEXT, 1000000000, array(
            ), 'overall_vn')
=======
>>>>>>> 285bf9b53deaeb11971b0ee3c14850d633380d58
            ->addColumn('requirements_vn', Varien_Db_Ddl_Table::TYPE_TEXT, 1000000000, array(
            ), 'requirements_vn')
            ->addColumn('work_related_vn', Varien_Db_Ddl_Table::TYPE_TEXT, 1000000000, array(
            ), 'work_related_vn')
            ->addColumn('benifits_vn', Varien_Db_Ddl_Table::TYPE_TEXT, 1000000000, array(
            ), 'benifits_vn')
            ->addColumn('status', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
            ), 'Status')
            ->addColumn('quatity', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            ), 'quatity')
            ->addColumn('start_time', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            ), 'start_time')
            ->addColumn('deadline', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            ), 'deadline')

            ->setComment('Job Offers Table');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('simihr/submissions'))
            ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'auto_increment' => true,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Submission ID')
            ->addColumn('first_name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'First Name')
            ->addColumn('last_name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'Last Name')
            ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'Email')
            ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'Phone')
            ->addColumn('job_applied', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'Job Apllied')
            ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'Comment')
            ->addColumn('submitted_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                'nullable' => false,
                'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
            ), 'Submission time')
            ->addColumn('resume_cv_path', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'Resume Path')
            ->addColumn('cover_letter_path', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'Cover Letter Path')

            ->setComment('Submissions Table');
        $installer->getConnection()->createTable($table);
<<<<<<< HEAD

        $table = $installer->getConnection()
            ->newTable($installer->getTable('simihr/content'))
            ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'auto_increment' => true,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'id')
            ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'name')
            ->addColumn('detail', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'detail')
            ->addColumn('detail_vn', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'detail_vn')
            ->addColumn('note', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'note')

            ->setComment('Content Table');
        $installer->getConnection()->createTable($table);
=======
>>>>>>> 285bf9b53deaeb11971b0ee3c14850d633380d58
        $installer->endSetup();
        echo "success";
    }

}


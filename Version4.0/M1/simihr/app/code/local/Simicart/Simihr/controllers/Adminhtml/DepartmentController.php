<?php
class Simicart_Simihr_Adminhtml_DepartmentController extends Mage_Adminhtml_Controller_Action
{
    public function _initDepartment() {
        $id  = (int) $this->getRequest()->getParam('id');
        $department = Mage::getModel('simihr/department');
        if ($id) {
            $department->load($id);
        }
        Mage::register('current_department', $department);
        return $department;
    }

    public function indexAction()
    {
        // Let's call our initAction method which will set some basic params for each action
        $this->_initAction()
            ->renderLayout();
    }

    public function newAction()
    {
        // We just forward the new action to a blank edit form
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_initAction();
        // Get id if available
        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('simihr/department');

        if ($id) {
            // Load record
            $model->load($id);

            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This department no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Department'));

        $data = Mage::getSingleton('adminhtml/session')->getBazData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('simicart_simihr', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Department') : $this->__('New Department'), $id ? $this->__('Edit Department') : $this->__('New Department'))
            ->_addContent($this->getLayout()->createBlock('simihr/adminhtml_department_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            try {
                //Image
                if ($postData['department']['dep_img']['delete']== 1) {
                    $postData['department']['dep_img'] = '';
                }
                if (isset($_FILES) && $_FILES['dep_img']['name'] != '') {
                    if ($_FILES['dep_img']['name']) {
                        if ($this->getRequest()->getParam("id")) {
                            $model = Mage::getSingleton('simihr/department')->load($this->getRequest()->getParam("id"));
                            if ($model->getData('dep_img')) {
                                $io = new Varien_Io_File();
                                $io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('dep_img'))));
                            }
                        }

                        $fileName       = $_FILES['dep_img']['name'];
                        $fileExt        = strtolower(substr(strrchr($fileName, ".") ,1));
                        $fileNamewoe    = rtrim($fileName, $fileExt);
                        // $fileName       = preg_replace('/\s+', '', $fileNamewoe) . time() . '.' . $fileExt;
                        $uploader       = new Varien_File_Uploader('dep_img');
                        $uploader->setAllowedExtensions(array('jpg', 'png','gif'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media') ;
                        if(!is_dir($path)){
                            mkdir($path, 0777, true);
                        }
                        $uploader->save($path . DS . 'simihr' . DS . 'department'  . DS, $fileName );

                        // them data[img]
                        $postData['department']['dep_img'] = "simihr/department/".$_FILES['dep_img']['name'];

                    }
                } else {
                    $postData['department']['dep_img'] = $postData['department']['dep_img']['value'];
                }

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            $model = Mage::getSingleton('simihr/department');
            $model->setData($postData['department']);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The department has been saved.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this department.'));
            }

            Mage::getSingleton('adminhtml/session')->setBazData($postData['department']);
            $this->_redirectReferer();
        }
    }

    public function messageAction()
    {
        $data = Mage::getModel('simihr/department')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            // Make the active menu match the menu config nodes (without 'children' inbetween)
            ->_setActiveMenu('simihr/simihr_department')
            ->_title($this->__('Simihr'))->_title($this->__('Department'))
            ->_addBreadcrumb($this->__('Simihr'), $this->__('Simihr'))
            ->_addBreadcrumb($this->__('Department'), $this->__('Department'));

        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simihr/simihr_department');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $department = Mage::getModel('simihr/department');
                $department->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Department was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error deleteing department.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError($this->__('Could not find department to delete.'));
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('simihr/adminhtml_department_grid')->toHtml()
        );
    }

    public function gridJobListAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('simihr/adminhtml_department_edit_tab_joblist')->toHtml()
        );
    }

//    public function exportCsvAction(){
//        $fileName   = 'file.csv';
//        $content	= $this->getLayout()->createBlock('simihr/adminhtml_department_grid')->getCsv();
//        $this->_prepareDownloadResponse($fileName, $content);
//    }
//
//    public function exportExcelAction(){
//        $fileName   = 'file.xls';
//        $content	= $this->getLayout()->createBlock('simihr/adminhtml_department_grid')->getExcelFile();
//        $this->_prepareDownloadResponse($fileName, $content);
//    }
//
//    public function exportXmlAction(){
//        $fileName   = 'file.xml';
//        $content	= $this->getLayout()->createBlock('simihr/adminhtml_department_grid')->getXml();
//        $this->_prepareDownloadResponse($fileName, $content);
//    }
}
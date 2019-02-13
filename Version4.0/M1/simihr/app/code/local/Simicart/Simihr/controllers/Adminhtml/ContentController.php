<?php
class Simicart_Simihr_Adminhtml_ContentController extends Mage_Adminhtml_Controller_Action
{
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
        $model = Mage::getModel('simihr/content');

        if ($id) {
            // Load record
            $model->load($id);

            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This content no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Content'));

        $data = Mage::getSingleton('adminhtml/session')->getBazData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('simicart_simihr', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Content') : $this->__('New Content'), $id ? $this->__('Edit Content') : $this->__('New Content'))
//            ->_addContent($this->getLayout()->createBlock('simihr/adminhtml_content_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {

            // try {
            //     //Image
            //     if ($postData['img_url']['delete']== 1) {
            //         $postData['img_url'] = '';
            //     }
            //     if (isset($_FILES) && $_FILES['img_url']['name'] != '') {
            //         if ($_FILES['img_url']['name']) {
            //             if ($this->getRequest()->getParam("id")) {
            //                 $model = Mage::getSingleton('simihr/jobOffers')->load($this->getRequest()->getParam("id"));
            //                 if ($model->getData('img_url')) {
            //                     $io = new Varien_Io_File();
            //                     $io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('img_url'))));
            //                 }
            //             }

            //             $fileName       = $_FILES['img_url']['name'];
            //             $fileExt        = strtolower(substr(strrchr($fileName, ".") ,1));
            //             $fileNamewoe    = rtrim($fileName, $fileExt);
            //             // $fileName       = preg_replace('/\s+', '', $fileNamewoe) . time() . '.' . $fileExt;
            //             $uploader       = new Varien_File_Uploader('img_url');
            //             $uploader->setAllowedExtensions(array('jpg', 'png','gif'));
            //             $uploader->setAllowRenameFiles(false);
            //             $uploader->setFilesDispersion(false);
            //             $path = Mage::getBaseDir('media') ;
            //             if(!is_dir($path)){
            //                 mkdir($path, 0777, true);
            //             }
            //             $uploader->save($path . DS . 'simihr' . DS . 'joboffers'  . DS, $fileName );

            //             // them data[img]
            //             $postData['img_url'] = "simihr/joboffers/".$_FILES['img_url']['name'];
            //         }
            //     } else {
            //         $postData['img_url'] = $postData['img_url']['value'];
            //     }

            // } catch (Exception $e) {
            //     Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            //     $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            //     return;
            // }
            
            $model = Mage::getSingleton('simihr/content');
            $model->setData($postData);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The content has been saved.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this content.'));
            }
            Mage::getSingleton('adminhtml/session')->setBazData($postData);
            $this->_redirectReferer();
        }
    }

    public function messageAction()
    {
        $data = Mage::getModel('simihr/content')->load($this->getRequest()->getParam('id'));
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
            ->_setActiveMenu('simihr/simihr_content')
            ->_title($this->__('Simihr'))->_title($this->__('Content'))
            ->_addBreadcrumb($this->__('Simihr'), $this->__('Simihr'))
            ->_addBreadcrumb($this->__('Content'), $this->__('Content'));

        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simihr/simihr_content');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $department = Mage::getModel('simihr/content');
                $department->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__(' Selected content was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error deleteing selected content.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError($this->__('Could not find content to delete.'));
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('simihr/adminhtml_content_grid')->toHtml()
        );
    }
}
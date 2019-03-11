<?php
class Simicart_Simihr_Adminhtml_SubmissionsController extends Mage_Adminhtml_Controller_Action
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
        $model = Mage::getModel('simihr/submissions');

        if ($id) {
            // Load record
            $model->load($id);

            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This submission no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Submission'));

        $data = Mage::getSingleton('adminhtml/session')->getBazData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('simicart_simihr', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Submission') : $this->__('New Submission'), $id ? $this->__('Edit Submission') : $this->__('New Submission'))
            ->_addContent($this->getLayout()->createBlock('simihr/adminhtml_submissions_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('simihr/submissions');
            $model->setData($postData);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The submission has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this submission.'));
            }

            Mage::getSingleton('adminhtml/session')->setBazData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $department = Mage::getModel('simihr/submissions');
                $department->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__(' Selected submission was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error deleteing selected submission.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError($this->__('Could not find submission to delete.'));
        $this->_redirect('*/*/');
    }

    public function messageAction()
    {
        $data = Mage::getModel('simihr/submissions')->load($this->getRequest()->getParam('id'));
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
            ->_setActiveMenu('simihr/simihr_submissions')
            ->_title($this->__('Simihr'))->_title($this->__('Submission'))
            ->_addBreadcrumb($this->__('Simihr'), $this->__('Simihr'))
            ->_addBreadcrumb($this->__('Submission'), $this->__('Submission'));

        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simihr/simihr_submissions');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('simihr/adminhtml_submissions_grid')->toHtml()
        );
    }

    public function massDeleteAction() {
        $requestIds = $this->getRequest()->getParam('id');
        if(!is_array($requestIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select request(s)'));
        } else {
            try {
                foreach ($requestIds as $requestId) {
                    $RequestData = Mage::getModel('simihr/submissions')->load($requestId);
                    $RequestData->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__(
                        '%d submission(s) were successfully deleted', count($requestIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
}
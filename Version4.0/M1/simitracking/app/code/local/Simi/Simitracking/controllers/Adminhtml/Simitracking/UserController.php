<?php

class Simi_Simitracking_Adminhtml_Simitracking_UserController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Simi_Simitracking_Adminhtml_Simitracking_UserController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('simitracking/user')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('User Manager'), Mage::helper('adminhtml')->__('User Manager'));
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction() {
        if (Mage::getModel('simitracking/role')->getCollection()->count() == 0)
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simitracking')->__('There is No Role Created'));
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('simitracking/user')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data))
                $model->setData($data);

            Mage::register('user_data', $model);
            
            $this->loadLayout();
            $this->_setActiveMenu('simitracking/user');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('simitracking/adminhtml_user_edit'))
                    ->_addLeft($this->getLayout()->createBlock('simitracking/adminhtml_user_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simitracking')->__('User does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) { 
            /*
             * Profile Image
             */
            if (isset($_FILES['user_profile_image_co']['name']) && $_FILES['user_profile_image_co']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('user_profile_image_co');
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    str_replace(" ", "_", $_FILES['user_profile_image_co']['name']);
                    $path = Mage::getBaseDir('media') . DS . 'simi' . DS . 'simitracking' . DS . 'profileimage';
                    if (!is_dir($path)) {
                        try {
                            mkdir($path, 0777, TRUE);
                        } catch (Exception $e) {
                            
                        }
                    }
                    $result = $uploader->save($path, $_FILES['user_profile_image_co']['name']);
                    try {
                        chmod($path . '/' . $result['file'], 0777);
                    } catch (Exception $e) {
                        
                    }
                    $data['user_profile_image'] = Mage::getBaseUrl('media') . 'simi/simitracking/profileimage/' . $result['file'];
                } catch (Exception $e) {
                    $data['user_profile_image'] = Mage::getBaseUrl('media') . 'simi/simitracking/profileimage/' . $_FILES['user_profile_image_co']['name'];
                }
            }
            if (isset($data['user_profile_image_co']['delete']) && $data['user_profile_image_co']['delete'] == 1) {
                try {
                    unlink($data['user_profile_image_co']['value']);
                } catch (Exception $e) {
                    
                }
                $data['user_profile_image'] = '';
            }
            
            
            $model = Mage::getModel('simitracking/user');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simitracking')->__('User was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                $customer = Mage::getModel('simitracking/customer')->getCustomerByEmail($model->getData('user_email'));
                if (!$customer->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simitracking')->__('There is no customer account created with this email. Please create an account with the email you added or use another email.'));
                }
                Mage::getModel('simitracking/device')->createKeyDevice($model->getData('user_email'));
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simitracking')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    
    
    /**
     * delete item action
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('simitracking/user');
                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('User was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction() {
        $bannerIds = $this->getRequest()->getParam('simitracking');

        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $notice = Mage::getModel('simitracking/user')->load($bannerId);
                    $notice->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($bannerIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('simitracking');
    }

}

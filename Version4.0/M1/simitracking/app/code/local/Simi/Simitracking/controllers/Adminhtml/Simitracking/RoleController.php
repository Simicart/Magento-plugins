<?php

class Simi_Simitracking_Adminhtml_Simitracking_RoleController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Simi_Simitracking_Adminhtml_Simitracking_RoleController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('simitracking/role')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Role Manager'), Mage::helper('adminhtml')->__('Role Manager'));
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
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('simitracking/role')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data))
                $model->setData($data);

            Mage::register('role_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('simitracking/role');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('simitracking/adminhtml_role_edit'))
                    ->_addLeft($this->getLayout()->createBlock('simitracking/adminhtml_role_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simitracking')->__('Role does not exist'));
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
            $model = Mage::getModel('simitracking/role');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));
            try {
                $model->save();
                if ($model->getId()) {
                    foreach (Mage::getModel('simitracking/permission')->getCollection()->addFieldToFilter('role_id', $model->getId()) as $permissionModel) {
                        $permissionModel->delete();
                    }
                    foreach ($data as $index => $permissions) {
                        if ($permissions == '1' && strpos($index, 'simi_permission_id_') !== false) {
                            $permissionModel = Mage::getModel('simitracking/permission');
                            $permissionModel->setData('role_id', $model->getId());
                            $permissionModel->setData('permission_id', str_replace('simi_permission_id_', '', $index));
                            $permissionModel->save();
                            $model->save();
                        }
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simitracking')->__('Role was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

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
                $model = Mage::getModel('simitracking/role');
                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Role was successfully deleted'));
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
                    $notice = Mage::getModel('simitracking/role')->load($bannerId);
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

<?php

class Simi_Simistorelocator_Adminhtml_Simistorelocator_SpecialdayController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('simistorelocator/specialday')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Special Day Manager'), Mage::helper('adminhtml')->__('Special Day Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('simistorelocator/specialday')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('specialday_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('simistorelocator/specialday');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Specialday Manager'), Mage::helper('adminhtml')->__('Specialday Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Specialday News'), Mage::helper('adminhtml')->__('Specialday News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('simistorelocator/adminhtml_specialday_edit'))
                    ->_addLeft($this->getLayout()->createBlock('simistorelocator/adminhtml_specialday_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simistorelocator')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('simistorelocator/specialday');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('simistorelocator/specialday');
            if ($data['store_id']) {
                $data['store_id'] = implode(',', $data['store_id']);
            }
            if ($this->getRequest()->getParam('id')) {
                $specialdays = $model->getCollection()
                        ->addFieldToFilter('simistorelocator_specialday_id', array('nin' => $this->getRequest()->getParam('id')))
                        ->addFieldToFilter('store_id', $data['store_id'])
                        ->addFieldToFilter('date', $data['date'])
                        ->addFieldToFilter('specialday_date_to', $data['specialday_date_to']);
            } else {
                $specialdays = $model->getCollection()
                        ->addFieldToFilter('store_id', $data['store_id'])
                        ->addFieldToFilter('date', $data['date'])
                        ->addFieldToFilter('specialday_date_to', $data['specialday_date_to']);
            }

            if ($data['specialday_time_open']) {
                $data['specialday_time_open'] = implode(':', $data['specialday_time_open']);
            }
            if ($data['specialday_time_close']) {
                $data['specialday_time_close'] = implode(':', $data['specialday_time_close']);
            }
            if (!($specialdays->getData())) {
                $model->addData($data)
                        ->setId($this->getRequest()->getParam('id'));
                try {
                    $model->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simistorelocator')->__('Item was successfully saved'));
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
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simistorelocator')->__('Specialday exists'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simistorelocator')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $simistorelocatorIds = $this->getRequest()->getParam('specialday');
        if (!is_array($simistorelocatorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($simistorelocatorIds as $simistorelocatorId) {
                    $simistorelocator = Mage::getModel('simistorelocator/specialday')->load($simistorelocatorId);
                    $simistorelocator->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($simistorelocatorIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'specialday.csv';
        $content = $this->getLayout()->createBlock('simistorelocator/adminhtml_specialday_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'specialday.xml';
        $content = $this->getLayout()->createBlock('simistorelocator/adminhtml_specialday_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('simiconnector/simi_rich_content/simistorelocator');
    }

}

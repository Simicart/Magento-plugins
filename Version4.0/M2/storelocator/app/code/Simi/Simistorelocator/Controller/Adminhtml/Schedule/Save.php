<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Schedule;

class Save extends \Simi\Simistorelocator\Controller\Adminhtml\Schedule {

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute() {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            $id = $this->getRequest()->getParam(static::PARAM_CRUD_ID);

            /** @var \Simi\Simistorelocator\Model\Schedule $model */
            $model = $this->_createMainModel()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Schedule no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            if ($model->hasData('serialized_stores')) {
                $model->setData(
                        'in_storelocator_ids', $this->backendHelperJs
                        ->decodeGridSerializedInput($model->getData('serialized_stores'))
                );
            }

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The Schedule has been saved.'));
                $this->_getSession()->setFormData(false);

                return $this->_getBackResultRedirect($resultRedirect, $model->getId());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving the Schedule.'));
                $this->_getSession()->setFormData($data);

                $this->_getSession()->setSerializedStores($model->getData('serialized_stores'));

                return $resultRedirect->setPath('*/*/edit', [static::PARAM_CRUD_ID => $id]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}

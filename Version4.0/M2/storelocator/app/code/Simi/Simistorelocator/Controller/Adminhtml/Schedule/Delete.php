<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Schedule;

class Delete extends \Simi\Simistorelocator\Controller\Adminhtml\Schedule {

    /**
     * Delete action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam(static::PARAM_CRUD_ID);
        if ($id) {
            try {
                /** @var \Simi\Simistorelocator\Model\Schedule $model */
                $model = $this->_createMainModel();
                $model->setId($id)->delete();
                $this->messageManager->addSuccess(__('You deleted the Schedule.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [static::PARAM_CRUD_ID => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a Schedule to delete.'));

        return $resultRedirect->setPath('*/*/');
    }

}

<?php

namespace Simi\Simipushnotif\Controller\Adminhtml\Notification;

use Magento\Backend\App\Action;

class Save extends Action
{
    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $simiObjectManager = $this->_objectManager;
        $model = $simiObjectManager->create('Simi\Simipushnotif\Model\Notification');

        $id = $this->getRequest()->getParam('notice_id');
        if ($id) {
            $model->load($id);
        }
        $is_delete_siminotification = isset($data['image_url']['delete']) ? $data['image_url']['delete'] : false;
        $data['image_url'] = isset($data['image_url']['value']) ? $data['image_url']['value'] : '';
        $data['created_time'] = time();
        $data['device_id'] = (isset($data['devices_pushed']) &&
            $data['devices_pushed'] && ($data['devices_pushed'] !== '')
        ) ? $data['devices_pushed'] : '';
        $model->addData($data);
        try {

            $imageHelper = $simiObjectManager->get('Simi\Simipushnotif\Helper\Data');
            if ($is_delete_siminotification && $model->getImageUrl()) {
                $model->setImageUrl('');
            } else {
                $imageFile = $imageHelper->uploadImage('image_url', 'notification');
                if ($imageFile) {
                    $model->setImageUrl($imageFile);
                    $data['image_url'] = $imageFile;
                }
            }

            if ($data['device_id'] && ($data['device_id'] != '')) {
                $data['notice_type'] = 2;
            } else {
                $data['notice_type'] = 1;
            }

            if (!isset($data['type']) && $data['product_id']) {
                $data['type'] = 1;
            }
            $model->setData($data)->setStatus(1);
            $mess = $simiObjectManager->get('Simi\Simipushnotif\Model\Notification')->getCollection();
            foreach ($mess as $item) {
                $item->setStatus(2);
                $item->save();
            }
            if ($id) {
                $model->setId($id);
            }

            if ($this->getRequest()->getParam('back')) {
                $model->save();
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $simiObjectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                $this->_redirect('*/*/edit', ['message_id' => $model->getId(), '_current' => true]);
                return;
            } else {
                $device_ids = [];
                if ($data['device_id'] && ($data['device_id'] != '')) {
                    $device_ids = explode(',', $data['device_id']);
                }

                foreach ($device_ids as $index => $device) {
                    $send = $simiObjectManager->get('Simi\Simipushnotif\Model\Device')->send($device);
                    if (!$send) {
                        $deviceInfo = $simiObjectManager->get('Simi\Simipushnotif\Model\Device')->load($device);
                        if ($deviceInfo->getId()) {
                            $deviceInfo->delete();
                        }

                        unset($device_ids[$index]);
                    }
                }
                if ($device_ids && count($device_ids)) {
                    $model->setData('device_id', implode(',', $device_ids));
                }
                $model->setCreatedTime(date('Y-m-d H:i:s'))
                    ->setStatus(1);
                $model->save();
            }
            $this->_redirect('*/*/');
            return;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, $e->getMessage());
        }

        $this->_getSession()->setFormData($data);
        $this->_redirect('*/*/edit', ['notice_id' => $this->getRequest()->getParam('notice_id')]);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Simi_Simipushnotif::notification_manager');
    }
}

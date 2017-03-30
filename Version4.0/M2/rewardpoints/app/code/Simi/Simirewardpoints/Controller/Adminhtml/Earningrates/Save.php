<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simi\Simirewardpoints\Controller\Adminhtml\Earningrates;

use Magento\Framework\Controller\ResultFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\Adminhtml\Index
{

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $model = $this->_objectManager->create('Simi\Simirewardpoints\Model\Rate');
        $request = $this->getRequest()->getPostValue();
        if (isset($request['simirewardpoints_earningrates']) && $request['simirewardpoints_earningrates']) {
            $data = $request['simirewardpoints_earningrates'];
            if (isset($data['website_ids']) && $data['website_ids']) {
                $data['website_ids'] = implode(',', $data['website_ids']);
            }
            if (isset($data['customer_group_ids']) && $data['customer_group_ids']) {
                $data['customer_group_ids'] = implode(',', $data['customer_group_ids']);
            }
            $data['direction'] = \Simi\Simirewardpoints\Model\Rate::MONEY_TO_POINT;
            $model->setData($data);
            if (isset($data['rate_id']) && $data['rate_id']) {
                $model->setId($data['rate_id']);
            }
            try {
                $model->save();
                if ($model->getId()) {
                    $this->messageManager->addSuccess(__('Earning rate was successfully saved'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addSuccess(__('Unable to find item to save'));
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        if ($this->getRequest()->getParam('back')) {
            $this->_redirect('*/*/edit', ['id' => $model->getId()]);
            return;
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Simi_Simirewardpoints::Earning_Rates');
    }
}

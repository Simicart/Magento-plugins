<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Store;

use Magento\Framework\Controller\ResultFactory;

class NewAction extends \Simi\Simistorelocator\Controller\Adminhtml\Store {

    /**
     * Create new Store.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $resultForward->forward('edit');
    }
}

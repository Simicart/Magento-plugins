<?php

namespace Simi\Simirewardpoints\Controller\Adminhtml\Earningrates;

use Magento\Backend\App\Action\Context;
use Simi\Simirewardpoints\Model\ResourceModel\Rate\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Simi\Simirewardpoints\Model\ResourceModel\Rate\Collection;

/**
 * Class MassDelete
 */
class MassDelete extends AbstractMassAction
{

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $collectionFactory);
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(Collection $collection)
    {
        $rateDeleted = 0;
        foreach ($collection as $rate) {
            $rate->delete();
            $rateDeleted++;
        }

        if ($rateDeleted) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', $rateDeleted));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
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
